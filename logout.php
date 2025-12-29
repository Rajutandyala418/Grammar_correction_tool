<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/include/db_connect.php";

if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $stmt = $conn->prepare("UPDATE users SET auth_token = NULL WHERE id = ?");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $stmt->close();
}

if (isset($_COOKIE['remember_token'])) {
    setcookie("remember_token", "", time() - 3600, "/");
}

$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 3600, $p["path"], $p["domain"], $p["secure"], $p["httponly"]);
}

session_destroy();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");
header("Location: login.php");
exit();
?>
