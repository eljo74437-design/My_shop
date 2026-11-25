<?php
if (!isset($_SESSION)) { session_start(); }

$isAdminUser = false;
if (!empty($_SESSION['user_id'])) {
    include_once 'config/db.php';
    $adminCheck = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
    $adminCheck->execute([$_SESSION['user_id']]);
    $isAdminUser = (bool) $adminCheck->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($pageTitle ?? 'StoreFussion | Premium Online Shopping') ?></title>

    <!-- External CSS -->
     
    <link rel="stylesheet" href="assets/css/style.css?v=16">
       <link rel="stylesheet" href="assets/css/cart.css?v=16">
       <link rel="stylesheet" href="assets/css/header-glow.css?v=6">
<script>
(function () {
    var theme = null;
    try { theme = localStorage.getItem("myshopTheme"); } catch (e) {}
    if (!theme) theme = "dark";
    document.documentElement.setAttribute("data-theme", theme);
})();
</script>
</head>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<?php if (basename($_SERVER['PHP_SELF']) !== 'cart.php'): ?>
<a href="cart.php" class="float-cart" id="floatCart" draggable="false" style="touch-action:none;" aria-label="Open cart">
    🛒<span class="float-cart-count" id="floatCartCount">0</span>
</a>
<script>
(function () {
    var cart = document.getElementById("floatCart");
    if (!cart) return;

    var dragging = false, moved = false;
    var sx = 0, sy = 0, sl = 0, st = 0;

    function place(l, t) {
        var maxL = window.innerWidth - cart.offsetWidth - 5;
        var maxT = window.innerHeight - cart.offsetHeight - 5;
        l = Math.min(Math.max(5, l), maxL);
        t = Math.min(Math.max(5, t), maxT);
        cart.style.left = l + "px";
        cart.style.top = t + "px";
        cart.style.right = "auto";
        cart.style.bottom = "auto";
    }

    try {
        var saved = JSON.parse(localStorage.getItem("myshopCartPos"));
        if (saved) place(saved.left, saved.top);
    } catch (e) {}

    cart.addEventListener("dragstart", function (e) { e.preventDefault(); });
    cart.addEventListener("contextmenu", function (e) { e.preventDefault(); });

    cart.addEventListener("pointerdown", function (e) {
        dragging = true;
        moved = false;
        var r = cart.getBoundingClientRect();
        sx = e.clientX; sy = e.clientY; sl = r.left; st = r.top;
        try { cart.setPointerCapture(e.pointerId); } catch (err) {}
    });

    cart.addEventListener("pointermove", function (e) {
        if (!dragging) return;
        var dx = e.clientX - sx;
        var dy = e.clientY - sy;
        if (Math.abs(dx) + Math.abs(dy) > 8) moved = true;
        if (moved) place(sl + dx, st + dy);
    });

    function endDrag() {
        if (!dragging) return;
        dragging = false;
        if (moved) {
            var r = cart.getBoundingClientRect();
            try {
                localStorage.setItem("myshopCartPos", JSON.stringify({ left: r.left, top: r.top }));
            } catch (e) {}
        }
    }

    cart.addEventListener("pointerup", endDrag);
    cart.addEventListener("pointercancel", endDrag);

    cart.addEventListener("click", function (e) {
        if (moved) {
            e.preventDefault();
            moved = false;
        }
    });
})();
</script>
<?php endif; ?>
<script src="assets/js/script.js"></script>


<body class="page-<?php echo basename($_SERVER['PHP_SELF'], '.php'); ?>">

    <!-- TOP ANNOUNCEMENT BAR -->

   <div class="announcement-bar">
        <div class="annc-ticker">
            <div class="annc-track">
                <div class="annc-group">
                    <span>WELCOME TO STORE FUSSION</span><i>✦</i>
                    <span>ENJOY EXCLUSIVE DEALS ON SELECTED PRODUCTS</span><i>✦</i>
                    <span>THE PREMIUM COLLECTION</span><i>✦</i>
                </div>
                <div class="annc-group" aria-hidden="true">
                    <span>WELCOME TO STORE FUSSION</span><i>✦</i>
                    <span>ENJOY EXCLUSIVE DEALS ON SELECTED PRODUCTS</span><i>✦</i>
                    <span>THE PREMIUM COLLECTION</span><i>✦</i>
                </div>
            </div>
        </div>
    </div>


   <!-- HEADER AND NAVIGATION -->

    <?php
    $hdLoggedIn = !empty($_SESSION['user_id']);
    $hdName = '';
    if ($hdLoggedIn) {
        $nameQ = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
        $nameQ->execute([$_SESSION['user_id']]);
        $nameRow = $nameQ->fetch(PDO::FETCH_ASSOC);
        if ($nameRow) {
            $hdName = trim($nameRow['name']);
            if ($hdName === '') { $hdName = explode('@', $nameRow['email'])[0]; }
            $_SESSION['user_name'] = $hdName;
        }
    }
    $hdFirst = trim(explode(' ', $hdName)[0]);
    if ($hdFirst === '') { $hdFirst = 'Account'; }
    $hdInitial = strtoupper(mb_substr($hdFirst, 0, 1));
    ?>
    <header class="hd">
        <i class="hd-gem" aria-hidden="true"></i>

        <div class="hd-main">

            <a class="hd-logo" href="index.php" aria-label="StoreFussion home">
                <svg class="hd-logo-gem" viewBox="0 0 32 32" aria-hidden="true">
                    <polygon points="6,12 11,6 16,12" fill="#BFF2D6"/>
                    <polygon points="11,6 21,6 16,12" fill="#7FD9A8"/>
                    <polygon points="21,6 26,12 16,12" fill="#4FC08A"/>
                    <polygon points="6,12 16,12 16,27" fill="#2FA36B"/>
                    <polygon points="16,12 26,12 16,27" fill="#1F7A50"/>
                    <path class="hd-glint" d="M26 1 L27.3 4.7 L31 6 L27.3 7.3 L26 11 L24.7 7.3 L21 6 L24.7 4.7 Z" fill="#fff"/>
                </svg>
                <span class="hd-logo-text">STORE<b>FUSSION</b></span>
            </a>

            <div class="hd-search">
                <svg class="hd-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
                <input type="text" id="productSearch" placeholder="Search phones, fashion, watches..." autocomplete="off">
                <button type="button" id="searchButton" aria-label="Search">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </button>
            </div>

            <div class="hd-tools">
                <button type="button" id="themeToggle" class="hd-tool hd-theme" aria-label="Switch light or dark mode">☀</button>

                <?php if ($hdLoggedIn): ?>
                    <div class="hd-user" id="hdUser">
                        <button type="button" class="hd-tool" id="hdUserBtn" aria-haspopup="true" aria-expanded="false" aria-label="Account menu">
                            <span class="hd-avatar"><?= htmlspecialchars($hdInitial) ?></span>
                            <span class="hd-label"><?= htmlspecialchars($hdFirst) ?></span>
                        </button>
                        <div class="hd-menu" id="hdMenu">
                            <p class="hd-menu-hi">Hi, <?= htmlspecialchars($hdFirst) ?></p>
                            <a href="orders.php">My orders</a>
                            <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
                                <a href="admin.php">Admin</a>
                                <a href="admin-subscribers.php">Subscribers</a>
                                <a href="admin-logins.php">Logins</a>
                                <a href="admin-orders.php">Orders</a>
                                <?php endif; ?>
                            <a href="logout.php" onclick="return confirm('Are you sure you want to log out?');">Log out</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a class="hd-tool" href="login.php" aria-label="Sign in">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-7 8-7s8 2.6 8 7"/></svg>
                        <span class="hd-label">Sign in</span>
                    </a>
                <?php endif; ?>

                <a class="hd-tool hd-cart" href="cart.php" aria-label="Cart">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 8h14l-1.2 11.2a2 2 0 0 1-2 1.8H8.2a2 2 0 0 1-2-1.8L5 8z"/><path d="M9 8V6.5a3 3 0 0 1 6 0V8"/></svg>
                    <span class="hd-label">Cart</span>
                    <b class="hd-badge" id="hdBadge">0</b>
                    <span class="hd-sr" id="cartCounter">My Cart (0)</span>
                </a>
            </div>
            </div>

        <nav class="navigation">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="#categories">Categories</a></li>
                <li><a href="#products">Shop</a></li>
                <li><a href="#trending">Trending</a></li>
                <li><a href="#offers">Special offers</a></li>
                <li><a href="#about">About us</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </nav>

    </header>

    <script>
    (function () {
        // account menu
        var box = document.getElementById("hdUser");
        var btn = document.getElementById("hdUserBtn");
        if (box && btn) {
            function setOpen(open) {
                box.classList.toggle("open", open);
                btn.setAttribute("aria-expanded", open ? "true" : "false");
            }
            btn.addEventListener("click", function (e) {
                e.stopPropagation();
                setOpen(!box.classList.contains("open"));
            });
            document.addEventListener("click", function (e) {
                if (!box.contains(e.target)) setOpen(false);
            });
            document.addEventListener("keydown", function (e) {
                if (e.key === "Escape") setOpen(false);
            });
        }

        // cart number badge (follows the cart counter the site already updates)
        var counter = document.getElementById("cartCounter");
        var badge = document.getElementById("hdBadge");
        if (counter && badge) {
            function sync() {
                var m = String(counter.textContent).match(/\d+/);
                var n = m ? parseInt(m[0], 10) : 0;
                badge.textContent = n > 99 ? "99+" : n;
                badge.classList.toggle("on", n > 0);
            }
            new MutationObserver(sync).observe(counter, { childList: true, characterData: true, subtree: true });
            sync();
        }
    })();
    </script>
