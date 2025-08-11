<?php
session_start();
include '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $cell = $_POST['cell'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $_SESSION['message'] = "Passwords do not match.";
        header("Location: ../register.php");
        exit();
    }

    // Hashing the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Checking if email already exists
    $stmt = $conn->prepare("SELECT userId FROM USERS WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['message'] = "Email already registered. Please use a different email or login.";
        header("Location: ../register.php");
        exit();
    }
    $stmt->close();

    // Inserting the new user into database
    $stmt = $conn->prepare("INSERT INTO USERS (name, email, cell, address, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $cell, $address, $hashed_password);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Registration successful! You can now login.";
        header("Location: ../login.php");
    } else {
        $_SESSION['message'] = "Error: " . $stmt->error;
        header("Location: ../register.php");
    }
    $stmt->close();
    $conn->close();
} else {
    header("Location: ../register.php"); // Redirecting if accessed directly
    exit();
}
?>
