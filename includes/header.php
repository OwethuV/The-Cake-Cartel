<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Cake Cartel</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0.2rem;
            box-sizing: border-box;
        }

        body,
        html {
            scroll-behavior: smooth;
            font-family: 'Poppins', 'Quicksand', sans-serif;
        }

        .underline {
            text-decoration: underline;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #ff7e8a;
            padding: 10px 20px;
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 10;
        }

        .navbar-left {
            display: flex;
            align-items: center;
        }

        .logo {
            width: 120px;
            height: 120px;
            margin-right: 0.5rem;
        }

        .navbar-right {
            display: flex;
            gap: 1rem;
        }

        .navbar-right a,
        .navbar-icons a {
            color: #000;
            text-decoration: none;
            padding: 0.5rem;
        }

        .hamburger {
            display: none;
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Announcement Bar */
        .announcement-bar {
            background: #111;
            /* dark background */
            color: #fff;
            /* white text */
            font-size: 14px;
            text-align: center;
            overflow: hidden;
            height: 40px;
            /* height of the bar */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .announcement-wrapper {
            animation: moveUp 5s linear infinite;
        }

        @keyframes moveUp {
            0% {
                transform: translateY(100%);
                /* start below */
                opacity: 0;
            }

            10% {
                transform: translateY(0);
                /* visible */
                opacity: 1;
            }

            90% {
                transform: translateY(0);
                /* stay visible */
                opacity: 1;
            }

            100% {
                transform: translateY(-100%);
                /* move up & disappear */
                opacity: 0;
            }
        }

        /* Responsive adjustments */
        @media (max-width: 600px) {
            .announcement-bar {
                font-size: 12px;
                height: 30px;
                padding: 0 5px;
            }
        }
    </style>
</head>

<body>

    <!-- Announcement Bar -->
    <div class="announcement-bar">
        <div class="announcement-wrapper">
            <p>🚚 Free Delivery when bill is over R 700.00</p>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php"><img src="img\logo-removebg-preview.png" alt="The Cake Cartel"
                    width="50" height="50"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">Products</a>
                    </li>
                    <li class="nav-item">

                        <a class="nav-link" href="cart.php"><i class="fas fa-shopping-cart"></i>Cart</a>
                    </li>
                    <?php if (isset($_SESSION['userId'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="order_history.php">Order History</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php"><i class="fas fa-user"></i>My Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="php/logout.php"
                                onclick="return confirm('Are you sure you want to log out?');">Logout
                                (<?php echo htmlspecialchars($_SESSION['userName']); ?>)
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php"><i class="fas fa-user"></i>Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Register</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php"><i class="fas fa-phone"></i>Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main class="container mt-4">