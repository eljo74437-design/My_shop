<?php
/* ============================================================
   CUSTOMER CARE WIDGET  (shown on every page via footer.php)
   ------------------------------------------------------------
   1. Put YOUR email below. The "Email us" button and complaints
      will go to this address.
   2. To add/change questions and automatic replies, edit the
      $faq list below.
   ============================================================ */

$careEmail = "support@storefussion0@gmail.com";   // <-- CHANGE THIS to your email
$careName  = "MyShop Care";
$careHello = "Hi there! 👋 Welcome to " . "MyShop" . ". How can we help you today? Pick a question below or type your own.";

/*  [ button label, [keywords that trigger it], reply ]  */
$faq = [
    ["How do I place an order?", ["order", "buy", "purchase", "how to"],
        "Browse the shop and tap ADD + on anything you like. Open your cart, check your items and tap Checkout. Log in or create an account, enter your delivery details and place your order."],
    ["Delivery", ["deliver", "shipping", "ship", "arrive", "how long", "country", "location"],
        "We deliver across Europe, South America and Asia. Choose your country and city at checkout and give your full street address and postal code so your parcel reaches you."],
    ["Payment", ["pay", "payment", "card", "cash", "price", "dollar", "money"],
        "Orders are currently paid on delivery. Online payment will be added soon. All prices are in US dollars ($)."],
    ["Track my order", ["track", "where is my", "status", "my orders", "shipped"],
        "Log in and open My Orders to see whether your order is Pending, Shipped or Delivered."],
    ["Returns & refunds", ["return", "refund", "exchange", "send back", "money back"],
        "You can return an item within 14 days of receiving it. It must be unused and in its original packaging. Email us with your order number and the reason, and we will reply with the next steps. See the Returns Policy for full details."],
    ["Damaged or wrong item", ["damaged", "broken", "wrong item", "wrong", "defect", "faulty", "not what i ordered"],
        "We are very sorry about that! Please email us right away with your order number and a photo of the item, and we will put it right."],
    ["Account & password", ["password", "login", "log in", "sign in", "account", "register", "sign up", "forgot"],
        "Use the Login page to sign in or create an account. If you forgot your password, tap 'Forgot password' on the login page and follow the steps to reset it."],
    ["Make a complaint", ["complain", "complaint", "bad service", "unhappy", "angry", "problem", "issue", "report"],
        "We are sorry you had a bad experience and we want to fix it. Please tap the Email us button below and describe the problem with your order number, and our team will get back to you."]
];

$fallback = "Thanks for your message! I could not find an exact answer to that. Please tap the Email us button below and our team will reply as soon as possible.";
?>
<link rel="stylesheet" href="assets/css/support.css?v=1">

<button type="button" class="care-fab" id="careFab" aria-label="Customer care" aria-expanded="false">
    <span class="care-fab-icon">🎧</span>
    <span class="care-fab-close">✕</span>
</button>

<div class="care-panel" id="carePanel" role="dialog" aria-label="Customer care chat" hidden>
    <div class="care-head">
        <div class="care-avatar">🎧</div>
        <div class="care-head-text">
            <strong><?php echo htmlspecialchars($careName); ?></strong>
            <span><i class="care-dot"></i> We reply instantly</span>
        </div>
        <button type="button" class="care-x" id="careClose" aria-label="Close">✕</button>
    </div>

    <div class="care-body" id="careBody"></div>

    <div class="care-chips" id="careChips"></div>
    <div class="care-foot">
        <form id="careForm" autocomplete="off">
            <input type="text" id="careInput" placeholder="Type your question..." maxlength="200">
            <button type="submit" aria-label="Send">➤</button>
        </form>
        <a class="care-mail" id="careMail" href="#">✉ Email us your complaint or question</a>
    </div>
</div>

<script>
window.CARE_CONFIG = <?php echo json_encode([
    "email" => $careEmail,
    "hello" => $careHello,
    "fallback" => $fallback,
    "faq" => $faq
], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP); ?>;
</script>
<script src="assets/js/support.js?v=1"></script>