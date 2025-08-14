<?php
session_start();
require_once '../includes/db_connect.php';  // using $conn

if (!isset($_SESSION['userId'])) {
    http_response_code(401);
    die(json_encode(['success' => false, 'message' => 'User not logged in']));
}

$userId = $_SESSION['userId'];
$deliveryOption = $_POST['deliveryMethod'] ?? 'pickup';
$address = ($deliveryOption === 'delivery') ? trim($_POST['address'] ?? '') : null;
$totalCartValue = floatval($_POST['totalCartValue'] ?? 0);
$cartIds = $_POST['cartIds'] ?? [];

if (empty($cartIds)) {
    die(json_encode(['success' => false, 'message' => 'Cart is empty.']));
}

// Calculate delivery price
$deliveryPrice = ($deliveryOption === 'delivery' && $totalCartValue <= 700) ? 5.00 : 0.00;
$totalPrice = $totalCartValue + $deliveryPrice;

// Insert order
$stmt = $conn->prepare("INSERT INTO ORDERS (userId, deliveryPrice, totalPrice, status, createdAt, updatedAt) VALUES (?, ?, ?, 'Pending', NOW(), NOW())");
$stmt->bind_param('idd', $userId, $deliveryPrice, $totalPrice);
if (!$stmt->execute()) {
    die(json_encode(['success' => false, 'message' => 'Error inserting order: ' . $stmt->error]));
}
$orderId = $stmt->insert_id;
$stmt->close();

// Insert each cart item into ORDER_ITEMS
foreach ($cartIds as $cartId) {
    $stmt = $conn->prepare("SELECT productId, quantity, cartPrice FROM CART WHERE cartId = ? AND userId = ?");
    $stmt->bind_param('ii', $cartId, $userId);
    $stmt->execute();
    $stmt->bind_result($productId, $quantity, $price);
    if ($stmt->fetch()) {
        $stmt->close();

        $insertItem = $conn->prepare("INSERT INTO ORDER_ITEMS (orderId, productId, quantity, price, createdAt) VALUES (?, ?, ?, ?, NOW())");
        $insertItem->bind_param('iiid', $orderId, $productId, $quantity, $price);
        $insertItem->execute();
        $insertItem->close();
    } else {
        $stmt->close();
    }
}

// Update user address if delivery
if ($address) {
    $stmt = $conn->prepare("UPDATE USERS SET address = ? WHERE userId = ?");
    $stmt->bind_param('si', $address, $userId);
    $stmt->execute();
    $stmt->close();
}

// Clear cart items
$placeholders = implode(',', array_fill(0, count($cartIds), '?'));
$types = str_repeat('i', count($cartIds));
$stmt = $conn->prepare("DELETE FROM CART WHERE cartId IN ($placeholders)");
$stmt->bind_param($types, ...$cartIds);
$stmt->execute();
$stmt->close();

echo json_encode(['success' => true, 'orderId' => $orderId, 'message' => 'Order placed successfully.']);
?>
