<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/include/db_connect.php";

if (isset($_SESSION['user_id'])) return;
if (!isset($_COOKIE['remember_token'])) return;

$token = $_COOKIE['remember_token'];
$hashedToken = hash('sha256', $token);

$stmt = $conn->prepare("SELECT id, username, first_name, last_name FROM users WHERE auth_token = ?");
$stmt->bind_param("s", $hashedToken);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['first_name'] = $user['first_name'];
    $_SESSION['last_name'] = $user['last_name'];
}

$stmt->close();
?>
