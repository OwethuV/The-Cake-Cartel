<?php
session_start();
require '../vendor/autoload.php'; // Ensure you have PHPMailer installed
include '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT userId, name FROM USERS WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userId, $userName);
        $stmt->fetch();

        // Generate a unique token for password reset
        $token = bin2hex(random_bytes(50));
        $expires = date("U") + 3600; // Token expires in 1 hour

        // Store the token in the database
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $email, $token, $expires);
        $stmt->execute();

        // Send the email
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = "owethu.valantiya@gmail.com";
        $mail->Password = "zxad jajd loql jbzr";
        $mail->Port = 587;

        $mail->setFrom('your_email@gmail.com', 'The Cake Cartel');
        $mail->addAddress($email);
        $mail->Subject = 'Password Reset Request';
        $mail->isHTML(true);
        $mail->Body = "Hello $userName,<br><br>To reset your password, please click the link below:<br>
                       <a href='http://localhost/redo/reset_password.php?token=$token'>Reset Password</a><br><br>
                       If you did not request a password reset, please ignore this email.";

        if ($mail->send()) {
            $_SESSION['message'] = "A password reset link has been sent to your email.";
        } else {
            $_SESSION['message'] = "Failed to send email. Please try again.";
        }
    } else {
        $_SESSION['message'] = "No account found with that email address.";
    }

    header("Location: ../forgot_password.php");
    exit();
} else {
    header("Location: ../forgot_password.php");
    exit();
}
