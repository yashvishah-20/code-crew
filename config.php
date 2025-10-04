<?php
session_start();
$dbHost = '127.0.0.1';
$dbName = 'expense_man';
$dbUser = 'root';
$dbPass = ''; // set your root password if any

$dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}

function is_logged_in() {
    return !empty($_SESSION['user_id']);
}
function current_user($pdo) {
    if (!is_logged_in()) return null;
    $stmt = $pdo->prepare("SELECT * FROM user_account WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function redirect($url) {
    header("Location: $url");
    exit;
}
