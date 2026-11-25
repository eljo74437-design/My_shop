<?php
header('Content-Type: application/json; charset=utf-8');
include 'config/db.php';

function reply($ok, $message) {
    echo json_encode(['ok' => $ok, 'message' => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    reply(false, 'Please use the subscribe form.');
}

// Bots fill the hidden "website" box. Real people never see it.
if (trim($_POST['website'] ?? '') !== '') {
    reply(true, 'Thank you for subscribing!');
}

$email = strtolower(trim($_POST['email'] ?? ''));

if (strlen($email) > 150 or !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    reply(false, 'Please enter a valid email address.');
}

try {
    $stmt = $pdo->prepare("INSERT IGNORE INTO subscribers (email, token) VALUES (?, ?)");
    $stmt->execute([$email, bin2hex(random_bytes(16))]);
} catch (Exception $e) {
    reply(false, 'Sorry, something went wrong. Please try again.');
}

reply(true, 'Thank you! You are subscribed to our updates.');