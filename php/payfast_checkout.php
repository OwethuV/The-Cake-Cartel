<?php
session_start();
require '../includes/db_connect.php';
require '../php/payfast/config.php'; // loads .env credentials

if (!isset($_SESSION['userId'])) {
    header("Location: ../login.php");
    exit();
}

$userId = $_SESSION['userId'];
$cartIds = $_POST['cartIds'] ?? [];
$totalPrice = $_POST['totalPrice'] ?? 0;
$deliveryPrice = 5.00;

if (!is_numeric($totalPrice) || $totalPrice <= 0) {
    die("Invalid order amount.");
}

// Get user email from session or DB
$email = $_SESSION['userEmail'] ?? '';
if (!$email) {
    $stmt = $conn->prepare("SELECT email FROM USERS WHERE userId = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($email);
    $stmt->fetch();
    $stmt->close();
}

// Insert orders for each cart item
foreach ($cartIds as $cartId) {
    $stmt = $conn->prepare("SELECT cartPrice FROM CART WHERE cartId = ? AND userId = ?");
    $stmt->bind_param("ii", $cartId, $userId);
    $stmt->execute();
    $stmt->bind_result($cartPrice);
    $stmt->fetch();
    $stmt->close();

    $totalWithDelivery = $cartPrice + $deliveryPrice;

    $stmt = $conn->prepare("INSERT INTO ORDERS (cartId, deliveryPrice, totalPrice, status) VALUES (?, ?, ?, 'Pending')");
    $stmt->bind_param("idd", $cartId, $deliveryPrice, $totalWithDelivery);
    $stmt->execute();
    $stmt->close();
}

// Create a unique order code for PayFast
$orderCode = 'ORD-' . bin2hex(random_bytes(4));

// Prepare PayFast request data
$pfData = [
    'merchant_id'    => trim($merchant_id),
    'merchant_key'   => trim($merchant_key),
    'return_url'     => trim($baseUrl) . '/payfast_return.php',
    'cancel_url'     => trim($baseUrl) . '/payfast_cancel.php',
    'notify_url'     => trim($baseUrl) . '/payfast_itn.php',
    'm_payment_id'   => $orderCode,
    'amount'         => number_format($totalPrice + $deliveryPrice, 2, '.', ''),
    'item_name'      => "Order $orderCode",
    'email_address'  => trim($email),
];

// 1. Sort the data by key
ksort($pfData);

// 2. Build the signature string (URL-encoded values)
$pfParamString = '';
foreach ($pfData as $key => $val) {
    if ($val !== '') {
        $pfParamString .= $key . '=' . urlencode(trim($val)) . '&';
    }
}
$pfParamString = rtrim($pfParamString, '&'); // Remove last '&'

// 3. No passphrase used — do not append one

// 4. Generate MD5 signature
$pfData['signature'] = md5($pfParamString);

// 5. Build PayFast URL correctly using RFC3986 encoding (for proper spaces etc.)
$queryString = http_build_query($pfData, '', '&', PHP_QUERY_RFC3986);

// 6. Determine PayFast URL (sandbox or production)
$payfastUrl = ($env === 'production')
    ? 'https://www.payfast.co.za/eng/process'
    : 'https://sandbox.payfast.co.za/eng/process';

// Optional debug
// file_put_contents('payfast_debug.log', $payfastUrl . '?' . $queryString);

// 7. Redirect user to PayFast
header('Location: ' . $payfastUrl . '?' . $queryString);
exit();
