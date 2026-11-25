<?php
// Edit these two lines to match your shop
$supportEmail = "support storefussion0@gmail.com";
$returnDays = 14;

$updated = date("F Y");

$pages = [

    "guide" => [
        "title" => "Shopping Guide",
        "intro" => "Everything you need to place an order with us.",
        "sections" => [
            ["How to order", "Browse the shop and tap ADD + on anything you like. Open your cart with the cart button, check your items and tap Checkout. Log in or create an account, then enter your delivery details and place your order."],
            ["Delivery", "We deliver across Europe, South America and Asia. Choose your country and city at checkout, and give your full street address and postal code so your parcel reaches you."],
            ["Payment", "Orders are currently paid on delivery. Online payment will be added soon. All prices are shown in US dollars ($)."],
            ["Tracking your order", "Log in and open My Orders to see whether your order is Pending, Shipped or Delivered."],
            ["Need help?", "Email us at " . $supportEmail . " with your order number and we will gladly help."]
        ]
    ],

    "returns" => [
        "title" => "Returns Policy",
        "intro" => "We want you to love what you ordered. If you do not, here is how returns work.",
        "sections" => [
            ["Return window", "You can return an item within " . $returnDays . " days of receiving it."],
            ["Condition of items", "Items must be unused, in their original condition and in their original packaging."],
            ["How to return", "Email us at " . $supportEmail . " with your order number and the reason for the return. We will reply with the next steps."],
            ["Refunds", "Once we receive and check the returned item, we will refund the price you paid for it. Delivery costs may not be refundable."],
            ["Damaged or wrong items", "If your item arrives damaged or is not what you ordered, contact us as soon as possible and we will put it right."]
        ]
    ],

    "privacy" => [
        "title" => "Privacy Policy",
        "intro" => "How we look after your personal information. Last updated " . $updated . ".",
        "sections" => [
            ["What we collect", "When you register and order, we collect your name, email address, phone number, delivery address, your order history and the times you log in to your account."],
            ["How we use it", "We use your details to process your orders, deliver your parcels, contact you about your delivery and manage your account."],
            ["Newsletter", "If you subscribe to our newsletter, we keep your email address so we can send you news about new arrivals and offers. Every email includes a link to unsubscribe, and you can also ask us to remove you at any time."],
            ["Your password", "Your password is never stored as plain text. It is scrambled before it is saved, so even we cannot read it."],
            ["Sharing", "We do not sell your personal information. We only share what is needed to get your order to you."],
            ["Maps", "The delivery map uses OpenStreetMap. When you pick a city, the city name is sent to their service to find it on the map."],
            ["On your device", "Your cart and your light or dark mode choice are stored in your own browser so the site can remember them."],
            ["Your rights", "You can ask to see, correct or delete your information at any time by emailing " . $supportEmail . "."]
        ]
    ]
];

$key = $_GET['page'] ?? 'guide';
if (!isset($pages[$key])) { $key = 'guide'; }
$page = $pages[$key];

$pageTitle = $page['title'] . " | StoreFussion";
include 'header.php';
?>

<section class="info-page">

    <div class="info-tabs">
        <?php foreach ($pages as $k => $p): ?>
            <a href="info.php?page=<?php echo $k; ?>" class="<?php echo $k === $key ? 'on' : ''; ?>"><?php echo htmlspecialchars($p['title']); ?></a>
        <?php endforeach; ?>
    </div>

    <div class="info-card">
        <h1><?php echo htmlspecialchars($page['title']); ?></h1>
        <p class="info-intro"><?php echo htmlspecialchars($page['intro']); ?></p>

        <?php foreach ($page['sections'] as $s): ?>
            <h2><?php echo htmlspecialchars($s[0]); ?></h2>
            <p><?php echo htmlspecialchars($s[1]); ?></p>
        <?php endforeach; ?>
    </div>
    </section>

<?php include 'footer.php'; ?>