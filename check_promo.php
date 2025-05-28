<?php
session_start();
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['email']) || !isset($data['promo_code'])) {
    echo json_encode(['can_use' => false]);
    exit;
}

$conn = new mysqli("localhost", "root", "", "fast_thrift");
if ($conn->connect_error) {
    echo json_encode(['can_use' => false]);
    exit;
}

$stmt = $conn->prepare("SELECT id FROM user_promo_usage WHERE user_email = ? AND promo_code = ?");
$stmt->bind_param("ss", $data['email'], $data['promo_code']);
$stmt->execute();
$result = $stmt->get_result();
$can_use = $result->num_rows === 0;

$stmt->close();
$conn->close();

echo json_encode(['can_use' => $can_use]);