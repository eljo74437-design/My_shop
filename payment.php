<?php
session_start();
require_once __DIR__ . '/config/db.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php?msg=' . urlencode('Please log in to continue.') . '&tab=login');
    exit;
}

// Capture checkout form data and compute cart total server-side before creating an order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['phone'], $_POST['address'], $_POST['cart'])) {
    $postPhone   = trim($_POST['phone']);
    $postAddress = trim($_POST['address']);
    $postCart    = json_decode($_POST['cart'], true);

    include_once __DIR__ . '/phone-codes.php';
    if (!phone_is_allowed($postPhone)) {
        header('Location: checkout.php?phone=bad');
        exit;
    }
    if ($postPhone === '' || $postAddress === '' || !is_array($postCart) || count($postCart) === 0) {
        header('Location: cart.php');
        exit;
    }

    $lookup = $pdo->prepare("SELECT name, price, image FROM products WHERE name = ? AND image = ? LIMIT 1");
    $serverTotal = 0.0;
    $cleanItems  = [];

    foreach ($postCart as $item) {
        $qty = (int)($item['qty'] ?? 0);
        if ($qty < 1 || $qty > 99) {
            continue;
        }
        $lookup->execute([(string)($item['name'] ?? ''), (string)($item['image'] ?? '')]);
        $prod = $lookup->fetch(PDO::FETCH_ASSOC);
        if (!$prod) {
            continue;
        }
        $cleanItems[] = [
            'name'  => $prod['name'],
            'image' => $prod['image'],
            'price' => (float)$prod['price'],
            'qty'   => $qty,
        ];
        $serverTotal += (float)$prod['price'] * $qty;
    }

    if (count($cleanItems) === 0) {
        header('Location: cart.php');
        exit;
    }

    $_SESSION['pending_order'] = [
        'phone'   => $postPhone,
        'address' => $postAddress,
        'items'   => $cleanItems,
        'total'   => $serverTotal,
    ];

    header('Location: payment.php');
    exit;
}

if (empty($_SESSION['pending_order'])) {
    header('Location: checkout.php');
    exit;
}

$pendingOrder = $_SESSION['pending_order'];
$orderTotal   = isset($pendingOrder['total']) ? (float)$pendingOrder['total'] : 0.00;

// Replace $paypalEmail with your real PayPal address before going live
$paypalEmail      = 'payments@example.com';
$btcWalletAddress = 'Bc1q4s8ueef6rghmw94v2drz9pcerrvtxkstyugxdr';

$sentReceipts = $_SESSION['sent_receipts'] ?? [];
$errorMsg     = $_SESSION['payment_error'] ?? '';
unset($_SESSION['payment_error']);

$pageTitle = 'Payment | MyShop';
include 'header.php';
?>
<link rel="stylesheet" href="assets/css/payment.css?v=1">

<section class="pay-wrap">
    <?php if ($errorMsg !== ''): ?>
        <div class="pay-err"><?= htmlspecialchars($errorMsg) ?></div>
    <?php endif; ?>

    <div class="pay-card">
        <p class="pay-intro">
            Choose a payment method. Tap PayPal to open it. The payment email is copied for you.
        </p>
        <div id="payToast" class="pay-toast"></div>

        <div class="method-list">
           
            <button type="button" class="method-tile" data-method="Gift card" onclick="handleGiftCard()">
                <span class="method-badge badge-gc">G</span>
                <span class="method-text">
                    <span class="method-title">Gift card</span>
                    <span class="method-sub">Buy online or offline, then upload receipt</span>
                </span>
            </button>
            
        </div>

        <div id="gcBox" class="method-detail">
            <strong>Gift Card (Online or Offline):</strong>
            Get a gift card from any online store or offline shop, then attach a clear photo or screenshot of the card/receipt below. Orders stay pending until manually verified in admin.
        </div>
    </div>

    <div class="pay-card">
        <form id="receiptForm" action="place_order.php" method="POST" enctype="multipart/form-data">
            <div class="proof-kicker">PROOF OF PAYMENT</div>
            <h1 class="proof-heading">Send your payment screenshot</h1>
            <p class="proof-sub">
                After paying, screenshot the receipt, add it below and press Send. Make sure the amount, date and reference are clear.
            </p>

            <label id="dropZone" class="drop-zone" for="receiptInput">
                <span id="dropLabel">Tap to choose your receipt screenshot, or drop it here</span>
                <input type="file" id="receiptInput" name="receipt_screenshot" class="drop-input" accept="image/*" required>
                <img id="receiptPreview" class="preview-thumb" alt="Receipt preview">
            </label>

            <input
                type="number"
                step="0.01"
                min="0.01"
                name="amount_paid"
                class="pay-input"
                placeholder="Amount you paid (USD)"
                value="<?= $orderTotal > 0 ? htmlspecialchars(number_format($orderTotal, 2, '.', '')) : '' ?>"
                required
            >

           <input type="hidden" name="payment_method" value="Gift card">

            <input
                type="text"
                id="paymentRefInput"
                name="payment_reference"
                class="pay-input"
                placeholder="Payment reference"
                required
            >
            <p class="ref-hint">
                Enter the card number from your payment, with a clear picture of your receipt.
            </p>

            <div class="pay-actions">
                <button type="submit" class="btn-send-receipt">Send receipt</button>
                <button type="button" class="btn-share-receipt" onclick="shareReceiptSummary()">Share receipt...</button>
            </div>

            <h2 class="sent-title">Your sent receipts</h2>
            <?php if (empty($sentReceipts)): ?>
                <p class="sent-empty">No receipts sent yet.</p>
            <?php else: ?>
                <ul class="sent-list">
                    <?php foreach ($sentReceipts as $r): ?>
                        <li>
                            <?= htmlspecialchars($r['method']) ?> &mdash; $<?= htmlspecialchars($r['amount']) ?> (Ref: <?= htmlspecialchars($r['reference']) ?>)
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </form>
    </div>
</section>

<script>


function showToast(msg) {
    const toast = document.getElementById("payToast");
    toast.textContent = msg;
    toast.classList.add("show");
}

function selectMethodUI(methodName) {
    document.querySelectorAll(".method-tile").forEach((btn) => {
        btn.classList.toggle("active", btn.dataset.method === methodName);
    });
   
    document.getElementById("gcBox").classList.toggle("open", methodName === "Gift card");
}

async function copyText(text, successMessage) {
    try {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(text);
        } else {
            const ta = document.createElement("textarea");
            ta.value = text;
            ta.className = "drop-input";
            document.body.appendChild(ta);
            ta.select();
            document.execCommand("copy");
            document.body.removeChild(ta);
        }
    } catch (_) {}
    showToast(successMessage);
}

}

function handleGiftCard() {
    selectMethodUI("Gift card");
    showToast("Gift card selected: buy online or offline, then attach your receipt below.");
}


}

document.getElementById("paymentMethodSelect").addEventListener("change", (e) => {
    const val = e.target.value;
    selectMethodUI(val);
    if (val === "PayPal") {
        copyText(PAYPAL_EMAIL, "PayPal email (" + PAYPAL_EMAIL + ") copied.");
    } else if (val === "Bitcoin") {
        copyText(BTC_ADDRESS, "Bitcoin wallet address (" + BTC_ADDRESS + ") copied.");
    }
});

const dropZone = document.getElementById("dropZone");
const receiptInput = document.getElementById("receiptInput");
const receiptPreview = document.getElementById("receiptPreview");
const dropLabel = document.getElementById("dropLabel");

function renderFilePreview(file) {
    if (!file) return;
    dropLabel.textContent = "Selected: " + file.name;
    const reader = new FileReader();
    reader.onload = (ev) => {
        receiptPreview.src = ev.target.result;
        receiptPreview.classList.add("show");
    };
    reader.readAsDataURL(file);
}

receiptInput.addEventListener("change", () => {
    if (receiptInput.files && receiptInput.files[0]) {
        renderFilePreview(receiptInput.files[0]);
    }
});

["dragenter", "dragover"].forEach((evtName) => {
    dropZone.addEventListener(evtName, (e) => {
        e.preventDefault();
        dropZone.classList.add("dragover");
    });
});

["dragleave", "drop"].forEach((evtName) => {
    dropZone.addEventListener(evtName, (e) => {
        e.preventDefault();
        dropZone.classList.remove("dragover");
    });
});

dropZone.addEventListener("drop", (e) => {
    const files = e.dataTransfer.files;
    if (files && files.length > 0) {
        receiptInput.files = files;
        renderFilePreview(files[0]);
    }
});
async function shareReceiptSummary() {
    const method = document.getElementById("paymentMethodSelect").value;
    const ref = document.getElementById("paymentRefInput").value.trim() || "N/A";
    const amount = document.querySelector('input[name="amount_paid"]').value || "0.00";
    const text = "Payment Proof - Method: " + method + ", Amount: $" + amount + " USD, Ref: " + ref;

    if (navigator.share) {
        try {
            await navigator.share({ title: "Payment Receipt", text });
            return;
        } catch (_) {}
    }
    copyText(text, "Receipt summary copied.");
}
</script>

<?php include 'footer.php'; ?>