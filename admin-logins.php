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

function ago($secs) {
    $secs = (int) $secs;
    if ($secs < 60) { return "just now"; }
    if ($secs < 3600) { return floor($secs / 60) . " min ago"; }
    if ($secs < 86400) { return floor($secs / 3600) . " hr ago"; }
    $days = floor($secs / 86400);
    return $days . ($days == 1 ? " day ago" : " days ago");
}

$today = $pdo->query("SELECT COUNT(*) AS n, COUNT(DISTINCT user_id) AS people FROM login_log WHERE logged_in_at >= CURDATE()")->fetch(PDO::FETCH_ASSOC);
$totalUsers = (int) $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalLogins = (int) $pdo->query("SELECT COUNT(*) FROM login_log")->fetchColumn();

$recent = $pdo->query(
    "SELECT l.logged_in_at, TIMESTAMPDIFF(SECOND, l.logged_in_at, NOW()) AS secs_ago, u.name, u.email
     FROM login_log l JOIN users u ON u.id = l.user_id
     ORDER BY l.id DESC LIMIT 200"
)->fetchAll(PDO::FETCH_ASSOC);

$people = $pdo->query(
    "SELECT u.name, u.email, MAX(l.logged_in_at) AS last_login,
            TIMESTAMPDIFF(SECOND, MAX(l.logged_in_at), NOW()) AS secs_ago,
            COUNT(l.id) AS total
     FROM users u LEFT JOIN login_log l ON l.user_id = u.id
     GROUP BY u.id, u.name, u.email
     ORDER BY MAX(l.logged_in_at) IS NULL, MAX(l.logged_in_at) DESC
     LIMIT 200"
)->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Logins | Admin";
include 'header.php';
?>

<style>
.lg-page { padding: 50px 5%; min-height: 70vh; background: var(--cream); }
.lg-title { font-size: 32px; color: var(--navy); margin-bottom: 14px; }
.lg-links { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 22px; }
.lg-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 24px; }
.lg-stat { padding: 16px; text-align: center; background: var(--cream-light); border: 1px solid var(--gold); border-radius: 18px; }
.lg-stat strong { display: block; font-size: 28px; color: var(--gold); }
.lg-stat span { font-size: 12px; opacity: 0.8; }
.lg-search { width: 100%; max-width: 420px; padding: 12px 18px; margin-bottom: 8px; border: 1.5px solid #DCD2BC; border-radius: 30px; outline: none; background: #fff; color: #17263B; font-size: 16px; }
.lg-search:focus { border-color: var(--gold); }
.lg-sub { margin: 26px 0 12px; font-size: 20px; }
.lg-row { display: flex; align-items: center; gap: 12px; padding: 10px 18px 10px 10px; margin-bottom: 8px; background: var(--cream-light); border: 1px solid rgba(217, 182, 108, 0.45); border-radius: 40px; }
.lg-av { width: 42px; height: 42px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border-radius: 50%; background: linear-gradient(135deg, var(--gold-light), var(--gold)); color: #041224; font-weight: bold; }
.lg-who { flex: 1; min-width: 0; }
.lg-who strong, .lg-who small, .lg-when strong, .lg-when small { display: block; }
.lg-who strong { font-size: 14px; }
.lg-who small { font-size: 12px; opacity: 0.7; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.lg-when { text-align: right; flex-shrink: 0; }
.lg-when strong { font-size: 13px; }
.lg-when small { font-size: 11px; opacity: 0.7; }
.lg-count { padding: 4px 10px; border-radius: 20px; background: rgba(217, 182, 108, 0.25); font-size: 12px; font-weight: bold; flex-shrink: 0; }
html[data-theme="dark"] .lg-title { color: #F3F6FB; }
html[data-theme="dark"] .lg-stat strong { color: var(--gold-light); }
html[data-theme="dark"] .lg-search { background: #0B1B33; color: #F3F6FB; border-color: rgba(217, 182, 108, 0.45); }
@media (max-width: 650px) {
    .lg-page { padding: 30px 5%; }
    .lg-title { font-size: 24px; }
    .lg-stats { grid-template-columns: repeat(2, 1fr); }
    .lg-row { padding-right: 12px; border-radius: 26px; }
}
</style>

<section class="lg-page">

    <h1 class="lg-title">Login activity</h1>

    <div class="lg-links">
        <a href="admin.php" class="primary-button">&larr; PRODUCTS</a>
        <a href="admin-orders.php" class="primary-button">ORDERS</a>
    </div>

    <div class="lg-stats">
        <div class="lg-stat"><strong><?php echo (int) $today['n']; ?></strong><span>Logins today</span></div>
        <div class="lg-stat"><strong><?php echo (int) $today['people']; ?></strong><span>People today</span></div>
        <div class="lg-stat"><strong><?php echo $totalUsers; ?></strong><span>Accounts</span></div>
        <div class="lg-stat"><strong><?php echo $totalLogins; ?></strong><span>Logins in total</span></div>
    </div>

    <input type="text" id="lgSearch" class="lg-search" placeholder="Search by name or email...">

    <h2 class="lg-sub">Recent logins</h2>

    <?php if (!$recent): ?>
        <p>No logins recorded yet. They will appear here the next time someone logs in.</p>
    <?php endif; ?>

    <?php foreach ($recent as $r): ?>
        <div class="lg-row" data-s="<?php echo htmlspecialchars(strtolower($r['name'] . ' ' . $r['email'])); ?>">
            <span class="lg-av"><?php echo htmlspecialchars(strtoupper(mb_substr($r['name'], 0, 1))); ?></span>
            <div class="lg-who">
                <strong><?php echo htmlspecialchars($r['name']); ?></strong>
                <small><?php echo htmlspecialchars($r['email']); ?></small>
            </div>
            <div class="lg-when">
                <strong><?php echo date('M j, Y g:i A', strtotime($r['logged_in_at'])); ?></strong>
                <small><?php echo ago($r['secs_ago']); ?></small>
            </div>
        </div>
    <?php endforeach; ?>

    <h2 class="lg-sub">All accounts</h2>

    <?php foreach ($people as $p): ?>
        <div class="lg-row" data-s="<?php echo htmlspecialchars(strtolower($p['name'] . ' ' . $p['email'])); ?>">
            <span class="lg-av"><?php echo htmlspecialchars(strtoupper(mb_substr($p['name'], 0, 1))); ?></span>
            <div class="lg-who">
                <strong><?php echo htmlspecialchars($p['name']); ?></strong>
                <small><?php echo htmlspecialchars($p['email']); ?></small>
            </div>
            <span class="lg-count"><?php echo (int) $p['total']; ?> logins</span>
            <div class="lg-when">
                <?php if ($p['last_login']): ?>
                    <strong><?php echo date('M j, Y g:i A', strtotime($p['last_login'])); ?></strong>
                    <small>last login, <?php echo ago($p['secs_ago']); ?></small>
                <?php else: ?>
                    <strong>No login recorded yet</strong>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>

</section>

<script>
document.getElementById("lgSearch").addEventListener("input", function () {
    var term = this.value.toLowerCase().trim();
    document.querySelectorAll(".lg-row").forEach(function (row) {
        row.style.display = row.dataset.s.indexOf(term) !== -1 ? "" : "none";
    });
});
</script>

<?php include 'footer.php'; ?>