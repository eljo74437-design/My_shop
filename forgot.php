<?php
session_start();
include 'config/db.php';

$devMode = true; // set to false once real email sending is added
$resetLink = '';
$sent = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $sent = true;

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $token = bin2hex(random_bytes(32));

        $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?")
            ->execute([$user['id']]);

        $pdo->prepare("INSERT INTO password_resets (user_id, token_hash, expires_at)
                       VALUES (?, ?, NOW() + INTERVAL 1 HOUR)")
            ->execute([$user['id'], hash('sha256', $token)]);

        $resetLink = 'http://localhost/myshop/reset.php?token=' . $token;
    }
}

$pageTitle = "Forgot Password | MyShop";
include 'header.php';
?>

<section class="auth-page">
    <div class="auth-card">

        <form class="auth-form active" action="forgot.php" method="POST">
            <h2>Forgot Password</h2>
            <p class="auth-sub">Enter your email and we'll help you reset it.</p>

            <?php if ($sent): ?>
                <p style="background:#e8f5e9;color:#2e7d32;padding:10px 14px;border-radius:8px;margin-bottom:12px;font-size:14px;">
                    If that email has an account, a reset link has been created.
                </p>
                <?php if ($devMode && $resetLink): ?>
                    <p style="font-size:13px;word-break:break-all;margin-bottom:12px;">
                        Test link: <a href="<?= htmlspecialchars($resetLink) ?>">Reset my password</a>
                    </p>
                <?php endif; ?>
            <?php endif; ?>

            <div class="field">
                <input type="email" name="email" placeholder=" " required>
                <label>Email address</label>
            </div>

            <button type="submit" class="auth-button">SEND RESET LINK</button>
            <p style="text-align:center;font-size:13px;margin-top:14px;">
                <a href="login.php">Back to login</a>
            </p>
        </form>

    </div>
</section>

<?php include 'footer.php'; ?>