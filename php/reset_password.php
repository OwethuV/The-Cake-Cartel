<?php
session_start();
include '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if the token is valid
    $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($email);
        $stmt->fetch();

        // Update the user's password
        $stmt = $conn->prepare("UPDATE USERS SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $password, $email);
        $stmt->execute();

        // Delete the token from the database
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();

        $_SESSION['message'] = "Your password has been reset successfully!";
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['message'] = "This token is invalid.";
        header("Location: login.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
