<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "fast_thrift");

// Constants - match with cart.php
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

// Check login
if (!isset($_SESSION['user']['email'])) {
    header("Location: login.php");
    exit;
}

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch orders
$user_email = $_SESSION['user']['email'];
$shipping = SHIPPING_FEE; // Create variable for binding

$stmt = $conn->prepare("SELECT o.*, 
    COALESCE(o.promo_code, '') as promo_code,
    COALESCE(o.discount, 0) as discount,
    COALESCE(o.shipping_fee, ?) as shipping_fee,
    COALESCE(o.discount_percentage, 0) as discount_percentage,
    COALESCE(o.subtotal, 0) as subtotal
    FROM orders o 
    WHERE o.user_email = ? 
    ORDER BY o.created_at DESC");

$stmt->bind_param("ds", $shipping, $user_email);
$stmt->execute();
$result = $stmt->get_result();
$orders = [];

while ($row = $result->fetch_assoc()) {
    $items = json_decode($row['order_data'], true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON decode error for order ID {$row['id']}: " . json_last_error_msg());
        continue;
    }

    // Use stored values or calculate if missing
    $subtotal = floatval($row['subtotal']);
    if ($subtotal === 0) {
        foreach ($items as $item) {
            $price = is_string($item['price']) ? str_replace(['₱', ','], '', $item['price']) : $item['price'];
            $subtotal += floatval($price);
        }
    }

    $discount = floatval($row['discount']);
    $shipping_fee = floatval($row['shipping_fee']);
    $discount_percentage = floatval($row['discount_percentage']);
    $total = $subtotal - $discount + $shipping_fee;

    $orders[] = [
        'id' => $row['id'],
        'created_at' => $row['created_at'],
        'user_email' => $row['user_email'],
        'address' => $row['address'],
        'items' => $items,
        'subtotal' => $subtotal,
        'discount' => $discount,
        'discount_percentage' => $discount_percentage,
        'shipping_fee' => $shipping_fee,
        'total' => $total,
        'promo_code' => $row['promo_code']
    ];
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Order History - Fast Thrift</title>
    <link rel="stylesheet" href="cart.css">
    <style>
        .receipt {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 20px auto;
        }
        .receipt-header { text-align: center; margin-bottom: 20px; }
        .customer-info { margin-bottom: 20px; }
        .items { width: 100%; border-collapse: collapse; }
        .items th, .items td { padding: 8px; border-bottom: 1px solid #ddd; }
        .price { text-align: right; }
        .discount-text { color: #28a745; }
        .free-shipping { color: #007bff; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Your Order History</h1>
    </div>
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="cart.php">Cart</a>
    </div>

    <?php if (empty($orders)): ?>
    <div class="receipt">
        <p style="text-align: center;">No orders found.</p>
    </div>
    <?php endif; ?>

    <?php foreach ($orders as $order): ?>
    <div class="receipt">
        <div class="receipt-header">
            <h3>Fast Thrift</h3>
            <p>Order #<?= htmlspecialchars($order['id']) ?></p>
            <p>Date: <?= htmlspecialchars($order['created_at']) ?></p>
        </div>
        <div class="customer-info">
            <p>Email: <?= htmlspecialchars($order['user_email']) ?></p>
            <p>Delivery Address: <?= htmlspecialchars($order['address']) ?></p>
        </div>
        <table class="items">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order['items'] as $item): 
                    $price = is_string($item['price']) ? str_replace(['₱', ','], '', $item['price']) : $item['price'];
                ?>
                    <tr>
                        <td><?= htmlspecialchars($item['title']) ?></td>
                        <td class="price">₱<?= number_format(floatval($price), 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td>Subtotal</td>
                    <td class="price">₱<?= number_format($order['subtotal'], 2) ?></td>
                </tr>
                <?php if ($order['subtotal'] >= 20000): ?>
                <tr>
                    <td class="discount-text">Automatic Discount (Order above ₱20,000)</td>
                    <td class="price">-₱<?= number_format(($order['subtotal'] * 0.20), 2) ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($order['promo_code']) && $order['discount'] > 0): ?>
                <tr>
                    <td class="discount-text">
                        Additional Discount (<?= htmlspecialchars($order['promo_code']) ?> - 
                        <?= number_format($order['discount_percentage'], 0) ?>% off)
                    </td>
                    <td class="price" style="color: #28a745">
                        -₱<?= number_format($order['discount'], 2) ?>
                    </td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td>
                        Shipping Fee
                        <?php if ($order['shipping_fee'] === 0): ?>
                            <span class="free-shipping">
                                (FREE with <?= htmlspecialchars($order['promo_code']) ?>)
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="price">₱<?= number_format($order['shipping_fee'], 2) ?></td>
                </tr>
                <tr>
                    <td><strong>Total</strong></td>
                    <td class="price">
                        <strong>₱<?= number_format($order['total'], 2) ?></strong>
                    </td>
                </tr>
            </tfoot>
        </table>
        <div class="receipt-footer">
            <p>Thank you for shopping at Fast Thrift!</p>
        </div>
    </div>
    <?php endforeach; ?>
</body>
</html>