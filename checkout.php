<?php
session_start();
header('Content-Type: application/json');

// Constants
define('SHIPPING_FEE', 100.00);
define('PROMO_CODES', [
    'WELCOME10' => ['type' => 'discount', 'value' => 10],
    'WELCOME20' => ['type' => 'discount', 'value' => 20],
    'WELCOME30' => ['type' => 'discount', 'value' => 30],
    'WELCOME40' => ['type' => 'discount', 'value' => 40],
    'WELCOME50' => ['type' => 'discount', 'value' => 50],
    'SUMMER25' => ['type' => 'discount', 'value' => 25],
    'FREESHIP' => ['type' => 'shipping', 'value' => true],
    'THRIFTY15' => ['type' => 'discount', 'value' => 15],
    'SAVE35' => ['type' => 'discount', 'value' => 35],
    'KAEYLEPOGI' => ['type' => 'discount', 'value' => 100]
]);
define('AUTO_DISCOUNT_THRESHOLD', 25000);
define('AUTO_DISCOUNT_PERCENTAGE', 20);

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid request data']);
    exit;
}

$cart = $data['cart'] ?? [];
$user = $data['user'] ?? null;
$promoCode = $data['promo_code'] ?? '';
$appliedDiscount = $data['discount'] ?? 0;
$freeShipping = $data['freeShipping'] ?? false;

// Validate user data
if (!$user || !$user['email'] || !$user['address']) {
    echo json_encode(['success' => false, 'message' => 'Missing user information']);
    exit;
}

// Connect to database
$conn = new mysqli("localhost", "root", "", "fast_thrift");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Check if promo code was already used by the user
function checkPromoCodeUsage($conn, $user_email, $promo_code) {
    $stmt = $conn->prepare("SELECT id FROM user_promo_usage WHERE user_email = ? AND promo_code = ?");
    $stmt->bind_param("ss", $user_email, $promo_code);
    $stmt->execute();
    $result = $stmt->get_result();
    $used = $result->num_rows > 0;
    $stmt->close();
    return $used;
}

// Calculate totals
$subtotal = 0;
foreach ($cart as $item) {
    $price = is_string($item['price']) ? str_replace(['₱', ','], '', $item['price']) : $item['price'];
    $subtotal += floatval($price);
}

// Update the discount calculation section
$discount = 0;
$shipping_fee = SHIPPING_FEE;
$discount_percentage = 0;
$auto_discount = 0;

// Calculate automatic discount
if ($subtotal >= AUTO_DISCOUNT_THRESHOLD) {
    $auto_discount = ($subtotal * AUTO_DISCOUNT_PERCENTAGE) / 100;
}

// Apply promo code discount
if (!empty($promoCode) && isset(PROMO_CODES[$promoCode])) {
    // Check if user has already used this promo code
    if (checkPromoCodeUsage($conn, $user['email'], $promoCode)) {
        echo json_encode([
            'success' => false,
            'message' => 'You have already used this promo code'
        ]);
        exit;
    }

    $promo = PROMO_CODES[$promoCode];
    if ($promo['type'] === 'discount') {
        $discount_percentage = $promo['value'];
        $promo_discount = ($subtotal * $discount_percentage) / 100;
        $discount = $auto_discount + $promo_discount; // Add both discounts
    } elseif ($promo['type'] === 'shipping') {
        $shipping_fee = 0;
        $discount = $auto_discount; // Only automatic discount
    }

    // Record promo code usage
    $stmt = $conn->prepare("INSERT INTO user_promo_usage (user_email, promo_code) VALUES (?, ?)");
    $stmt->bind_param("ss", $user['email'], $promoCode);
    $stmt->execute();
    $stmt->close();
} else {
    $discount = $auto_discount; // Only automatic discount
}

// Calculate final total
$total = $subtotal - $discount + $shipping_fee;

// Update product quantities
foreach ($cart as $item) {
    $stmt = $conn->prepare("UPDATE products SET quantity = quantity - 1 WHERE id = ? AND quantity > 0");
    $stmt->bind_param("s", $item['id']);
    $stmt->execute();
    $stmt->close();
}

// Save order to database
$stmt = $conn->prepare("INSERT INTO orders (
    user_email, 
    order_data, 
    total,
    subtotal,
    discount,
    discount_percentage,
    shipping_fee,
    address,
    promo_code
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

$order_data = json_encode($cart);
$stmt->bind_param(
    "ssddddsss",
    $user['email'],
    $order_data,
    $total,
    $subtotal,
    $discount,
    $discount_percentage,
    $shipping_fee,
    $user['address'],
    $promoCode
);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully',
        'order_id' => $conn->insert_id
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to save order'
    ]);
}

$stmt->close();
$conn->close();