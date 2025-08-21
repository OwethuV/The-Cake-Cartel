<?php
// Start session on every page that needs it
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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
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
            background-color: #FF7E8A;
            padding: 10px 20px;
            color: #fff;
            position: fixed;
            /* Keep navbar at the top of the page */
            top: 0;
            /* Position navbar at the top */
            left: 0;
            /* Position navbar at the left */
            width: 100%;
            /* Make navbar full width */
            z-index: 10;
            /* Ensure navbar is above other elements */
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
    </style>
</head>

<body>
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
                        <a class="nav-link active" aria-current="page" href="index.php"><i class="fa fa-home"
                                aria-hidden="true"></i>Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php"><i class="fa fa-shopping-basket"
                                aria-hidden="true"></i>Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php"><i class="fas fa-shopping-cart"></i>Cart</a>
                    </li>
                    <?php if (isset($_SESSION['userId'])): ?>
                        <!-- <li class="nav-item">
                            <a class="nav-link" href="order_history.php"><i class="fa fa-list-alt"
                                    aria-hidden="true"></i>Order History</a>
                        </li> -->
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php"><i class="fas fa-user"></i>My Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="php/logout.php"
                                onclick="return confirm('Are you sure you want to log out?');"><i class="fa fa-sign-out"
                                    aria-hidden="true"></i>Logout
                                (<?php echo htmlspecialchars($_SESSION['userName']); ?>)
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php"><i class="fas fa-user"></i>Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php"><i class="fa fa-user-plus"
                                    aria-hidden="true"></i>Register</a>
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