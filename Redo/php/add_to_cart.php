<?php
session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['userId'])) {
    $_SESSION['message'] = "Please login to add items to your cart.";
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['userId'];
    $productId = $_POST['productId'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price']; // Price of a single product

    // Calculating total price for this item
    $cartPrice = $quantity * $price;

    // Check if the product is already in the cart for this user
    $stmt = $conn->prepare("SELECT cartId, quantity, cartPrice FROM CART WHERE userId = ? AND productId = ?");
    $stmt->bind_param("ii", $userId, $productId);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($cartId, $existingQuantity, $existingCartPrice);
    $stmt->fetch();

    if ($stmt->num_rows > 0) {
        // UPDATE existing cart item
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

    header("Location: ../products.php"); // Redirecting back to product page
    exit();
} else {
    header("Location: ../index.php"); // Redirecting if accessed directly
    exit();
}