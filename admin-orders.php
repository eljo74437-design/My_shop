<?php
session_start();
include 'config/db.php';

// Admins only
$isAdmin = false;
if (!empty($_SESSION['user_id'])) {
    $q = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
    $q->execute([$_SESSION['user_id']]);
    $isAdmin = (bool) $q->fetchColumn();
}
if (!$isAdmin) {
    header("Location: login.php");
    exit;
}

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

$statuses = ["Pending", "Shipped", "Delivered"];

$columns = $pdo->query("SHOW COLUMNS FROM orders")->fetchAll(PDO::FETCH_COLUMN);
$hasStatus = in_array("status", $columns);
$hasPaymentStatus = in_array("payment_status", $columns);

function pick($row, $keys) {
    foreach ($keys as $k) {
        if (isset($row[$k]) and trim((string) $row[$k]) !== '') {
            return $row[$k];
        }
    }
    return '';
}

$msg = "";

// Manual payment verification action (no automatic approval)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_payment_id'])) {
    $result = "error";

    if (hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        $payOrderId = (int) ($_POST['verify_payment_id'] ?? 0);
        $decision   = $_POST['payment_decision'] ?? '';

        if ($payOrderId > 0 && in_array($decision, ['verify', 'reject'], true)) {
            $newPayStatus = ($decision === 'verify') ? 'verified' : 'rejected';

            if ($hasPaymentStatus && $hasStatus) {
                if ($decision === 'verify') {
                    $upPay = $pdo->prepare("UPDATE orders SET payment_status = ?, status = 'Delivered' WHERE id = ?");
                    $upPay->execute([$newPayStatus, $payOrderId]);
                } else {
                    $upPay = $pdo->prepare("UPDATE orders SET payment_status = ? WHERE id = ?");
                    $upPay->execute([$newPayStatus, $payOrderId]);
                }
                $result = "payment_updated";
            } elseif ($hasPaymentStatus) {
                $upPay = $pdo->prepare("UPDATE orders SET payment_status = ? WHERE id = ?");
                $upPay->execute([$newPayStatus, $payOrderId]);
                $result = "payment_updated";
            }
        }
    }

    $back = $_POST['filter'] ?? '';
    if (!in_array($back, $statuses)) { $back = ''; }
    header("Location: admin-orders.php?msg=" . $result . ($back !== '' ? "&status=" . $back : ""));
    exit;
}

// Change an order's shipping/fulfillment status
if ($_SERVER['REQUEST_METHOD'] === 'POST' and $hasStatus) {
    $result = "error";

    if (hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        $id = (int) ($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';
        if ($id > 0 and in_array($status, $statuses)) {
            $up = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $up->execute([$status, $id]);
            $result = "updated";
        }
    }

    $back = $_POST['filter'] ?? '';
    if (!in_array($back, $statuses)) { $back = ''; }
    header("Location: admin-orders.php?msg=" . $result . ($back !== '' ? "&status=" . $back : ""));
    exit;
}

$filter = $_GET['status'] ?? '';
if (!in_array($filter, $statuses)) { $filter = ''; }

// Counts for the tabs
$all = (int) $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$counts = ["Pending" => 0, "Shipped" => 0, "Delivered" => 0];
if ($hasStatus) {
    foreach ($pdo->query("SELECT status, COUNT(*) AS n FROM orders GROUP BY status") as $r) {
        foreach ($statuses as $s) {
            if (strtolower(trim((string) $r['status'])) === strtolower($s)) {
                $counts[$s] += (int) $r['n'];
            }
        }
    }
}

// The orders
$sql = "SELECT o.*, u.name AS account_name, u.email AS account_email
        FROM orders o LEFT JOIN users u ON u.id = o.user_id";
$params = [];
if ($filter !== '' and $hasStatus) {
    $sql .= " WHERE LOWER(o.status) = LOWER(?)";
    $params[] = $filter;
}
$sql .= " ORDER BY o.id DESC LIMIT 200";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
// The items inside each order
$items = [];
if ($orders) {
    $ids = array_column($orders, 'id');
    $marks = implode(',', array_fill(0, count($ids), '?'));
    $it = $pdo->prepare("SELECT order_id, product_name, price, qty FROM order_items WHERE order_id IN ($marks)");
    $it->execute($ids);
    foreach ($it->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $items[$row['order_id']][] = $row;
    }
}

if (($_GET['msg'] ?? '') === 'updated') { $msg = "Order status updated."; }
if (($_GET['msg'] ?? '') === 'payment_updated') { $msg = "Payment verification status updated."; }
if (($_GET['msg'] ?? '') === 'error') { $msg = "Something went wrong. Please try again."; }

$pageTitle = "Orders | Admin";
include 'header.php';
?>

<link rel="stylesheet" href="assets/css/payment.css?v=2">

<section class="ord-page">

    <h1 class="ord-title">Orders (<?php echo $all; ?>)</h1>

    <a href="admin.php" class="primary-button">&larr; PRODUCTS</a>

    <?php if ($msg !== ""): ?>
        <div class="ord-msg"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <?php if (!$hasStatus): ?>
        <div class="ord-warn">
            Your orders table has no status column yet. Run this once in HeidiSQL, then reload this page:
            <code class="ord-warn-code">ALTER TABLE orders ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'Pending';</code>
        </div>
    <?php else: ?>
        <div class="ord-tabs">
            <a href="admin-orders.php" class="<?php echo $filter === '' ? 'on' : ''; ?>">All (<?php echo $all; ?>)</a>
            <?php foreach ($statuses as $s): ?>
                <a href="admin-orders.php?status=<?php echo $s; ?>" class="<?php echo $filter === $s ? 'on' : ''; ?>"><?php echo $s; ?> (<?php echo $counts[$s]; ?>)</a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!$orders): ?>
        <p>No orders here yet.</p>
    <?php endif; ?>

    <?php foreach ($orders as $o):
        $oid = (int) $o['id'];
        $lines = $items[$oid] ?? [];

        $sum = 0;
        foreach ($lines as $l) { $sum += $l['price'] * $l['qty']; }
        $total = isset($o['total']) ? (float) $o['total'] : $sum;

        $customer = pick($o, ['full_name', 'name', 'customer_name']);
        if ($customer === '') { $customer = $o['account_name'] ?? 'Customer'; }
        $address = trim(pick($o, ['address']) . ' ' . pick($o, ['city']));
        $note = pick($o, ['note', 'notes']);
        $date = pick($o, ['created_at', 'order_date', 'date']);
        $phone = pick($o, ['phone']);

        $payMethod = pick($o, ['payment_method']);
        $payAmount = isset($o['amount_paid']) ? (float) $o['amount_paid'] : 0.0;
        $payRef    = pick($o, ['payment_reference']);
        $receipt   = pick($o, ['receipt_image']);
        $payStatus = pick($o, ['payment_status']);

        $current = $hasStatus ? (string) $o['status'] : '';
        $matched = '';
        foreach ($statuses as $s) {
            if (strtolower(trim($current)) === strtolower($s)) { $matched = $s; }
        }
        $badgeClass = $matched !== '' ? strtolower($matched) : 'other';
        $badgeText = $matched !== '' ? $matched : $current;
    ?>
        <div class="ord-card">
            <div class="ord-top">
                <div>
                    <div class="ord-id">Order #<?php echo $oid; ?></div>
                    <div class="ord-date"><?php echo htmlspecialchars($date); ?></div>
                </div>
                <?php if ($hasStatus): ?>
                    <span class="ord-badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($badgeText); ?></span>
                <?php endif; ?>
            </div>

            <div class="ord-info">
                <strong><?php echo htmlspecialchars($customer); ?></strong>
                <?php if (!empty($o['account_email'])): ?>
                    (<?php echo htmlspecialchars($o['account_email']); ?>)
                <?php endif; ?><br>
                <?php if ($phone !== ''): ?>
                    Phone: <a href="tel:<?php echo htmlspecialchars($phone); ?>"><?php echo htmlspecialchars($phone); ?></a><br>
                <?php endif; ?>
                <?php if ($address !== ''): ?>
                    Address: <?php echo htmlspecialchars($address); ?><br>
                <?php endif; ?>
                <?php if ($note !== ''): ?>
                    Note: <?php echo htmlspecialchars($note); ?>
                <?php endif; ?>
            </div>

            <div class="pay-meta-box">
                <strong>Payment Proof:</strong><br>
                Method: <strong><?php echo htmlspecialchars($payMethod !== '' ? $payMethod : 'N/A'); ?></strong><br>
                Amount Paid: <strong>$<?php echo number_format($payAmount, 2); ?></strong><br>
                Reference: <strong><?php echo htmlspecialchars($payRef !== '' ? $payRef : 'N/A'); ?></strong><br>
                Verification Status: <strong><?php echo htmlspecialchars($payStatus !== '' ? $payStatus : 'pending_verification'); ?></strong>
                <?php if ($receipt !== ''): ?><br>
                    <a class="pay-receipt-link" href="<?php echo htmlspecialchars($receipt); ?>" target="_blank" rel="noopener">View Receipt Screenshot</a>
                <?php endif; ?>

                <?php if ($hasPaymentStatus && $payStatus === 'pending_verification'): ?>
                    <form method="POST" class="pay-verify-form">
                        <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">
                        <input type="hidden" name="verify_payment_id" value="<?php echo $oid; ?>">
                        <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
                        <button type="submit" name="payment_decision" value="verify" class="btn-verify-ok">Verify &amp; Approve</button>
                        <button type="submit" name="payment_decision" value="reject" class="btn-verify-no">Reject Payment</button>
                    </form>
                <?php endif; ?>
            </div>

            <div class="ord-items">
                <?php foreach ($lines as $l): ?>
                    <div class="ord-item-row">
                        <span><?php echo htmlspecialchars($l['product_name']); ?> &times; <?php echo (int) $l['qty']; ?></span>
                        <span>$<?php echo number_format($l['price'] * $l['qty'], 2); ?></span>
                    </div>
                <?php endforeach; ?>
                <div class="ord-total">
                    <span>Total</span>
                    <span>$<?php echo number_format($total, 2); ?></span>
                </div>
            </div>

            <?php if ($hasStatus): ?>
                <form method="POST" class="ord-form">
                    <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">
                    <input type="hidden" name="id" value="<?php echo $oid; ?>">
                    <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
                    <select name="status">
                        <?php if ($matched === ''): ?>
                            <option value="" disabled selected>Current: <?php echo htmlspecialchars($current); ?></option>
                        <?php endif; ?>
                        <?php foreach ($statuses as $s): ?>
                            <option value="<?php echo $s; ?>" <?php echo $s === $matched ? 'selected' : ''; ?>><?php echo $s; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit">UPDATE</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

</section>

<?php include 'footer.php'; ?>