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

// Get user email
$email = $_SESSION['userEmail'] ?? '';
if (!$email) {
    $stmt = $conn->prepare("SELECT email FROM USERS WHERE userId = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($email);
    $stmt->fetch();
    $stmt->close();
}

// Insert each cart item into the ORDERS table
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

// Create a unique order reference
$orderCode = 'ORD-' . bin2hex(random_bytes(4));

// Prepare PayFast parameters
$pfData = [
    'merchant_id'    => trim($merchant_id),
    'merchant_key'   => trim($merchant_key),
    'return_url'     => rtrim(trim($baseUrl), '/') . '/payfast_return.php',
    'cancel_url'     => rtrim(trim($baseUrl), '/') . '/payfast_cancel.php',
    'notify_url'     => rtrim(trim($baseUrl), '/') . '/payfast_itn.php',
    'm_payment_id'   => $orderCode,
    'amount'         => number_format($totalPrice + $deliveryPrice, 2, '.', ''),
    'item_name'      => "Order $orderCode",
    'email_address'  => trim($email),
];

// Step 1: Sort the array by key
ksort($pfData);

// Step 2: Build the signature string
$signatureString = '';
foreach ($pfData as $key => $val) {
    $val = trim($val);
    if ($val !== '') {
        $signatureString .= $key . '=' . urlencode($val) . '&';
    }
}
$signatureString = rtrim($signatureString, '&');

// Step 3: Append passphrase if set
if (!empty($passphrase)) {
    $signatureString .= '&passphrase=' . urlencode($passphrase);
}

// Step 4: Generate signature
$signature = md5($signatureString);
$pfData['signature'] = $signature;

// Step 5: Build the final query string
$queryString = http_build_query($pfData, '', '&', PHP_QUERY_RFC3986);

// Step 6: PayFast URL
$payfastUrl = ($env === 'production') 
    ? 'https://www.payfast.co.za/eng/process' 
    : 'https://sandbox.payfast.co.za/eng/process';

// Step 7: Debug logging (optional, can delete after it works)
file_put_contents('payfast_signature_debug.log', "Signature String:\n$signatureString\n\nSignature:\n$signature\n\nURL:\n$payfastUrl?$queryString\n");

// Step 8: Redirect to PayFast
header("Location: $payfastUrl?$queryString");
exit();
