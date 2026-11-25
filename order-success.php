<?php
session_start();
require_once __DIR__ . '/config/db.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php?msg=' . urlencode('Please log in to view your order.') . '&tab=login');
    exit;
}

$orderId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($orderId <= 0) {
    header('Location: index.php');
    exit;
}

$qAdmin = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
$qAdmin->execute([$_SESSION['user_id']]);
$isAdmin = (bool) $qAdmin->fetchColumn();

if ($isAdmin) {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? LIMIT 1");
    $stmt->execute([$orderId]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->execute([$orderId, $_SESSION['user_id']]);
}
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: index.php');
    exit;
}

$itemStmt = $pdo->prepare("SELECT product_name, price, qty FROM order_items WHERE order_id = ?");
$itemStmt->execute([$orderId]);
$orderItems = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

$payMethod = (string) ($order['payment_method'] ?? 'PayPal');
$payAmount = isset($order['amount_paid']) ? (float) $order['amount_paid'] : (float) ($order['total'] ?? 0);
$payRef    = (string) ($order['payment_reference'] ?? '');
$payStatus = (string) ($order['payment_status'] ?? 'pending_verification');
$payBadgeClass = in_array($payStatus, ['verified', 'rejected'], true) ? $payStatus : 'pending';

$pageTitle = 'Order #' . $orderId . ' | MyShop';
include 'header.php';
?>
<link rel="stylesheet" href="assets/css/payment.css?v=1">

<section class="order-status-wrap">
    <div class="order-status-card">
        <h1 class="order-status-heading">Receipt Received — Order #<?= $orderId ?></h1>
        <p class="order-status-sub">
            Your order is recorded and awaiting manual payment verification by our team.
        </p>

        <div class="order-detail-box">
            <div><strong>Order Status:</strong> <?= htmlspecialchars((string) ($order['status'] ?? 'Pending')) ?></div>
            <div>
                <strong>Payment Verification:</strong>
                <span class="order-badge-verify <?= htmlspecialchars($payBadgeClass) ?>">
                    <?= htmlspecialchars($payStatus) ?>
                </span>
            </div>
            <div><strong>Payment Method:</strong> <?= htmlspecialchars($payMethod !== '' ? $payMethod : 'N/A') ?></div>
            <div><strong>Amount Paid:</strong> $<?= number_format($payAmount, 2) ?></div>
            <div><strong>Payment Reference:</strong> <?= htmlspecialchars($payRef !== '' ? $payRef : 'N/A') ?></div>
            <?php if (!empty($order['address'])): ?>
                <div><strong>Delivery Address:</strong> <?= htmlspecialchars((string) $order['address']) ?></div>
            <?php endif; ?>
            <?php if (!empty($order['phone'])): ?>
                <div><strong>Phone:</strong> <?= htmlspecialchars((string) $order['phone']) ?></div>
            <?php endif; ?>
        </div>

        <?php if ($orderItems): ?>
            <div class="order-items-box">
                <?php foreach ($orderItems as $item): ?>
                    <div class="order-item-line">
                        <span><?= htmlspecialchars((string) $item['product_name']) ?> &times; <?= (int) $item['qty'] ?></span>
                        <span>$<?= number_format((float) $item['price'] * (int) $item['qty'], 2) ?></span>
                    </div>
                <?php endforeach; ?>
                <div class="order-total-line">
                    <span>Order Total</span>
                    <span>$<?= number_format((float) ($order['total'] ?? 0), 2) ?></span>
                </div>
            </div>
        <?php endif; ?>

        <div class="order-actions-row">
            <a href="index.php" class="btn-continue-shop">Continue Shopping</a>
        </div>
    </div>
</section>
<?php unset($_SESSION['cart'], $_SESSION['pending_order']); ?>
<script>
localStorage.removeItem("cart");
</script>
<?php include 'footer.php'; ?>