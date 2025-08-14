<?php
session_start();
require_once '../includes/db_connect.php';  // Your secure DB connection file

// Make sure user is logged in and userId is set
if (!isset($_SESSION['user_id'])) {
    die('User not logged in');
}

$userId = $_SESSION['user_id'];

// Get POST data (example: delivery option, address, cart items, delivery price)
$deliveryOption = $_POST['deliveryOption'] ?? 'pickup'; // 'pickup' or 'delivery'
$address = $deliveryOption === 'delivery' ? trim($_POST['address'] ?? '') : null;
$cartItems = $_POST['cartItems'] ?? []; // Expect array of items: ['productId' => ..., 'quantity' => ...]
$deliveryPrice = 0.0;

// Calculate delivery price logic
if ($deliveryOption === 'delivery') {
    // Example delivery price logic: cap at R700, R5 otherwise
    $subtotal = 0;
    foreach ($cartItems as $item) {
        // You might want to query product price from DB or trust posted price carefully
        $productId = intval($item['productId']);
        $quantity = intval($item['quantity']);

        // Fetch price from DB (recommended)
        $stmt = $mysqli->prepare("SELECT price FROM PRODUCTS WHERE productId = ?");
        $stmt->bind_param('i', $productId);
        $stmt->execute();
        $stmt->bind_result($price);
        $stmt->fetch();
        $stmt->close();

        $subtotal += $price * $quantity;
    }

    // Delivery price capped at 700+
    if ($subtotal > 700) {
        $deliveryPrice = 0;
    } else {
        $deliveryPrice = 5;
    }

} else {
    $address = null;
    $deliveryPrice = 0;
}

// Calculate total price
$totalPrice = 0;
foreach ($cartItems as $item) {
    $productId = intval($item['productId']);
    $quantity = intval($item['quantity']);

    // Get price again (or reuse from above)
    $stmt = $mysqli->prepare("SELECT price FROM PRODUCTS WHERE productId = ?");
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $stmt->bind_result($price);
    $stmt->fetch();
    $stmt->close();

    $totalPrice += $price * $quantity;
}
$totalPrice += $deliveryPrice;

// Insert order
$stmt = $mysqli->prepare("INSERT INTO ORDERS (userId, deliveryPrice, totalPrice, status, createdAt, updatedAt) VALUES (?, ?, ?, 'Pending', NOW(), NOW())");
$stmt->bind_param('idd', $userId, $deliveryPrice, $totalPrice);

if (!$stmt->execute()) {
    die("Error inserting order: " . $stmt->error);
}

$orderId = $stmt->insert_id;
$stmt->close();

// Insert order items
$stmt = $mysqli->prepare("INSERT INTO ORDER_ITEMS (orderId, productId, quantity, price, createdAt) VALUES (?, ?, ?, ?, NOW())");

foreach ($cartItems as $item) {
    $productId = intval($item['productId']);
    $quantity = intval($item['quantity']);

    // Get price again (or reuse from above)
    $stmtPrice = $mysqli->prepare("SELECT price FROM PRODUCTS WHERE productId = ?");
    $stmtPrice->bind_param('i', $productId);
    $stmtPrice->execute();
    $stmtPrice->bind_result($price);
    $stmtPrice->fetch();
    $stmtPrice->close();

    $itemTotalPrice = $price * $quantity;

    $stmt->bind_param('iiid', $orderId, $productId, $quantity, $itemTotalPrice);
    if (!$stmt->execute()) {
        die("Error inserting order item: " . $stmt->error);
    }
}
$stmt->close();

// Optionally save address for user (if delivery)
if ($address) {
    $stmt = $mysqli->prepare("UPDATE USERS SET address = ? WHERE userId = ?");
    $stmt->bind_param('si', $address, $userId);
    $stmt->execute();
    $stmt->close();
}

// Optionally clear user's cart here (if you have a CART table)
$stmt = $mysqli->prepare("DELETE FROM CART WHERE userId = ?");
$stmt->bind_param('i', $userId);
$stmt->execute();
$stmt->close();

echo json_encode([
    'success' => true,
    'orderId' => $orderId,
    'message' => 'Order placed successfully',
]);

?>
