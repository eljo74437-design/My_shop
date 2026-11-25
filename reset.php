<?php
session_start();
include 'config/db.php';

$token = $_GET['token'] ?? ($_POST['token'] ?? '');
$error = '';
$done = false;
$user = null;

if ($token !== '') {
    $stmt = $pdo->prepare("SELECT user_id FROM password_resets
                           WHERE token_hash = ? AND expires_at > NOW()");
    $stmt->execute([hash('sha256', $token)]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($user && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'The two passwords do not match.';
    } else {
        $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")
            ->execute([password_hash($password, PASSWORD_DEFAULT), $user['user_id']]);

        $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?")
            ->execute([$user['user_id']]);

        $done = true;
    }
}

$pageTitle = "Reset Password | MyShop";
include 'header.php';
?>

<section class="auth-page">
    <div class="auth-card">

        <?php if ($done): ?>
            <div class="auth-form active">
                <h2>Password Updated</h2>
                <p class="auth-sub">You can now log in with your new password.</p>
                <a href="login.php" class="auth-button" style="display:block;text-align:center;text-decoration:none;">GO TO LOGIN</a>
            </div>

        <?php elseif (!$user): ?>
            <div class="auth-form active">
                <h2>Link Expired</h2>
                <p class="auth-sub">This reset link is invalid or has expired.</p>
                <a href="forgot.php" class="auth-button" style="display:block;text-align:center;text-decoration:none;">REQUEST A NEW LINK</a>
            </div>

        <?php else: ?>
            <form class="auth-form active" action="reset.php" method="POST">
                <h2>Choose a New Password</h2>
                <p class="auth-sub">Enter your new password below.</p>

                <?php if ($error): ?>
                    <p style="background:#fdecea;color:#b3261e;padding:10px 14px;border-radius:8px;margin-bottom:12px;font-size:14px;">
                        <?= htmlspecialchars($error) ?>
                    </p>
                <?php endif; ?>

                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                <div class="field">
                    <input type="password" name="password" placeholder=" " required>
                    <label>New password</label>
                </div>

                <div class="field">
                    <input type="password" name="confirm" placeholder=" " required>
                    <label>Confirm new password</label>
                </div>

                <button type="submit" class="auth-button">UPDATE PASSWORD</button>
            </form>
        <?php endif; ?>

    </div>
</section>

<?php include 'footer.php'; ?>