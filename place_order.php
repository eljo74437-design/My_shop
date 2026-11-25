<?php
session_start();
include __DIR__ . '/config/db.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php?msg=' . urlencode('Please log in to checkout.') . '&tab=login');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cart.php');
    exit;
}

if (empty($_SESSION['pending_order'])) {
    header('Location: checkout.php');
    exit;
}

$pendingOrder = $_SESSION['pending_order'];
$phone        = trim((string)($pendingOrder['phone'] ?? ''));
$address      = trim((string)($pendingOrder['address'] ?? ''));
$lines        = $pendingOrder['items'] ?? [];
$total        = isset($pendingOrder['total']) ? (float)$pendingOrder['total'] : 0.0;

$paymentMethod = trim((string)($_POST['payment_method'] ?? 'Gift card'));
$amountPaid    = filter_var($_POST['amount_paid'] ?? 0, FILTER_VALIDATE_FLOAT);
$paymentRef    = trim((string)($_POST['payment_reference'] ?? ''));

include 'phone-codes.php';

if (!phone_is_allowed($phone)) {
    unset($_SESSION['pending_order']);
    header('Location: checkout.php?phone=bad');
    exit;
}

if ($phone === '' || $address === '' || !is_array($lines) || count($lines) === 0 || $total <= 0) {
    unset($_SESSION['pending_order']);
    header('Location: cart.php');
    exit;
}

$allowedMethods = ['Gift card'];
if (!in_array($paymentMethod, $allowedMethods, true)) {
    $paymentMethod = 'Gift card';
}

if ($amountPaid === false || $amountPaid <= 0) {
    $_SESSION['payment_error'] = 'Please enter a valid amount paid.';
    header('Location: payment.php');
    exit;
}

if ($paymentRef === '') {
    $_SESSION['payment_error'] = 'Please enter your payment reference.';
    header('Location: payment.php');
    exit;
}

if (empty($_FILES['receipt_screenshot']) || $_FILES['receipt_screenshot']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['payment_error'] = 'Please attach a clear screenshot of your payment receipt.';
    header('Location: payment.php');
    exit;
}

$tmpPath  = $_FILES['receipt_screenshot']['tmp_name'];
$fileSize = (int)($_FILES['receipt_screenshot']['size'] ?? 0);
$maxBytes = 5 * 1024 * 1024;

if ($fileSize <= 0 || $fileSize > $maxBytes) {
    $_SESSION['payment_error'] = 'Receipt image must be smaller than 5 MB.';
    header('Location: payment.php');
    exit;
}

$finfo     = new finfo(FILEINFO_MIME_TYPE);
$mimeType  = $finfo->file($tmpPath);
$mimeToExt = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
    'image/gif'  => 'gif',
];

if (!isset($mimeToExt[$mimeType])) {
    $_SESSION['payment_error'] = 'Only JPG, PNG, WEBP, or GIF images are allowed for receipts.';
    header('Location: payment.php');
    exit;
}

$uploadDir = __DIR__ . '/assets/uploads/receipts';
if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
    $_SESSION['payment_error'] = 'Unable to create receipt upload directory.';
    header('Location: payment.php');
    exit;
}

$fileName      = 'receipt_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $mimeToExt[$mimeType];
$targetPath    = $uploadDir . '/' . $fileName;
$dbReceiptPath = 'assets/uploads/receipts/' . $fileName;

if (!move_uploaded_file($tmpPath, $targetPath)) {
    $_SESSION['payment_error'] = 'Failed to save receipt screenshot.';
    header('Location: payment.php');
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO orders (
            user_id, phone, address, total, status,
            payment_method, amount_paid, payment_reference, receipt_image, payment_status
        ) VALUES (?, ?, ?, ?, 'Pending', ?, ?, ?, ?, 'pending_verification')
    ");
    $stmt->execute([
        $_SESSION['user_id'],
        $phone,
        $address,
        $total,
        $paymentMethod,
        $amountPaid,
        $paymentRef,
        $dbReceiptPath,
    ]);
    $orderId = $pdo->lastInsertId();
    $insert = $pdo->prepare("
        INSERT INTO order_items (order_id, product_name, image, price, qty)
        VALUES (?, ?, ?, ?, ?)
    ");
    foreach ($lines as $item) {
        $insert->execute([
            $orderId,
            $item['name'],
            $item['image'],
            $item['price'],
            $item['qty'],
        ]);
    }

    $pdo->commit();
    unset($_SESSION['cart']);

    $_SESSION['sent_receipts'][] = [
        'method'    => $paymentMethod,
        'amount'    => number_format($amountPaid, 2),
        'reference' => $paymentRef,
    ];
    unset($_SESSION['pending_order']);
} catch (Exception $e) {
    $pdo->rollBack();
    die('Sorry, something went wrong placing your order. Please try again.');
}

echo '<script>
  localStorage.removeItem("myshopCartItems");
  window.location.href = "order-success.php?id=' . (int)$orderId . '";
</script>';
exit;