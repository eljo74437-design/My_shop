<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: login.php?msg=' . urlencode('Please log in to checkout.') . '&tab=login');
    exit;
}

include 'phone-codes.php';

$pageTitle = "Checkout | MyShop";
include 'header.php';
?>

<section class="cart-page">

    <h1 class="cart-title">Checkout</h1>

    <div id="checkoutEmpty" class="cart-empty" style="display:none;">
        <p>Your cart is empty.</p>
        <a href="index.php#products" class="primary-button">START SHOPPING</a>
    </div>

    <div id="checkoutContent" class="cart-layout">

        <form id="checkoutForm" action="payment.php" method="POST" class="cart-items">
            <h3>Delivery details</h3>

            <?php if (($_GET['phone'] ?? '') === 'bad'): ?>
                <p class="phone-msg bad" style="margin-bottom:14px;">That phone number can't be used. Please choose a country code from Europe, South America or Asia and enter your number again.</p>
            <?php endif; ?>

            <div class="addr-box phone-box" id="phoneBox">
                <label for="phoneCode">Phone country code</label>
                <select id="phoneCode" required>
                    <option value="">Choose your country code</option>
                    <?php foreach ($PHONE_CODES as $region => $list): ?>
                        <optgroup label="<?= htmlspecialchars($region) ?>">
                            <?php foreach ($list as $country => $code): ?>
                                <option value="<?= $code ?>"><?= htmlspecialchars($country) ?> (+<?= $code ?>)</option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>

                <label for="phoneNum">Phone number</label>
                <input type="tel" id="phoneNum" inputmode="tel" autocomplete="tel-national" placeholder="e.g. 7911 123456" maxlength="22" required>
                <small class="phone-msg" id="phoneMsg">We deliver to Europe, South America and Asia.</small>
                <input type="hidden" name="phone" id="phoneFull">
            </div>

            <div class="field">
                <input type="text" name="address" placeholder=" " required>
                <label>Delivery address</label>
            </div>

            <input type="hidden" name="cart" id="cartData">

            <button type="submit" class="primary-button" style="border:none;cursor:pointer;">PLACE ORDER</button>
        </form>

        <aside class="cart-summary">
            <h3>Order Summary</h3>
            <div id="summaryItems"></div>
            <div class="summary-row">
                <span>Total</span>
                <strong id="summaryTotal">$0.00</strong>
            </div>
        </aside>

    </div>

</section>

<style>
.phone-msg { display: block; margin-top: 8px; font-size: 13px; color: #6B7A90; line-height: 1.4; }
html[data-theme="dark"] .phone-msg { color: #9FB0C8; }
.phone-box.ok .phone-msg { color: #1F9D5C; font-weight: 600; }
.phone-box.bad .phone-msg, .phone-msg.bad { color: #E5484D; font-weight: 600; }
.phone-box.ok #phoneNum, .phone-box.ok #phoneCode { border-color: #1F9D5C !important; }
.phone-box.bad #phoneNum { border-color: #E5484D !important; box-shadow: 0 0 0 3px rgba(229, 72, 77, 0.2) !important; }
</style>

<script>
(function () {
    var CODES = <?= json_encode(array_values(array_unique(array_merge(...array_values($PHONE_CODES))))) ?>;
    var BY_COUNTRY = <?= json_encode(array_merge(...array_values($PHONE_CODES))) ?>;

    var box  = document.getElementById("phoneBox");
    var sel  = document.getElementById("phoneCode");
    var num  = document.getElementById("phoneNum");
    var full = document.getElementById("phoneFull");
    var msg  = document.getElementById("phoneMsg");
    if (!box) return;
    if (!sel) return;
    if (!num) return;

    var DEFAULT_TEXT = "We deliver to Europe, South America and Asia.";
    var NOT_ALLOWED  = "Not allowed. We only deliver to Europe, South America and Asia, so this country code can't be used.";
    function show(state, text) {
        box.classList.toggle("ok", state === "ok");
        var isBad = (state === "bad");
        if (state === "need") { isBad = true; }
        box.classList.toggle("bad", isBad);
        msg.textContent = text ? text : DEFAULT_TEXT;
        if (isBad) {
            num.setCustomValidity(text ? text : "Check your phone number.");
        } else {
            num.setCustomValidity("");
        }
    }

    function update() {
        var raw = num.value.trim();
        full.value = "";

        // Someone typed the code themselves, like +44 7911... or 0044 7911...
        var typedCode = (raw.indexOf("+") === 0);
        if (raw.indexOf("00") === 0) { typedCode = true; }
        if (typedCode) {
            var digits = raw.replace(/^\+/, "").replace(/^00/, "").replace(/\D/g, "");
            var match = null;
            for (var L = 1; L <= 3 && !match; L++) {
                var c = digits.slice(0, L);
                if (CODES.indexOf(c) !== -1) match = c;
            }
            if (match) {
                sel.value = match;
                num.value = digits.slice(match.length);
                raw = num.value;
            } else if (digits.length > 0) {
                var possible = CODES.some(function (c) { return c.indexOf(digits) === 0; });
                if (!possible) { show("bad", NOT_ALLOWED); return; }
                show("wait", "Keep typing the country code...");
                num.setCustomValidity("Finish the country code, or choose it from the list.");
                return;
            } else {
                show("wait");
                return;
            }
        }

        if (!sel.value) {
            if (raw) show("need", "Choose your country code from the list first.");
            else show("empty");
            return;
        }

        var local = raw.replace(/\D/g, "").replace(/^0+/, "");
        if (!local) { show("empty"); return; }

        var badLength = (local.length < 6);
        if (local.length > 12) { badLength = true; }
        if (badLength) {
            show("bad", "Enter a valid phone number for +" + sel.value + " (6 to 12 digits, without the code).");
            return;
        }

        full.value = "+" + sel.value + local;
        show("ok", "\u2713 +" + sel.value + " " + local + " is accepted.");
    }

    sel.addEventListener("change", update);
    num.addEventListener("input", update);
    num.addEventListener("blur", update);

    // When the delivery country is picked, suggest the matching phone code
    document.addEventListener("change", function (e) {
        if (e.target && e.target.id === "addrCountry" && !sel.value && BY_COUNTRY[e.target.value]) {
            sel.value = BY_COUNTRY[e.target.value];
            update();
        }
    });

    document.getElementById("checkoutForm").addEventListener("submit", function (e) {
        update();
        if (!full.value) {
            e.preventDefault();
            num.reportValidity();
        }
    }, true);

    update();
})();
</script>

<script>
(function () {
    var cart = [];
    try { cart = JSON.parse(localStorage.getItem("myshopCartItems")); } catch (e) {}
    if (!Array.isArray(cart)) cart = [];

    if (cart.length === 0) {
        document.getElementById("checkoutEmpty").style.display = "block";
        document.getElementById("checkoutContent").style.display = "none";
        return;
    }

    var box = document.getElementById("summaryItems");
    var total = 0;

    cart.forEach(function (item) {
        total += item.price * item.qty;
        var row = document.createElement("div");
        row.className = "summary-row";
        var left = document.createElement("span");
        left.textContent = item.name + " × " + item.qty;
        var right = document.createElement("span");
        right.textContent = "$" + (item.price * item.qty).toFixed(2);
        row.appendChild(left);
        row.appendChild(right);
        box.appendChild(row);
    });

    document.getElementById("summaryTotal").textContent = "$" + total.toFixed(2);
    document.getElementById("checkoutForm").addEventListener("submit", function () {
        document.getElementById("cartData").value = JSON.stringify(cart);
    });
})();
</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script src="assets/js/address.js?v=3"></script>
<script src="assets/js/address-map.js?v=2"></script>
<?php include 'footer.php'; ?>