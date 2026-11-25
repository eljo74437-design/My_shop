<?php
session_start();
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$action = $_POST['action'] ?? '';

function back($msg, $tab) {
    header('Location: login.php?msg=' . urlencode($msg) . '&tab=' . $tab);
    exit;
}

if ($action === 'register') {

    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if ($name === '') back('Please enter your name.', 'register');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) back('Please enter a valid email.', 'register');
    if (strlen($password) < 6) back('Password must be at least 6 characters.', 'register');

    $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);
    if ($check->fetch()) back('That email already has an account. Please log in.', 'login');

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);

    session_regenerate_id(true);
    $_SESSION['user_id'] = $pdo->lastInsertId();
    $_SESSION['user_name'] = $name;

    header('Location: index.php');
    exit;
}

if ($action === 'login') {

    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password'])) {
        back('Incorrect email or password.', 'login');
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
   $_SESSION['user_name'] = $name;
    try { $pdo->prepare("INSERT INTO login_log (user_id) VALUES (?)")->execute([$_SESSION['user_id']]); } catch (Exception $e) {}
   $_SESSION['user_role'] = $user['role'];
    try { $pdo->prepare("INSERT INTO login_log (user_id) VALUES (?)")->execute([$user['id']]); } catch (Exception $e) {}
    header('Location: index.php');
    exit;
}

header('Location: login.php');