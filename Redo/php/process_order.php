<?php
session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['userId'])) {
    $_SESSION['message'] = "Please login to place an order.";
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cartIds'])) {
    $userId = $_SESSION['userId'];
    $cartIds = $_POST['cartIds']; // Array of cartIds
    $deliveryPrice = $_POST['deliveryPrice'];
    $totalPrice = $_POST['totalPrice'];

    // Start a transaction
    $conn->begin_transaction();
    $order_success = true;

    // Insert into ORDERS table
    $stmt = $conn->prepare("INSERT INTO ORDERS (userId, deliveryPrice, totalPrice, status) VALUES (?, ?, ?, 'Pending')");
    $stmt->bind_param("idd", $userId, $deliveryPrice, $totalPrice);
    
    if (!$stmt->execute()) {
        $order_success = false;
        $_SESSION['message'] = "Error placing order: " . $stmt->error;
    } else {
        $orderId = $stmt->insert_id; // Get the last inserted order ID

        // Now insert each cart item into ORDER_ITEMS
        foreach ($cartIds as $cartId) {
            // Fetch product details from CART
            $item_stmt = $conn->prepare("SELECT productId, quantity, cartPrice FROM CART WHERE cartId = ? AND userId = ?");
            $item_stmt->bind_param("ii", $cartId, $userId);
            $item_stmt->execute();
            $item_result = $item_stmt->get_result();

            if ($item_result->num_rows > 0) {
                $item = $item_result->fetch_assoc();
                $productId = $item['productId'];
                $quantity = $item['quantity'];
                $price = $item['cartPrice'];

                // Insert into ORDER_ITEMS table
                $order_item_stmt = $conn->prepare("INSERT INTO ORDER_ITEMS (orderId, productId, quantity, price) VALUES (?, ?, ?, ?)");
                $order_item_stmt->bind_param("iiid", $orderId, $productId, $quantity, $price);
                
                if (!$order_item_stmt->execute()) {
                    $order_success = false;
                    $_SESSION['message'] = "Error adding item to order: " . $order_item_stmt->error;
                    break;
                }
                $order_item_stmt->close();
            }
            $item_stmt->close();
        }

        // After successful order, remove items from CART
        if ($order_success) {
            $delete_stmt = $conn->prepare("DELETE FROM CART WHERE userId = ? AND cartId IN (" . implode(',', array_fill(0, count($cartIds), '?')) . ")");
            $delete_stmt->bind_param(str_repeat('i', count($cartIds)), ...$cartIds);
            if (!$delete_stmt->execute()) {
                $order_success = false;
                $_SESSION['message'] = "Error clearing cart items: " . $delete_stmt->error;
            }
            $delete_stmt->close();
        }
    }
    $stmt->close();

    if ($order_success) {
        $conn->commit();
        $_SESSION['message'] = "Your order has been placed successfully!";
        header("Location: ../products.php"); // Redirect to products page
    } else {
        $conn->rollback();
        header("Location: ../checkout.php"); // Redirect back to checkout with error
    }

    $conn->close();
    exit();

} else {
    $_SESSION['message'] = "Invalid request to process order.";
    header("Location: ../cart.php");
    exit();
}