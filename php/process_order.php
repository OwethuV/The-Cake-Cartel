<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['userId'])) {
    http_response_code(401);
    die(json_encode(['success' => false, 'message' => 'User not logged in']));
}

$userId = $_SESSION['userId'];
$deliveryOption = $_POST['deliveryOption'] ?? 'pickup';
$address = ($deliveryOption === 'delivery') ? trim($_POST['address'] ?? '') : null;
$cartIds = $_POST['cartIds'] ?? [];
$totalCartValue = floatval($_POST['totalCartValue'] ?? 0);
$deliveryPrice = ($deliveryOption === 'delivery' && $totalCartValue <= 700) ? 5 : 0;
$totalPrice = $totalCartValue + $deliveryPrice;

// Insert order
$stmt = $conn->prepare("INSERT INTO ORDERS (userId, deliveryPrice, totalPrice, status, createdAt, updatedAt) VALUES (?, ?, ?, 'Pending', NOW(), NOW())");
$stmt->bind_param('idd', $userId, $deliveryPrice, $totalPrice);
if (!$stmt->execute()) {
    die(json_encode(['success' => false, 'message' => 'Failed to insert order: ' . $stmt->error]));
}
$orderId = $stmt->insert_id;
$stmt->close();

// Insert order items (from cart)
foreach ($cartIds as $cartId) {
    // Fetch item info
    $stmt = $conn->prepare("SELECT productId, quantity, cartPrice FROM CART WHERE cartId = ?");
    $stmt->bind_param('i', $cartId);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    $stmt->close();

    if ($item) {
        $stmt = $conn->prepare("INSERT INTO ORDER_ITEMS (orderId, productId, quantity, price, createdAt) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param('iiid', $orderId, $item['productId'], $item['quantity'], $item['cartPrice']);
        $stmt->execute();
        $stmt->close();
    }
}

// Save address if delivery
if ($address) {
    $stmt = $conn->prepare("UPDATE USERS SET address = ? WHERE userId = ?");
    $stmt->bind_param('si', $address, $userId);
    $stmt->execute();
    $stmt->close();
}

// Clear user's cart
$stmt = $conn->prepare("DELETE FROM CART WHERE userId = ?");
$stmt->bind_param('i', $userId);
$stmt->execute();
$stmt->close();

echo json_encode([
    'success' => true,
    'orderId' => $orderId,
    'message' => 'Order placed successfully'
]);
?>
