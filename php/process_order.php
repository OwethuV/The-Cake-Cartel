<?php
session_start();
require_once './includes/db_connect.php';  // your DB connection

// Check user logged in
if (!isset($_SESSION['userId'])) {
    http_response_code(401);
    die(json_encode(['success' => false, 'message' => 'User not logged in']));
}

$userId = $_SESSION['userId'];

// Get POST data
$deliveryOption = $_POST['deliveryOption'] ?? 'pickup';  // pickup or delivery
$address = ($deliveryOption === 'delivery') ? trim($_POST['address'] ?? '') : null;
$cartItems = $_POST['cartItems'] ?? [];  // array of ['productId' => ..., 'quantity' => ...]
$deliveryPrice = 0.0;

// Calculate subtotal
$subtotal = 0.0;
foreach ($cartItems as $item) {
    $productId = intval($item['productId']);
    $quantity = intval($item['quantity']);

    // Get product price from DB
    $stmt = $mysqli->prepare("SELECT price FROM PRODUCTS WHERE productId = ?");
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $stmt->bind_result($price);
    if (!$stmt->fetch()) {
        $stmt->close();
        die(json_encode(['success' => false, 'message' => 'Invalid product ID']));
    }
    $stmt->close();

    $subtotal += $price * $quantity;
}

// Delivery price logic
if ($deliveryOption === 'delivery') {
    $deliveryPrice = ($subtotal > 700) ? 0 : 5;
} else {
    $deliveryPrice = 0;
    $address = null;
}

$totalPrice = $subtotal + $deliveryPrice;

// Insert order
$stmt = $mysqli->prepare("INSERT INTO ORDERS (userId, deliveryPrice, totalPrice, status, createdAt, updatedAt) VALUES (?, ?, ?, 'Pending', NOW(), NOW())");
$stmt->bind_param('idd', $userId, $deliveryPrice, $totalPrice);
if (!$stmt->execute()) {
    die(json_encode(['success' => false, 'message' => 'Error inserting order: ' . $stmt->error]));
}
$orderId = $stmt->insert_id;
$stmt->close();

// Insert order items
$stmt = $mysqli->prepare("INSERT INTO ORDER_ITEMS (orderId, productId, quantity, price, createdAt) VALUES (?, ?, ?, ?, NOW())");
foreach ($cartItems as $item) {
    $productId = intval($item['productId']);
    $quantity = intval($item['quantity']);

    // Get price again (for total price per item)
    $stmtPrice = $mysqli->prepare("SELECT price FROM PRODUCTS WHERE productId = ?");
    $stmtPrice->bind_param('i', $productId);
    $stmtPrice->execute();
    $stmtPrice->bind_result($price);
    $stmtPrice->fetch();
    $stmtPrice->close();

    $itemTotalPrice = $price * $quantity;

    $stmt->bind_param('iiid', $orderId, $productId, $quantity, $itemTotalPrice);
    if (!$stmt->execute()) {
        die(json_encode(['success' => false, 'message' => 'Error inserting order item: ' . $stmt->error]));
    }
}
$stmt->close();

// Update user address if delivery
if ($address) {
    $stmt = $mysqli->prepare("UPDATE USERS SET address = ? WHERE userId = ?");
    $stmt->bind_param('si', $address, $userId);
    $stmt->execute();
    $stmt->close();
}

// Clear user's cart (optional)
$stmt = $mysqli->prepare("DELETE FROM CART WHERE userId = ?");
$stmt->bind_param('i', $userId);
$stmt->execute();
$stmt->close();

// Return success response
echo json_encode([
    'success' => true,
    'orderId' => $orderId,
    'message' => 'Order placed successfully'
]);
?>
