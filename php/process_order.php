<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['userId'])) {
    $_SESSION['message'] = "Please login to place an order.";
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['cartIds'])) {
    $userId = $_SESSION['userId'];
    $cartIds = $_POST['cartIds'];
    $deliveryOption = $_POST['deliveryOption'];
    $totalCartValue = floatval($_POST['totalCartValue']);

    // Determine delivery price
    $deliveryPrice = ($deliveryOption === 'delivery' && $totalCartValue <= 700) ? 5.00 : 0.00;
    $finalTotal = $totalCartValue + $deliveryPrice;

    // Prepare PayFast variables
    $merchant_id = $_ENV['PAYFAST_MERCHANT_ID'];
    $merchant_key = $_ENV['PAYFAST_MERCHANT_KEY'];
    $return_url = $_ENV['PAYFAST_RETURN_URL'];
    $cancel_url = $_ENV['PAYFAST_CANCEL_URL'];
    $notify_url = $_ENV['PAYFAST_NOTIFY_URL'];

    $order_id = uniqid(); // you can replace this with actual DB order ID if desired

    // Build PayFast payment form
    $data = array(
        'merchant_id' => $merchant_id,
        'merchant_key' => $merchant_key,
        'return_url' => $return_url,
        'cancel_url' => $cancel_url,
        'notify_url' => $notify_url,
        'amount' => number_format($finalTotal, 2, '.', ''),
        'item_name' => 'Dessert Order - ' . date('Y-m-d H:i'),
        'custom_str1' => json_encode([
            'userId' => $userId,
            'cartIds' => $cartIds,
            'deliveryOption' => $deliveryOption,
            'deliveryPrice' => $deliveryPrice,
        ])
    );

    // Redirect to PayFast via POST
    echo '<form id="payfast_form" action="https://www.payfast.co.za/eng/process" method="POST">';
    foreach ($data as $name => $value) {
        echo "<input type='hidden' name='$name' value='" . htmlspecialchars($value, ENT_QUOTES) . "'>";
    }
    echo '</form>';
    echo '<script>document.getElementById("payfast_form").submit();</script>';
    exit();
} else {
    $_SESSION['message'] = "Invalid order request.";
    header("Location: ../checkout.php");
    exit();
}
?>
