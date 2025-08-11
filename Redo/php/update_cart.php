<?php
session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['userId'])) {
    $_SESSION['message'] = "Please login to manage your cart.";
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cartId'], $_POST['quantity'], $_POST['unitPrice'])) {
    $cartId = $_POST['cartId'];
    $newQuantity = max(1, intval($_POST['quantity'])); // Ensuring quantity is at least 1
    $unitPrice = $_POST['unitPrice'];
    $userId = $_SESSION['userId'];

    $newCartPrice = $newQuantity * $unitPrice;

    $stmt = $conn->prepare("UPDATE CART SET quantity = ?, cartPrice = ? WHERE cartId = ? AND userId = ?");
    $stmt->bind_param("iidi", $newQuantity, $newCartPrice, $cartId, $userId);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $_SESSION['message'] = "Cart quantity updated successfully!";
        } else {
            $_SESSION['message'] = "No changes made or item not found in your cart.";
        }
    } else {
        $_SESSION['message'] = "Error updating cart: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();

    header("Location: ../cart.php");
    exit();
} else {
    $_SESSION['message'] = "Invalid request to update cart.";
    header("Location: ../cart.php");
    exit();
}
?>
