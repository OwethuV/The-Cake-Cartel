<?php
session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['userId'])) {
    $_SESSION['message'] = "Please login to place an order.";
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userId = $_SESSION['userId'];
    $deliveryMethod = $_POST['deliveryMethod'];
    $totalCartValue = floatval($_POST['totalCartValue']);
    $cartIds = $_POST['cartIds'];

    $deliveryPrice = 0;
    $address = null;

    if ($deliveryMethod === 'delivery') {
        if ($totalCartValue < 700) {
            $deliveryPrice = 5.00;
        }
        $address = trim($_POST['address']);
    }

    $totalPrice = $totalCartValue + $deliveryPrice;

    $conn->begin_transaction();
    $success = true;

    $stmt = $conn->prepare("INSERT INTO ORDERS (userId, deliveryPrice, totalPrice, status, address) VALUES (?, ?, ?, 'Pending', ?)");
    $stmt->bind_param("idds", $userId, $deliveryPrice, $totalPrice, $address);

    if (!$stmt->execute()) {
        $success = false;
    } else {
        $orderId = $stmt->insert_id;

        foreach ($cartIds as $cartId) {
            $itemStmt = $conn->prepare("SELECT productId, quantity, cartPrice FROM CART WHERE cartId = ? AND userId = ?");
            $itemStmt->bind_param("ii", $cartId, $userId);
            $itemStmt->execute();
            $itemResult = $itemStmt->get_result();

            if ($itemResult->num_rows > 0) {
                $item = $itemResult->fetch_assoc();
                $productId = $item['productId'];
                $quantity = $item['quantity'];
                $price = $item['cartPrice'];

                $orderItemStmt = $conn->prepare("INSERT INTO ORDER_ITEMS (orderId, productId, quantity, price) VALUES (?, ?, ?, ?)");
                $orderItemStmt->bind_param("iiid", $orderId, $productId, $quantity, $price);

                if (!$orderItemStmt->execute()) {
                    $success = false;
                    break;
                }
                $orderItemStmt->close();
            }
            $itemStmt->close();
        }

        if ($success) {
            $placeholders = implode(',', array_fill(0, count($cartIds), '?'));
            $deleteStmt = $conn->prepare("DELETE FROM CART WHERE userId = ? AND cartId IN ($placeholders)");
            $types = str_repeat('i', count($cartIds) + 1);
            $params = array_merge([$userId], $cartIds);
            $deleteStmt->bind_param($types, ...$params);
            if (!$deleteStmt->execute()) $success = false;
            $deleteStmt->close();
        }
    }
    $stmt->close();

    if ($success) {
        $conn->commit();

        // Generate PayFast payment form
        $pfData = [
            'merchant_id' => $_ENV['PAYFAST_MERCHANT_ID'],
            'merchant_key' => $_ENV['PAYFAST_MERCHANT_KEY'],
            'return_url' => $_ENV['PAYFAST_RETURN_URL'],
            'cancel_url' => $_ENV['PAYFAST_CANCEL_URL'],
            'notify_url' => $_ENV['PAYFAST_NOTIFY_URL'],
            'amount' => number_format($totalPrice, 2, '.', ''),
            'item_name' => "Order #" . $orderId
        ];

        $queryString = http_build_query($pfData);
        $signature = md5($queryString . '&passphrase=' . $_ENV['PAYFAST_PASSPHRASE']);
        $pfData['signature'] = $signature;

        echo '<form action="https://www.payfast.co.za/eng/process" method="post" name="payfastForm">';
        foreach ($pfData as $key => $val) {
            echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($val) . '">';
        }
        echo '<p>Redirecting to payment...</p>';
        echo '<script>document.payfastForm.submit();</script>';
        echo '</form>';

    } else {
        $conn->rollback();
        $_SESSION['message'] = "Order failed.";
        header("Location: ../checkout.php");
    }

    $conn->close();
    exit();
}

$_SESSION['message'] = "Invalid request.";
header("Location: ../cart.php");
exit();
