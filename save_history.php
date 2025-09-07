<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$input_text = $data['input_text'] ?? '';
$output_text = $data['output_text'] ?? '';
$user_id = $_SESSION['user_id'];

$mysqli = new mysqli("localhost", "root", "", "grammar_tool");
if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "DB connection failed"]);
    exit();
}

$stmt = $mysqli->prepare("INSERT INTO history (user_id, input_text, output_text) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $user_id, $input_text, $output_text);
$stmt->execute();
$stmt->close();
$mysqli->close();

echo json_encode(["success" => true]);
?>