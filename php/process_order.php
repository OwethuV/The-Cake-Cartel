<?php
session_start();
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['userId'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$userId = $_SESSION['userId'];
$deliveryOption = $_POST['deliveryMethod'] ?? 'pickup';
$address = ($deliveryOption === 'delivery') ? trim($_POST['address'] ?? '') : null;
$totalCartValue = floatval($_POST['totalCartValue'] ?? 0);
$cartIds = $_POST['cartIds'] ?? [];

if (empty($cartIds)) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty.']);
    exit();
}

$deliveryPrice = ($deliveryOption === 'delivery' && $totalCartValue <= 700) ? 5.00 : 0.00;
$totalPrice = $totalCartValue + $deliveryPrice;

$stmt = $conn->prepare("
    INSERT INTO ORDERS (userId, deliveryPrice, totalPrice, status, createdAt, updatedAt)
    VALUES (?, ?, ?, 'Pending', NOW(), NOW())
");
$stmt->bind_param('idd', $userId, $deliveryPrice, $totalPrice);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Error inserting order: ' . $stmt->error]);
    exit();
}
$orderId = $stmt->insert_id;
$stmt->close();

// Insert ORDER_ITEMS
foreach ($cartIds as $cartId) {
    $stmt = $conn->prepare("SELECT productId, quantity, cartPrice FROM CART WHERE cartId = ? AND userId = ?");
    $stmt->bind_param('ii', $cartId, $userId);
    $stmt->execute();
    $stmt->bind_result($productId, $quantity, $price);

    if ($stmt->fetch()) {
        $stmt->close();

        $insertItem = $conn->prepare("
            INSERT INTO ORDER_ITEMS (orderId, productId, quantity, price, createdAt)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $insertItem->bind_param('iiid', $orderId, $productId, $quantity, $price);
        $insertItem->execute();
        $insertItem->close();
    } else {
        $stmt->close();
    }
}

// Update address if needed
if ($address) {
    $stmt = $conn->prepare("UPDATE USERS SET address = ? WHERE userId = ?");
    $stmt->bind_param('si', $address, $userId);
    $stmt->execute();
    $stmt->close();
}

// Delete items from cart
$placeholders = implode(',', array_fill(0, count($cartIds), '?'));
$types = str_repeat('i', count($cartIds));
$query = "DELETE FROM CART WHERE cartId IN ($placeholders) AND userId = ?";
$types .= 'i';
$cartIds[] = $userId;

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$cartIds);
$stmt->execute();
$stmt->close();

echo json_encode([
    'success' => true,
    'orderId' => $orderId,
    'message' => 'Order placed successfully.'
]);
?>
