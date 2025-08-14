<?php
session_start();
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];
$deliveryOption = $_POST['deliveryOption'] ?? 'pickup';
$address = $deliveryOption === 'delivery' ? trim($_POST['address'] ?? '') : null;
$cartItems = $_POST['cartItems'] ?? [];

if (!is_array($cartItems) || empty($cartItems)) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty or invalid']);
    exit;
}

$mysqli->begin_transaction();

try {
    // Prepare placeholders for IN clause and types string
    $productIds = array_map(fn($item) => intval($item['productId']), $cartItems);
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $types = str_repeat('i', count($productIds));

    // Fetch all product prices in one query
    $stmt = $mysqli->prepare("SELECT productId, price FROM PRODUCTS WHERE productId IN ($placeholders)");
    $stmt->bind_param($types, ...$productIds);
    $stmt->execute();
    $result = $stmt->get_result();

    $productPrices = [];
    while ($row = $result->fetch_assoc()) {
        $productPrices[$row['productId']] = $row['price'];
    }
    $stmt->close();

    // Verify all products are valid
    foreach ($productIds as $pid) {
        if (!isset($productPrices[$pid])) {
            throw new Exception("Invalid product ID $pid");
        }
    }

    // Calculate subtotal
    $subtotal = 0;
    foreach ($cartItems as $item) {
        $pid = intval($item['productId']);
        $qty = intval($item['quantity']);
        $subtotal += $productPrices[$pid] * $qty;
    }

    // Calculate delivery price logic
    $deliveryPrice = 0;
    if ($deliveryOption === 'delivery') {
        $deliveryPrice = ($subtotal > 700) ? 0 : 5;
    }

    $totalPrice = $subtotal + $deliveryPrice;

    // Insert into ORDERS table
    $stmt = $mysqli->prepare("INSERT INTO ORDERS (userId, deliveryPrice, totalPrice, status, createdAt, updatedAt) VALUES (?, ?, ?, 'Pending', NOW(), NOW())");
    $stmt->bind_param('idd', $userId, $deliveryPrice, $totalPrice);
    if (!$stmt->execute()) {
        throw new Exception("Failed to insert order: " . $stmt->error);
    }
    $orderId = $stmt->insert_id;
    $stmt->close();

    // Insert order items
    $stmt = $mysqli->prepare("INSERT INTO ORDER_ITEMS (orderId, productId, quantity, price, createdAt) VALUES (?, ?, ?, ?, NOW())");

    foreach ($cartItems as $item) {
        $pid = intval($item['productId']);
        $qty = intval($item['quantity']);
        $itemPrice = $productPrices[$pid] * $qty;

        $stmt->bind_param('iiid', $orderId, $pid, $qty, $itemPrice);
        if (!$stmt->execute()) {
            throw new Exception("Failed to insert order item: " . $stmt->error);
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

    // Clear user cart
    $stmt = $mysqli->prepare("DELETE FROM CART WHERE userId = ?");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stmt->close();

    // Commit transaction
    $mysqli->commit();

    echo json_encode([
        'success' => true,
        'orderId' => $orderId,
        'message' => 'Order placed successfully',
    ]);
} catch (Exception $e) {
    // Rollback on error
    $mysqli->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Order failed: ' . $e->getMessage(),
    ]);
}
?>
