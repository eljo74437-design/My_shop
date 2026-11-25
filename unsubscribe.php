<?php
include 'config/db.php';

$token = $_GET['token'] ?? '';
$done = false;

if (preg_match('/^[a-f0-9]{32}$/', $token)) {
    $stmt = $pdo->prepare("DELETE FROM subscribers WHERE token = ?");
    $stmt->execute([$token]);
    $done = $stmt->rowCount() > 0;
}

$pageTitle = "Unsubscribe | StoreFussion";
include 'header.php';
?>

<section class="info-page">
    <div class="info-card" style="text-align:center;">
        <h1><?php echo $done ? 'You are unsubscribed' : 'Link not valid'; ?></h1>
        <p><?php echo $done ? 'We have removed your email. You will not get any more updates from us.' : 'This link may already have been used.'; ?></p>
        <p style="margin-top:20px;"><a href="index.php" class="primary-button">BACK TO THE SHOP</a></p>
    </div>
</section>

<?php include 'footer.php'; ?>