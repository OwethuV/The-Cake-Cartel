<?php
session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['userId'])) {
    $_SESSION['message'] = "Please login to manage your cart.";
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cartId'])) {
    $cartId = $_POST['cartId'];
    $userId = $_SESSION['userId']; // Ensuring that the user owns the cart item

    $stmt = $conn->prepare("DELETE FROM CART WHERE cartId = ? AND userId = ?");
    $stmt->bind_param("ii", $cartId, $userId);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $_SESSION['message'] = "Item removed from cart.";
        } else {
            $_SESSION['message'] = "Item not found or it has already been removed.";
        }
    } else {
        $_SESSION['message'] = "Error removing item: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();

    header("Location: ../cart.php");
    exit();
} else {
    header("Location: ../cart.php"); // Redirecting if accessed directly or cartId not set
    exit();
}