<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['contactName']);
    $email = htmlspecialchars($_POST['contactEmail']);
    $subject = htmlspecialchars($_POST['contactSubject']);
    $message = htmlspecialchars($_POST['contactMessage']);

    $to = "outisvalantiya@gmail.com"; // **CHANGE THIS TO YOUR ACTUAL EMAIL ADDRESS**
    $headers = "From: " . $name . " <" . $email . ">\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

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

    // Use mail() function (requires PHP mail configuration on your server)
    if (mail($to, $subject, $email_body, $headers)) {
        $_SESSION['message'] = "Your message has been sent successfully!";
    } else {
        $_SESSION['message'] = "Failed to send your message. Please try again later.";
    }

    header("Location: ../contact.php");
    exit();
} else {
    header("Location: ../contact.php");
    exit();
}
?>
