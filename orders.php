<?php
session_start();
include 'config/db.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php?msg=' . urlencode('Please log in to see your orders.') . '&tab=login');
    exit;
}

// Get this user's orders, newest first
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get the items for each order
$itemStmt = $pdo->prepare("SELECT product_name, image, price, qty FROM order_items WHERE order_id = ?");

$pageTitle = "My Orders | MyShop";
include 'header.php';
?>

<section class="cart-page">

    <h1 class="cart-title">My Orders</h1>

    <?php if (count($orders) === 0): ?>
        <div class="cart-empty" style="display:block;">
            <p>You haven't placed any orders yet.</p>
            <a href="index.php#products" class="primary-button">START SHOPPING</a>
        </div>

    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <?php
                $itemStmt->execute([$order['id']]);
                $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <div class="cart-items" style="margin-bottom:20px;">

                <h3>
                    Order #<?= (int)$order['id'] ?>
                    <small style="font-weight:400;text-transform:capitalize;">
                        &middot; <?= htmlspecialchars($order['status'] ?? 'pending') ?>
                    </small>
                    <?php if (!empty($order['created_at'])): ?>
                        <small style="font-weight:400;">
                            &middot; <?= htmlspecialchars(date('M j, Y', strtotime($order['created_at']))) ?>
                        </small>
                    <?php endif; ?>
                </h3>

                <?php foreach ($items as $item): ?>
                    <div class="cart-item">
                        <img src="assets/images/<?= htmlspecialchars(rawurlencode($item['image'])) ?>"
                             alt="<?= htmlspecialchars($item['product_name']) ?>">
                        <div class="cart-info">
                            <h3><?= htmlspecialchars($item['product_name']) ?></h3>
                            <p>$<?= number_format($item['price'], 2) ?> × <?= (int)$item['qty'] ?></p>
                        </div>
                        <strong class="cart-line-total">
                            $<?= number_format($item['price'] * $item['qty'], 2) ?>
                        </strong>
                    </div>
                <?php endforeach; ?>

                <div class="summary-row">
                    <span>Total</span>
                    <strong>$<?= number_format($order['total'], 2) ?></strong>
                </div>

            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</section>

<?php include 'footer.php'; ?>