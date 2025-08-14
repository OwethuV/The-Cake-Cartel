<?php
session_start();
include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | The Cake Cartel</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@400;500;600&family=Quicksand:wght@500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #fff9f6;
            font-family: 'Poppins', 'Quicksand', sans-serif;
        }

        .bakery-contact-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .bakery-contact-container {
            width: 100%;
            max-width: 1200px;
        }

        .bakery-contact-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(251, 176, 166, 0.15);
            overflow: hidden;
            padding: 30px;
            margin: 0 auto;
        }

        .contact-header {
            margin-bottom: 30px;
            text-align: center;
        }

        .contact-main-title {
            font-family: 'Pacifico', cursive;
            color: #ff7e8a;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .contact-subtitle {
            font-size: 1rem;
            color: #a38b82;
            max-width: 500px;
            margin: 0 auto 20px;
        }

        .divider {
            height: 30px;
            margin: 15px 0;
            text-align: center;
        }

        .contact-content-wrapper {
            margin-top: 30px;
        }

        .contact-info-box {
            background: #fff9f6;
            border-radius: 12px;
            padding: 20px;
            height: 100%;
        }

        .info-item {
            display: flex;
            margin-bottom: 20px;
            align-items: flex-start;
        }

        .info-icon {
            background: #ffd6dd;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            flex-shrink: 0;
            color: #ff7e8a;
        }

        .material-icons {
            font-size: 20px;
        }

        .info-content h3 {
            font-size: 1.1rem;
            color: #5a4a42;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .info-content p {
            color: #8a7369;
            margin: 0;
            line-height: 1.5;
            font-size: 0.95rem;
        }

        .info-content a {
            color: #ff7e8a;
            text-decoration: none;
            transition: all 0.3s;
        }

        .info-content a:hover {
            color: #ff5a6a;
            text-decoration: underline;
        }

        .contact-form-box {
            background: white;
            padding: 25px;
            border-radius: 12px;
            height: 100%;
        }

        .form-title {
            color: #5a4a42;
            font-size: 1.6rem;
            margin-bottom: 20px;
            font-weight: 600;
            font-family: 'Quicksand', sans-serif;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: #5a4a42;
            font-size: 0.95rem;
        }

        .form-control {
            border: 1px solid #f0e6e0;
            border-radius: 8px;
            padding: 10px 12px;
            width: 100%;
            transition: all 0.3s;
            background: #fff9f6;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: #ffb6c1;
            box-shadow: 0 0 0 3px rgba(255, 182, 193, 0.2);
            background: white;
        }

        .form-control::placeholder {
            color: #c4b5ac;
        }

        textarea.form-control {
            min-height: 120px;
        }

        .btn-send {
            background: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%);
            border: none;
            color: white;
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(255, 154, 158, 0.3);
            font-size: 0.9rem;
            cursor: pointer;
        }

        .btn-send:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 154, 158, 0.4);
        }

        .btn-send i {
            margin-right: 6px;
        }

        @media (max-width: 992px) {
            .bakery-contact-card {
                padding: 25px;
            }

            .contact-info-box,
            .contact-form-box {
                padding: 20px;
            }
        }

        @media (max-width: 768px) {
            .contact-main-title {
                font-size: 2rem;
            }

            .bakery-contact-wrapper {
                padding: 30px 15px;
            }

            .info-item {
                margin-bottom: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="bakery-contact-wrapper">
        <div class="bakery-contact-container">
            <div class="bakery-contact-card">
                <div class="contact-header">
                    <h1 class="contact-main-title mt-2">Contact Us</h1>
                    <p class="contact-subtitle">Have a question, feedback or a special request? We'd love to hear from
                        you!</p>
                </div>
                <!-- Display the session message -->
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-info" style="text-align: center; margin-bottom: 20px;">
                        <?php
                        echo $_SESSION['message'];
                        unset($_SESSION['message']); // Clear the message after displaying it
                        ?>
                    </div>
                <?php endif; ?>
                <div class="contact-content-wrapper">
                    <div class="row" style="display: flex; flex-wrap: wrap; margin: -15px;">
                        <div class="col-lg-5" style="padding: 15px; flex: 0 0 41.666667%; max-width: 41.666667%;">
                            <div class="contact-info-box">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <span class="material-icons">location_on</span>
                                    </div>
                                    <div class="info-content">
                                        <h3>Our Bakery</h3>
                                        <p>The Cake Cartel<br>
                                            1234 Sweet Street<br>
                                            Cape Town, 7780
                                        </p>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon">
                                        <span class="material-icons">phone</span>
                                    </div>
                                    <div class="info-content">
                                        <h3>Call Us</h3>
                                        <p><a href="tel:+1234567890">+27 67 404 0090</a></p>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon">
                                        <span class="material-icons">email</span>
                                    </div>
                                    <div class="info-content">
                                        <h3>Email Us</h3>
                                        <p><a href="mailto:hello@cakecartel.com">hello@cakecartel.com</a></p>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon">
                                        <span class="material-icons">schedule</span>
                                    </div>
                                    <div class="info-content">
                                        <h3>Hours</h3>
                                        <p>Monday-Friday: 8am-9pm<br>
                                            Saturday: 9am-9pm<br>
                                            Sunday: 9am-6pm</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-7" style="padding: 15px; flex: 0 0 58.333333%; max-width: 58.333333%;">
                            <div class="contact-form-box">
                                <h2 class="form-title">Send a Message</h2>
                                <form action="php/send_contact.php" method="POST">
                                    <div class="row" style="display: flex; flex-wrap: wrap; margin: -8px;">
                                        <div class="col-md-6" style="padding: 8px; flex: 0 0 50%; max-width: 50%;">
                                            <div class="form-group">
                                                <label for="contactName">Your Name</label>
                                                <input type="text" class="form-control" id="contactName"
                                                    name="contactName" placeholder="e.g. Ruth N'zola" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6" style="padding: 8px; flex: 0 0 50%; max-width: 50%;">
                                            <div class="form-group">
                                                <label for="contactEmail">Your Email</label>
                                                <input type="email" class="form-control" id="contactEmail"
                                                    name="contactEmail" placeholder="e.g. ruth@example.com" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="contactSubject">Subject</label>
                                        <input type="text" class="form-control" id="contactSubject"
                                            name="contactSubject" placeholder="Subject" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="contactMessage">Your Message</label>
                                        <textarea class="form-control" id="contactMessage" name="contactMessage"
                                            placeholder="Type your message here..." required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-send">
                                        <i class="fas fa-paper-plane"></i> Send Message
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <?php
    include 'includes/footer.php';
    ?>