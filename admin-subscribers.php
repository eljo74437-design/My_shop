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

function safeCell($v) {
    $v = (string) $v;
    if ($v !== '' and strpos("=+-@", $v[0]) !== false) { return "'" . $v; }
    return $v;
}

function ago($secs) {
    $secs = (int) $secs;
    if ($secs < 60) { return "just now"; }
    if ($secs < 3600) { return floor($secs / 60) . " min ago"; }
    if ($secs < 86400) { return floor($secs / 3600) . " hr ago"; }
    $days = floor($secs / 86400);
    return $days . ($days == 1 ? " day ago" : " days ago");
}

// Download everyone as a file
if (isset($_GET['export'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="subscribers.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['email', 'subscribed_at'], ',', '"', '\\');
    foreach ($pdo->query("SELECT email, subscribed_at FROM subscribers ORDER BY id DESC") as $r) {
        fputcsv($out, [safeCell($r['email']), $r['subscribed_at']], ',', '"', '\\');
    }
    fclose($out);
    exit;
}

// Remove one subscriber
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $pdo->prepare("DELETE FROM subscribers WHERE id = ?")->execute([$id]);
        }
    }
    header("Location: admin-subscribers.php");
    exit;
}

$total = (int) $pdo->query("SELECT COUNT(*) FROM subscribers")->fetchColumn();
$week = (int) $pdo->query("SELECT COUNT(*) FROM subscribers WHERE subscribed_at >= (NOW() - INTERVAL 7 DAY)")->fetchColumn();
$allEmails = $pdo->query("SELECT email FROM subscribers ORDER BY id DESC")->fetchAll(PDO::FETCH_COLUMN);
$subs = $pdo->query(
    "SELECT id, email, subscribed_at, TIMESTAMPDIFF(SECOND, subscribed_at, NOW()) AS secs_ago
     FROM subscribers ORDER BY id DESC LIMIT 500"
)->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Subscribers | Admin";
include 'header.php';
?>

<style>
.sb-page { padding: 50px 5%; min-height: 70vh; background: var(--cream); }
.sb-title { font-size: 32px; color: var(--navy); margin-bottom: 14px; }
.sb-links { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 22px; }
.sb-links button { border: none; cursor: pointer; }
.sb-stats { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; max-width: 460px; margin-bottom: 22px; }
.sb-stat { padding: 16px; text-align: center; background: var(--cream-light); border: 1px solid var(--gold); border-radius: 18px; }
.sb-stat strong { display: block; font-size: 28px; color: var(--gold); }
.sb-stat span { font-size: 12px; opacity: 0.8; }
.sb-search { width: 100%; max-width: 420px; padding: 12px 18px; margin-bottom: 14px; border: 1.5px solid #DCD2BC; border-radius: 30px; outline: none; background: #fff; color: #17263B; font-size: 16px; }
.sb-search:focus { border-color: var(--gold); }
.sb-row { display: flex; align-items: center; gap: 12px; padding: 10px 14px 10px 10px; margin-bottom: 8px; background: var(--cream-light); border: 1px solid rgba(217, 182, 108, 0.45); border-radius: 40px; }
.sb-av { width: 42px; height: 42px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border-radius: 50%; background: linear-gradient(135deg, var(--gold-light), var(--gold)); color: #041224; font-weight: bold; }
.sb-who { flex: 1; min-width: 0; }
.sb-who strong, .sb-who small { display: block; }
.sb-who strong { font-size: 14px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.sb-who small { font-size: 12px; opacity: 0.7; }
.sb-rm { padding: 8px 14px; border: none; border-radius: 20px; background: #B03A3A; color: #fff; font-size: 11px; font-weight: bold; cursor: pointer; flex-shrink: 0; }
.sb-rm.sure { background: #E53935; }
html[data-theme="dark"] .sb-title { color: #F3F6FB; }
html[data-theme="dark"] .sb-stat strong { color: var(--gold-light); }
html[data-theme="dark"] .sb-search { background: #0B1B33; color: #F3F6FB; border-color: rgba(217, 182, 108, 0.45); }
@media (max-width: 650px) {
    .sb-page { padding: 30px 5%; }
    .sb-title { font-size: 24px; }
    .sb-row { border-radius: 26px; }
}
</style>

<section class="sb-page">

    <h1 class="sb-title">Subscribers (<?php echo $total; ?>)</h1>

    <div class="sb-links">
        <a href="admin.php" class="primary-button">&larr; PRODUCTS</a>
        <a href="admin-subscribers.php?export=1" class="primary-button">DOWNLOAD CSV</a>
        <button type="button" id="sbCopy" class="primary-button">COPY ALL EMAILS</button>
    </div>

    <div class="sb-stats">
        <div class="sb-stat"><strong><?php echo $total; ?></strong><span>Subscribers</span></div>
        <div class="sb-stat"><strong><?php echo $week; ?></strong><span>New in 7 days</span></div>
    </div>

    <input type="text" id="sbSearch" class="sb-search" placeholder="Search emails...">

    <?php if (!$subs): ?>
        <p>No subscribers yet. They will appear here when someone signs up on the home page.</p>
    <?php endif; ?>

    <?php foreach ($subs as $s): ?>
        <form method="POST" class="sb-row" data-s="<?php echo htmlspecialchars(strtolower($s['email'])); ?>">
            <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">
            <input type="hidden" name="id" value="<?php echo (int) $s['id']; ?>">
            <span class="sb-av"><?php echo htmlspecialchars(strtoupper(substr($s['email'], 0, 1))); ?></span>
            <div class="sb-who">
                <strong><?php echo htmlspecialchars($s['email']); ?></strong>
                <small><?php echo date('M j, Y g:i A', strtotime($s['subscribed_at'])); ?>, <?php echo ago($s['secs_ago']); ?></small>
            </div>
            <button type="submit" class="sb-rm">REMOVE</button>
        </form>
    <?php endforeach; ?>

</section>

<script>
(function () {
    var emails = <?php echo json_encode($allEmails, JSON_HEX_TAG | JSON_HEX_AMP); ?>;

    document.getElementById("sbSearch").addEventListener("input", function () {
        var term = this.value.toLowerCase().trim();
        document.querySelectorAll(".sb-row").forEach(function (row) {
            row.style.display = row.dataset.s.indexOf(term) !== -1 ? "" : "none";
        });
    });

    var copyBtn = document.getElementById("sbCopy");
    copyBtn.addEventListener("click", function () {
        var text = emails.join(", ");
        function done() {
            copyBtn.textContent = "COPIED " + emails.length;
            setTimeout(function () { copyBtn.textContent = "COPY ALL EMAILS"; }, 2000);
        }
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(done);
        } else {
            var t = document.createElement("textarea");
            t.value = text;
            document.body.appendChild(t);
            t.select();
            document.execCommand("copy");
            document.body.removeChild(t);
            done();
        }
    });

    // Two taps to remove, so nobody is removed by accident
    document.querySelectorAll(".sb-rm").forEach(function (btn) {
        var armed = false;
        var timer;
        btn.addEventListener("click", function (e) {
            if (armed) return;
            e.preventDefault();
            armed = true;
            btn.textContent = "SURE?";
            btn.classList.add("sure");
            timer = setTimeout(function () {
                armed = false;
                btn.textContent = "REMOVE";
                btn.classList.remove("sure");
            }, 3000);
        });
    });
})();
</script>

<?php include 'footer.php'; ?>