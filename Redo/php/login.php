<?php
session_start();
include '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT userId, name, password FROM USERS WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($userId, $userName, $hashed_password);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
        $_SESSION['userId'] = $userId;
        $_SESSION['userName'] = $userName;
        $_SESSION['message'] = "Login successful!";
        header("Location: ../index.php"); // Redirecting to homepage
    } else {
        $_SESSION['message'] = "Invalid email or password.";
        header("Location: ../login.php");
    }
    $stmt->close();
    $conn->close();
} else {
    header("Location: ../login.php");
    exit();
}
?>
