<?php
// notify.php

// IPN to verify payment status from PayFast

// Load env
$merchant_id = getenv('PAYFAST_MERCHANT_ID');
$merchant_key = getenv('PAYFAST_MERCHANT_KEY');

$pfHost = 'https://www.payfast.co.za'; // live

// Read POST data from PayFast
$pfData = $_POST;

// Step 1: Verify source IP (optional but recommended)

// Step 2: Build signature for verification
$pfParamString = '';
foreach ($pfData as $key => $val) {
    if ($key !== 'signature') {
        $pfParamString .= "$key=" . urlencode(trim($val)) . '&';
    }
}
$pfParamString = rtrim($pfParamString, '&');
$signature = md5($pfParamString);

if ($signature !== $pfData['signature']) {
    // Invalid signature
    header('HTTP/1.0 400 Bad Request');
    exit('Invalid signature');
}

// Step 3: Verify payment status
if ($pfData['payment_status'] !== 'COMPLETE') {
    exit('Payment not complete');
}

// Step 4: Verify merchant ID and key match
if ($pfData['merchant_id'] !== $merchant_id || $pfData['merchant_key'] !== $merchant_key) {
    exit('Invalid merchant details');
}

// Step 5: Verify amount and m_payment_id, update order accordingly
require_once '../includes/db_connect.php';

$orderId = intval($pfData['m_payment_id']);
$amountPaid = floatval($pfData['amount_gross']);

// Fetch order from DB
$stmt = $conn->prepare("SELECT totalPrice, status FROM ORDERS WHERE orderId = ?");
$stmt->bind_param('i', $orderId);
$stmt->execute();
$stmt->bind_result($totalPrice, $status);
$stmt->fetch();
$stmt->close();

if (!$totalPrice || $status !== 'Pending') {
    exit('Order not found or already processed');
}

// Check amounts match (allow small float rounding difference)
if (abs($totalPrice - $amountPaid) > 0.01) {
    exit('Amount mismatch');
}

// Update order status to Paid
$stmt = $conn->prepare("UPDATE ORDERS SET status = 'Paid', updatedAt = NOW() WHERE orderId = ?");
$stmt->bind_param('i', $orderId);
$stmt->execute();
$stmt->close();

$conn->close();

http_response_code(200);
echo "OK";
exit();
