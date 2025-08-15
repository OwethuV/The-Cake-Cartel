<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../vendor/autoload.php';

$merchant_id = $_ENV['PAYFAST_MERCHANT_ID'];
$merchant_key = $_ENV['PAYFAST_MERCHANT_KEY'];
$return_url = $_ENV['PAYFAST_RETURN_URL'];
$cancel_url = $_ENV['PAYFAST_CANCEL_URL'];
$notify_url = $_ENV['PAYFAST_NOTIFY_URL'];

if (!isset($_SESSION['userId'])) {
    header("Location: ../login.php");
    exit();
}

$userId = $_SESSION['userId'];
$deliveryOption = $_POST['deliveryMethod'] ?? 'pickup';
$address = ($deliveryOption === 'delivery') ? trim($_POST['address'] ?? '') : null;
$totalCartValue = floatval($_POST['totalCartValue'] ?? 0);
$cartIds = $_POST['cartIds'] ?? [];

if (empty($cartIds)) {
    die("Cart is empty.");
}

$deliveryPrice = ($deliveryOption === 'delivery' && $totalCartValue <= 700) ? 5.00 : 0.00;
$totalPrice = $totalCartValue + $deliveryPrice;

// Save address
if ($address) {
    $stmt = $conn->prepare("UPDATE USERS SET address = ? WHERE userId = ?");
    $stmt->bind_param('si', $address, $userId);
    $stmt->execute();
    $stmt->close();
}

// Create order
$stmt = $conn->prepare("INSERT INTO ORDERS (userId, deliveryPrice, totalPrice, status, createdAt, updatedAt) VALUES (?, ?, ?, 'Pending', NOW(), NOW())");
$stmt->bind_param('idd', $userId, $deliveryPrice, $totalPrice);
$stmt->execute();
$orderId = $stmt->insert_id;
$stmt->close();

// Order items
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

// Clear cart
$placeholders = implode(',', array_fill(0, count($cartIds), '?'));
$types = str_repeat('i', count($cartIds)) . 'i';
$cartIds[] = $userId;
$stmt = $conn->prepare("DELETE FROM CART WHERE cartId IN ($placeholders) AND userId = ?");
$stmt->bind_param($types, ...$cartIds);
$stmt->execute();
$stmt->close();

// ✅ Prepare PayFast data
$pfData = [
    'merchant_id' => $merchant_id,
    'merchant_key' => $merchant_key,
    'return_url' => $return_url,
    'cancel_url' => $cancel_url,
    'notify_url' => $notify_url,
    'm_payment_id' => $orderId,
    'amount' => number_format($totalPrice, 2, '.', ''),
    'item_name' => "Order #$orderId - DessertECommerce",
];

// ✅ Signature generation (MUST BE IN THE EXACT ORDER)
ksort($pfData);
$pfParamString = "";
foreach ($pfData as $key => $val) {
    $pfParamString .= $key . '=' . urlencode(trim($val)) . '&';
}
$pfParamString = rtrim($pfParamString, '&');
$pfSignature = md5($pfParamString);
$pfData['signature'] = $pfSignature;

// ✅ Submit to PayFast
$payfastUrl = "https://www.payfast.co.za/eng/process";

echo '<html><body>';
echo '<form id="payfastForm" action="' . htmlspecialchars($payfastUrl) . '" method="post">';
foreach ($pfData as $key => $value) {
    echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
}
echo '</form>';
echo '<script>document.getElementById("payfastForm").submit();</script>';
echo '</body></html>';
$conn->close();
exit();
?>
