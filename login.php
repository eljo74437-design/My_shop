<?php
$pageTitle = "My Account | StoreFussion";
include 'header.php';
?>

<section class="auth-page">

    <div class="bubbles" id="bubbles"></div>

    <div class="auth-card">
        <?php if (!empty($_GET['msg'])): ?>
    <p style="background:#fdecea;color:#b3261e;padding:10px 14px;border-radius:8px;margin-bottom:12px;font-size:14px;">
        <?= htmlspecialchars($_GET['msg']) ?>
    </p>
<?php endif; ?>

        <div class="auth-tabs">
            <button type="button" class="auth-tab active" data-target="loginForm">Login</button>
            <button type="button" class="auth-tab" data-target="registerForm">Register</button>
            <span class="tab-slider"></span>
        </div>

        <form id="loginForm" class="auth-form active" action="auth.php" method="POST">
    <input type="hidden" name="action" value="login">
            <h2>Welcome Back</h2>
            <p class="auth-sub">Log in to continue shopping.</p>

            <div class="field">
                <input type="email" name="email" placeholder=" " required>
                <label>Email address</label>
            </div>

            <div class="field">
                <input type="password" name="password" placeholder=" " required>
                <label>Password</label>
                <button type="button" class="eye">👁</button>
            </div>
            <p style="text-align:right;font-size:13px;margin:-4px 0 14px;">
    <a href="forgot.php">Forgot password?</a>
</p>

            <button type="submit" class="auth-button">LOGIN</button>
        </form>

        <form id="registerForm" class="auth-form" action="auth.php" method="POST">
    <input type="hidden" name="action" value="register">
            <h2>Create Account</h2>
            <p class="auth-sub">Join us and enjoy exclusive deals.</p>

            <div class="field">
                <input type="text" name="name" placeholder=" " required>
                <label>Full name</label>
            </div>

            <div class="field">
                <input type="email" name="email" placeholder=" " required>
                <label>Email address</label>
            </div>

            <div class="field">
                <input type="password" name="password" id="regPassword" placeholder=" " required>
                <label>Password</label>
                <button type="button" class="eye">👁</button>
            </div>

            <div class="strength">
                <div class="strength-track"><div id="strengthBar"></div></div>
                <small id="strengthText">Enter a password</small>
            </div>

            <button type="submit" class="auth-button">REGISTER</button>
        </form>

    </div>

</section>

<script>
(function () {

    // Floating bubbles
    const box = document.getElementById("bubbles");
    for (let i = 0; i < 18; i++) {
        const bubble = document.createElement("span");
        bubble.className = "bubble";
        const size = 20 + Math.random() * 70;
        bubble.style.width = size + "px";
        bubble.style.height = size + "px";
        bubble.style.left = Math.random() * 100 + "%";
        bubble.style.animationDuration = (8 + Math.random() * 12) + "s";
        bubble.style.animationDelay = (Math.random() * 10) + "s";
        box.appendChild(bubble);
    }

    // Login / Register tabs
    const tabs = document.querySelectorAll(".auth-tab");
    const forms = document.querySelectorAll(".auth-form");
    const tabBar = document.querySelector(".auth-tabs");

    tabs.forEach(function (tab, index) {
        tab.addEventListener("click", function () {
            tabs.forEach(function (t) { t.classList.remove("active"); });
            forms.forEach(function (f) { f.classList.remove("active"); });
            tab.classList.add("active");
            document.getElementById(tab.dataset.target).classList.add("active");
            tabBar.classList.toggle("register", index === 1);
        });
    });

    // Show / hide password
    document.querySelectorAll(".eye").forEach(function (eye) {
        eye.addEventListener("click", function () {
            const input = eye.parentElement.querySelector("input");
            const show = input.type === "password";
            input.type = show ? "text" : "password";
            eye.textContent = show ? "🙈" : "👁";
        });
    });

    // Password strength
    const password = document.getElementById("regPassword");
    const bar = document.getElementById("strengthBar");
    const text = document.getElementById("strengthText");
    const levels = [
        { width: "0%", color: "#ccc", label: "Enter a password" },
        { width: "25%", color: "#D9534F", label: "Weak" },
        { width: "50%", color: "#E69A3C", label: "Fair" },
        { width: "75%", color: "#D9B66C", label: "Good" },
        { width: "100%", color: "#3FA66B", label: "Strong" }
    ];

    password.addEventListener("input", function () {
        const value = password.value;
        let score = 0;
        if (value.length >= 8) score++;
        if (/[A-Z]/.test(value)) score++;
        if (/[0-9]/.test(value)) score++;
        if (/[^A-Za-z0-9]/.test(value)) score++;
        if (value.length === 0) score = 0;
        if (value.length > 0 && score === 0) score = 1;

        bar.style.width = levels[score].width;
        bar.style.background = levels[score].color;
        text.textContent = levels[score].label;
    });

})();
</script>

<?php include 'footer.php'; ?>