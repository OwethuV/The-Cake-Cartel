<?php
session_start();
require_once '../includes/db_connect.php'; // uses $mysqli

if (!isset($_SESSION['userId'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['userId'];
$deliveryOption = $_POST['deliveryOption'] ?? 'pickup';
$address = ($deliveryOption === 'delivery') ? trim($_POST['address'] ?? '') : null;
$cartItems = $_POST['cartItems'] ?? [];

if (empty($cartItems)) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
}

$subtotal = 0.0;

foreach ($cartItems as $item) {
    $productId = intval($item['productId']);
    $quantity = intval($item['quantity']);

    $stmt = $mysqli->prepare("SELECT price FROM PRODUCTS WHERE productId = ?");
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $stmt->bind_result($price);

    if (!$stmt->fetch()) {
        $stmt->close();
        echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
        exit;
    }

    $stmt->close();

    $subtotal += $price * $quantity;
}

// Delivery logic
$deliveryPrice = 0;
if ($deliveryOption === 'delivery') {
    $deliveryPrice = ($subtotal > 700) ? 0 : 5;
} else {
    $address = null;
}

$totalPrice = $subtotal + $deliveryPrice;

// Insert into ORDERS
$stmt = $mysqli->prepare("INSERT INTO ORDERS (userId, deliveryPrice, totalPrice, status, createdAt, updatedAt) VALUES (?, ?, ?, 'Pending', NOW(), NOW())");
$stmt->bind_param('idd', $userId, $deliveryPrice, $totalPrice);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Failed to insert order']);
    exit;
}
$orderId = $stmt->insert_id;
$stmt->close();

// Insert ORDER_ITEMS
$itemStmt = $mysqli->prepare("INSERT INTO ORDER_ITEMS (orderId, productId, quantity, price, createdAt) VALUES (?, ?, ?, ?, NOW())");

foreach ($cartItems as $item) {
    $productId = intval($item['productId']);
    $quantity = intval($item['quantity']);

    $priceStmt = $mysqli->prepare("SELECT price FROM PRODUCTS WHERE productId = ?");
    $priceStmt->bind_param('i', $productId);
    $priceStmt->execute();
    $priceStmt->bind_result($price);
    $priceStmt->fetch();
    $priceStmt->close();

    $itemTotalPrice = $price * $quantity;

    $itemStmt->bind_param('iiid', $orderId, $productId, $quantity, $itemTotalPrice);
    if (!$itemStmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Error inserting order item']);
        exit;
    }
}
$itemStmt->close();

// Save delivery address if provided
if ($address) {
    $stmt = $mysqli->prepare("UPDATE USERS SET address = ? WHERE userId = ?");
    $stmt->bind_param('si', $address, $userId);
    $stmt->execute();
    $stmt->close();
}

// Clear user's cart
$stmt = $mysqli->prepare("DELETE FROM CART WHERE userId = ?");
$stmt->bind_param('i', $userId);
$stmt->execute();
$stmt->close();

echo json_encode([
    'success' => true,
    'orderId' => $orderId,
    'message' => 'Order placed successfully'
]);
?>
