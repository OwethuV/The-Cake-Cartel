<?php
session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['userId'])) {
    $_SESSION['message'] = "Please login to add items to your cart.";
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userId = $_SESSION['userId'];

    // Verify user exists
    $checkUser = $conn->prepare("SELECT userId FROM USERS WHERE userId = ?");
    $checkUser->bind_param("i", $userId);
    $checkUser->execute();
    $checkUser->store_result();
    if ($checkUser->num_rows === 0) {
        $_SESSION['message'] = "User account not found.";
        header("Location: ../login.php");
        exit();
    }
    $checkUser->close();

    // Validate input
    $productId = isset($_POST['productId']) ? (int)$_POST['productId'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0.0;

    if ($productId <= 0 || $quantity <= 0 || $price <= 0) {
        $_SESSION['message'] = "Invalid input data.";
        header("Location: ../products.php");
        exit();
    }

    $cartPrice = $quantity * $price;

    // Check if product already exists in user's cart
    $stmt = $conn->prepare("SELECT cartId, quantity, cartPrice FROM CART WHERE userId = ? AND productId = ?");
    $stmt->bind_param("ii", $userId, $productId);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($cartId, $existingQuantity, $existingCartPrice);
    $stmt->fetch();

    if ($stmt->num_rows > 0) {
        // Update existing cart item
        $newQuantity = $existingQuantity + $quantity;
        $newCartPrice = $existingCartPrice + $cartPrice;
        $update_stmt = $conn->prepare("UPDATE CART SET quantity = ?, cartPrice = ? WHERE cartId = ?");
        $update_stmt->bind_param("idi", $newQuantity, $newCartPrice, $cartId);
        if ($update_stmt->execute()) {
            $_SESSION['message'] = "Product quantity updated in cart!";
        } else {
            $_SESSION['message'] = "Error updating cart: " . $update_stmt->error;
        }
        $update_stmt->close();
    } else {
        // Add new item to cart
        $insert_stmt = $conn->prepare("INSERT INTO CART (userId, productId, quantity, cartPrice) VALUES (?, ?, ?, ?)");
        $insert_stmt->bind_param("iiid", $userId, $productId, $quantity, $cartPrice);
        if ($insert_stmt->execute()) {
            $_SESSION['message'] = "Product added to cart!";
        } else {
            $_SESSION['message'] = "Error adding to cart: " . $insert_stmt->error;
        }
        $insert_stmt->close();
    }

    $stmt->close();
    $conn->close();

    header("Location: ../products.php");
    exit();
} else {
    header("Location: ../index.php");
    exit();
}
