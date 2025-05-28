<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "fast_thrift");

if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$stmt = $conn->prepare("SELECT code, type, value FROM promo_codes WHERE active = true AND valid_until >= CURDATE()");
$stmt->execute();
$result = $stmt->get_result();

$promo_codes = [];
while ($row = $result->fetch_assoc()) {
    $promo_codes[$row['code']] = [
        'type' => $row['type'],
        'value' => floatval($row['value'])
    ];
}

echo json_encode($promo_codes);

$stmt->close();
$conn->close();