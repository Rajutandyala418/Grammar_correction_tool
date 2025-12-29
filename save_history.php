<?php
session_start();
include("validate_token.php");
require_once __DIR__ . "/include/db_connect.php";

if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized";
    exit();
}

if (!isset($_POST['input_text']) || !isset($_POST['output_text'])) {
    echo "Missing data";
    exit();
}

$user_id = $_SESSION['user_id'];
$input   = trim($_POST['input_text']);
$output  = trim($_POST['output_text']);

$stmt = $conn->prepare("INSERT INTO history (user_id, input_text, output_text) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $user_id, $input, $output);

if($stmt->execute()){
    echo "OK";
} else {
    echo "DB ERROR";
}

$stmt->close();
$conn->close();
