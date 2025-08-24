<?php
session_start();

//CSRF Validation
if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    $_SESSION['message'] = "Security token mismatch. Please reload the page.";
    header("Location: ../contact.php");
    exit();
}

require "../vendor/autoload.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim(htmlspecialchars($_POST['contactName']));
    $email = trim(htmlspecialchars($_POST['contactEmail']));
    $subject = trim(htmlspecialchars($_POST['contactSubject']));
    $message = trim(htmlspecialchars($_POST['contactMessage']));
    // Validate inputs
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $_SESSION['message'] = "All fields are required.";
        header("Location: ../contact.php");
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Invalid email format.";
        header("Location: ../contact.php");
        exit();
    }
    // Limit the length of inputs
    if (strlen($name) > 100 || strlen($subject) > 100 || strlen($message) > 500) {
        $_SESSION['message'] = "Input exceeds maximum length.";
        header("Location: ../contact.php");
        exit();
    }

    $mail = new PHPMailer(true);
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->SMTPAuth = true;

    $mail->Host = $_ENV['SMTP_HOST'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $_ENV['SMTP_PORT'];

    $mail->Username = $_ENV['SMTP_USER'];
    $mail->Password = $_ENV['SMTP_PASS'];

    $mail->setFrom($email, $name);
    $mail->addAddress("thecakecartel2025@gmail.com"); // Add your email address here

    $mail->Subject = $subject; // Set the subject
    $mail->isHTML(true); // Set email format to HTML

    $email_body = "
    <html>
    <head>
        <title>New Contact Form Submission</title>
    </head>
    <body>
        <h2>The Cake Cartel Form Submission</h2>
        <p><strong>Name:</strong> {$name}</p>
        <p><strong>Email:</strong> {$email}</p>
        <p><strong>Subject:</strong> {$subject}</p> 
        <p><strong>Message:</strong><br>{$message}</p>
    </body>
    </html>
    ";

    $mail->Body = $email_body; // Set the email body

    // Attempt to send the email
    if ($mail->send()) {
        $_SESSION['message'] = "Your message has been sent successfully!";
        error_log("Message set: " . $_SESSION['message']); // Debugging line
    } else {
        $_SESSION['message'] = "Failed to send your message. Please try again later.";
        error_log("Message set: " . $_SESSION['message']); // Debugging line
    }
    header("Location: ../contact.php");
    exit();
} else {
    header("Location: ../contact.php");
    exit();
}
