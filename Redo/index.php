<?php
session_start();
include 'includes/header.php';
include 'includes/db_connect.php';



// Fetch a limited number of products for the homepage
$sql = "SELECT * FROM PRODUCTS ORDER BY createdAt DESC LIMIT 6"; // Show 6 latest products
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Cake Cartel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css\style.css">
    <style>
        * {
            margin: 0;
            padding: 0.2rem;
        }

        body {
            scroll-behavior: smooth;
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
        }

        .header {
            height: 90vh;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding-right: 4vw;
            color: white;
            overflow: hidden;
            margin-top: auto;
        }

        .header-btn {
            border: none;
            padding: 15px;
            margin-left: 10%;
            border-radius: 30px;
        }

        .header-btn:hover {
            background-color: #ff7e8a;
            color: white;
            text-decoration: none;
            cursor: pointer;
        }

        .background-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 1;
            filter: brightness(0.4);
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: 2;
        }

        .content {
            position: relative;
            z-index: 3;
            margin-left: 5%;
            max-width: 700px;
        }

        .header h1 {
            font-size: 60px;
            margin-left: 40px;
            margin-bottom: 0.5rem;
        }

        .header p {
            font-size: 1.75rem;
            font-style: italic;
            margin-left: 40px;
        }

        .about-page-section {
            background-color: #fff;
            padding: 4rem 2rem;
        }

        .about-content {
            display: flex;
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
        }

        .about-text {
            flex: 1 1 500px;
            max-width: 600px;
        }

        .about-text h2 {
            font-size: 2.5rem;
            color: #ff7e8a;
            margin-bottom: 1rem;
        }

        .about-text p {
            font-size: 1.1rem;
            line-height: 1.6;
            color: #444;
            margin-bottom: 1rem;
        }

        .about-video {
            flex: 1 1 500px;
            max-width: 600px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .about-video video {
            width: 100%;
            height: auto;
            display: block;
            border-radius: 10px;
        }


        @media screen and (max-width: 768px) {
            .about-content {
                flex-direction: column;
                text-align: center;
            }

            .about-text h2 {
                font-size: 2rem;
            }

            .about-text p {
                font-size: 1rem;
            }

            .about-video {
                margin-top: 1.5rem;
            }
        }

        @media screen and (max-width: 480px) {
            .about-text h2 {
                font-size: 1.7rem;
            }

            .about-text p {
                font-size: 0.95rem;
            }
        }


        .menu-preview {
            background: #fff;
            padding: 4rem 2rem;
            position: relative;
        }

        .menu-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .menu-header h2 {
            font-size: 2.5rem;
            color: #ff7e8a;
        }

        .shop-now-btn {
            background: #ff7e8a;
            color: #fff;
            padding: 0.7rem 1.5rem;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s;
        }

        .shop-now-btn:hover {
            background: #a9391e;
        }

        .menu-items {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .menu-card {
            width: 300px;
            background: #fff7f2;
            border-radius: 10px;
            padding: 1rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            opacity: 0;
            transform: translateY(40px);
            transition: all 1s ease;
        }

        .menu-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .menu-card h3 {
            color: #ff7e8a;
            margin-bottom: 0.5rem;
        }

        .menu-card p {
            font-size: 0.95rem;
            color: #555;
        }

        .full-menu-btn-container {
            text-align: center;
            margin-top: 3rem;
        }

        .full-menu-btn {
            background: #333;
            color: white;
            padding: 0.75rem 2rem;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background 0.3s ease;
        }

        .full-menu-btn:hover {
            background: #111;
        }


        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }


        .reviews-section {
            padding: 2rem 1rem;
            background-color: #fff7f2;
            text-align: center;
        }

        .reviews-section h1 {
            color: #ff7e8a;
            margin-bottom: 1.5rem;
        }

        .reviews-scroller {
            overflow: hidden;
            position: relative;
        }

        .reviews-track {
            display: flex;
            gap: 1.5rem;
            animation: scrollReviews 60s linear infinite;
        }

        .review-box {
            flex: 0 0 300px;
            background: #fff;
            padding: 1rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: left;
        }

        .review-box h3 {
            margin-bottom: 0.5rem;
            color: #ff7e8a;
        }

        .review-box p {
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .stars {
            color: #FFD700;
            margin-bottom: 0.5rem;
        }

        @keyframes scrollReviews {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-100%);
            }
        }


        .delivery-section {
            background-color: #fff7f2;
            padding: 4rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            margin: none;
        }

        .delivery-section h2 {
            font-size: 2.5rem;
            color: #ff7e8a;
            margin-bottom: 1rem;
        }

        .delivery-message {
            font-size: 1.2rem;
            color: #444;
            margin-bottom: 3rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .truck-wrapper {
            position: relative;
            height: 100px;
            overflow: hidden;
        }

        .moving-truck {
            font-size: 3rem;
            color: #ff7e8a;
            position: absolute;
            top: 0;
            left: -10%;
            animation: drive 8s linear infinite;
        }

        .delay-1 {
            animation-delay: 2s;
        }

        .delay-2 {
            animation-delay: 4s;
        }

        .delay-3 {
            animation-delay: 6s;
        }

        .delay-4 {
            animation-delay: 8s;
        }

        @keyframes drive {
            0% {
                left: -10%;
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                left: 110%;
                transform: translateY(0);
            }
        }


        @media screen and (max-width: 768px) {
            .delivery-section h2 {
                font-size: 2rem;
            }

            .delivery-message {
                font-size: 1rem;
                padding: 0 1rem;
            }

            .moving-truck {
                font-size: 2.2rem;
            }

            .truck-wrapper {
                height: 80px;
            }
        }

        @media screen and (max-width: 480px) {
            .moving-truck {
                font-size: 1.8rem;
            }

            .truck-wrapper {
                height: 60px;
            }
        }



        .map-section {
            padding: 4rem 2rem;
            background-color: #fff;
            text-align: center;
        }

        .map-section h2 {
            font-size: 2.5rem;
            color: #ff7e8a;
            margin-bottom: 1rem;
        }

        .map-section p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            color: #333;
        }

        .map-container {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .map-container iframe {
            width: 100%;
            height: 450px;
            border: none;
        }


        @media screen and (max-width: 768px) {
            .map-section h2 {
                font-size: 1.8rem;
            }

            .map-section p {
                font-size: 1rem;
            }

            .map-container iframe {
                height: 300px;
            }
        }

        @media screen and (max-width: 480px) {
            .map-container iframe {
                height: 250px;
            }
        }



        .site-footer {
            background-color: #ede4d5;
            color: black;
            padding: 3rem 2rem 1rem;
            font-size: 0.95rem;
            margin: 0;
            width: 100%;
            position: relative;
        }

        .footer-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            bottom: 0;
            left: 0;
        }

        .footer-left {
            flex: 1 1 250px;
        }

        .footer-logo {
            width: 80px;
            height: auto;
            margin-bottom: 1rem;
            border-radius: 10px;
        }

        .footer-left h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .footer-right {
            flex: 2 1 600px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .footer-links {
            min-width: 150px;
            margin-bottom: 1.5rem;
        }

        .footer-links h4 {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .footer-links ul {
            list-style: none;
            padding: 0;
        }

        .footer-links ul li {
            margin-bottom: 0.5rem;
        }

        .footer-links ul li a {
            color: black;
            text-decoration: none;
            transition: opacity 0.3s ease;
        }

        .footer-links ul li a:hover {
            opacity: 0.7;
        }

        .footer-socials h4 {
            margin-bottom: 0.5rem;
        }

        .social-icons {
            display: flex;
            gap: 1rem;
            font-size: 1.2rem;
        }

        .social-icons a {
            color: #000;
            transition: transform 0.2s ease;
        }

        .social-icons a:hover {
            transform: scale(1.2);
        }

        .footer-bottom {
            text-align: center;
            margin-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            padding-top: 1rem;
            font-size: 0.9rem;
        }


        /* Modal Overlay */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.6);
        }

        /* Modal Content */
        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 2rem;
            border-radius: 10px;
            width: 90%;
            max-width: 400px;
            position: relative;
        }

        /* Close Button */
        .modal .close {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 1.5rem;
            color: #aaa;
            cursor: pointer;
        }

        .modal .close:hover {
            color: #000;
        }

        /* Form */
        .auth-form {
            display: flex;
            flex-direction: column;
        }

        .auth-form h2 {
            margin-bottom: 1rem;
            color: #ff7e8a;
        }

        .auth-form input {
            margin-bottom: 1rem;
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .auth-form button {
            background-color: #cc4b2c;
            color: #fff;
            border: none;
            padding: 0.75rem;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
        }

        .auth-form button:hover {
            background-color: #ff7e8a;;
        }

        /* Tabs */
        .modal-tabs {
            display: flex;
            justify-content: space-around;
            margin-bottom: 1rem;
        }

        .modal-tabs button {
            background: none;
            border: none;
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer;
            color: #ff7e8a;;
            padding: 0.5rem 1rem;
            border-bottom: 2px solid transparent;
        }

        .modal-tabs button:hover,
        .modal-tabs button.active {
            border-color: #ff7e8a;;
        }

        /* Mobile */
        @media screen and (max-width: 480px) {
            .modal-content {
                margin: 20% auto;
                width: 90%;
            }

            .auth-form input,
            .auth-form button {
                font-size: 1rem;
            }
        }



        @media screen and (max-width: 768px) {
            .footer-container {
                flex-direction: column;
                align-items: flex-start;
            }

            .footer-right {
                flex-direction: column;
            }

            .footer-left,
            .footer-links,
            .footer-socials {
                width: 100%;
            }

            .footer-logo {
                width: 60px;
            }

            .social-icons {
                justify-content: flex-start;
            }
        }

        .whatsapp-float {
            position: fixed;
            bottom: 25px;
            right: 25px;
            background-color: #25D366;
            color: white;
            border-radius: 50%;
            padding: 15px;
            font-size: 24px;
            z-index: 1001;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s ease;

        }

        .whatsapp-float:hover {
            transform: scale(1.1);
        }


        @media screen and (max-width: 768px) {
            .navbar {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                padding: 20px;
            }

            .navbar-left h2 {
                font-size: 1.2rem;
            }

            .navbar-right {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 100px;
                right: 0;
                width: 100%;
                background-color: #333;
                padding: 1rem;
                z-index: 1000;
            }

            .navbar-right a {
                color: white;
                padding: 0.75rem;
            }

            .navbar-right.active {
                display: flex;
            }

            .navbar-icons {
                display: flex;
                gap: 1rem;
            }

            .hamburger {
                display: block;
                font-size: 1.5rem;
                cursor: pointer;
            }

            .navbar-end {
                display: flex;
                align-items: center;
                gap: 1rem;
            } 

            .menu-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .menu-header h2 {
                font-size: 2rem;
            }

            .shop-now-btn {
                align-self: flex-end;
            }

            .menu-items {
                flex-direction: column;
                align-items: center;
            }

            .menu-card {
                width: 90%;
            }

            .review-box {
                flex: 0 0 250px;
                font-size: 0.85rem;
            }

            .reviews-section h1 {
                font-size: 1.5rem;
            }

            .whatsapp-float {
                bottom: 15px;
                right: 15px;
                padding: 12px;
                font-size: 20px;
            }
        }
    </style>
</head>

<body>

    <section id="home" class="header">
        <video autoplay muted loop class="background-video">
            <source src="video\video.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <div class="overlay"></div>
        <div class="content">
            <h1>WELCOME TO THE CAKE CARTEL</h1>
            <p>Bite Into Bliss</p>
            <button class="header-btn">SHOP NOW</button>
        </div>
        <div class="img-side">
            <img src="images/header-22" alt="img/hero-bg.jpg">
        </div>
    </section>

    <section class="delivery-section">
        <h2>Free Delivery on Orders Over R700!</h2>
        <p class="delivery-message">
            Treat yourself to our delicious cakes — and if your order is worth <strong>R700.00 or more</strong>, we’ll
            deliver it to your doorstep at <strong>no extra cost</strong>!
        </p>
        <div class="truck-wrapper">
            <i class="fas fa-truck-moving moving-truck"></i>
            <i class="fas fa-truck-moving moving-truck delay-1"></i>
            <i class="fas fa-truck-moving moving-truck delay-2"></i>
            <i class="fas fa-truck-moving moving-truck delay-3"></i>
        </div>
    </section>

    <section id="menu-preview" class="menu-preview">
        <div class="menu-header">
            <h2>Our Cake Menu</h2>
            <a href="shop.html" class="shop-now-btn">Shop Now</a>
        </div>

        <div class="menu-items">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <div class="menu-card fade-in">
                        <img src="<?php echo htmlspecialchars($row['productImg'] ?: 'img/placeholder.jpg'); ?>"
                            alt="<?php echo htmlspecialchars($row['productName']); ?>">
                        <h3><?php echo htmlspecialchars($row['productName']); ?></h3>
                        <p><?php echo htmlspecialchars(substr($row['description'], 0, 70)) . (strlen($row['description']) > 70 ? '...' : ''); ?>
                        </p>
                    </div>
                    <?php
                }
            } else {
                echo "<p class='text-center'>No products found.</p>";
            }
            ?>
        </div>

        <div class="full-menu-btn-container">
            <a href="shop.html" class="full-menu-btn">View Full Menu</a>
        </div>
    </section>

    <section id="reviews" class="reviews-section">
        <h1>Customer Reviews</h1>
        <div class="reviews-scroller">
            <div class="reviews-track">
                <!-- Add review boxes here as needed -->
                <div class="review-box">
                    <h3>Samantha B.</h3>
                    <div class="stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p>The cupcakes were absolutely divine! Soft, moist, and the frosting was perfect.</p>
                </div>
                <div class="review-box">
                    <h3>Green Basket Supermarket</h3>
                    <div class="stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <p>We've been stocking their cakes and customers are obsessed!</p>
                </div>
                <div class="review-box">
                    <h3>Trevor M.</h3>
                    <div class="stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p>Bought a cake for my wife’s birthday — she said it was the best ever.</p>
                </div>
                <div class="review-box">
                    <h3>Sweet Spot Café</h3>
                    <div class="stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p>Perfect addition to our menu. Customers are loving it!</p>
                </div>
                <div class="review-box">
                    <h3>Leah P.</h3>
                    <div class="stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                        <i class="far fa-star"></i>
                    </div>
                    <p>Red velvet cupcakes were amazing! But they sold out fast 😅</p>
                </div>

                <div class="review-box">
                    <h3>Samantha B.</h3>
                    <div class="stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p>The cupcakes were absolutely divine! Soft, moist, and the frosting was perfect.</p>
                </div>
                <div class="review-box">
                    <h3>Green Basket Supermarket</h3>
                    <div class="stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <p>We've been stocking their cakes and customers are obsessed!</p>
                </div>
                <div class="review-box">
                    <h3>Trevor M.</h3>
                    <div class="stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p>Bought a cake for my wife’s birthday — she said it was the best ever.</p>
                </div>
                <div class="review-box">
                    <h3>Sweet Spot Café</h3>
                    <div class="stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p>Perfect addition to our menu. Customers are loving it!</p>
                </div>
                <div class="review-box">
                    <h3>Leah P.</h3>
                    <div class="stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                        <i class="far fa-star"></i>
                    </div>
                    <p>Red velvet cupcakes were amazing! But they sold out fast 😅</p>
                </div>
            </div>
        </div>
    </section>

    <section class="about-page-section" id="about">
        <div class="about-content">
            <div class="about-text">
                <h2>About The Cake Cartel</h2>
                <p>
                    At The Cake Cartel, we believe in baking more than just cakes — we bake experiences.
                    Our journey started in the heart of Cape Town CBD, blending traditional recipes with modern twists.
                    Every cake is handcrafted with love, using only the finest ingredients to ensure premium taste and
                    quality.
                </p>
                <p>
                    Whether you're celebrating a special moment or just treating yourself, we’re here to make it
                    unforgettable — one slice at a time.
                </p>
            </div>
            <div class="about-video">
                <video autoplay muted loop playsinline>
                    <source src="video/black-about.mp4" type="video/mp4">
                </video>
            </div>
        </div>
    </section>

    <section id="location" class="map-section">
        <h2>Visit Us in Cape Town CBD</h2>
        <p>We are located in the heart of Cape Town's city center. Come indulge in our delicious cakes!</p>
        <div class="map-container">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3309.9075128901214!2d18.418551!3d-33.924869!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1dcc676c7d6bff4f%3A0x2e5140ffb3252583!2sCape%20Town%20City%20Centre%2C%20Cape%20Town%2C%208000!5e0!3m2!1sen!2sza!4v1691416428239!5m2!1sen!2sza"
                width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </section>

    <footer class="site-footer footer" id="footer">
        <div class="footer-container">
            <div class="footer-left">
                <img src="img\logo-removebg-preview.png" alt="The Cake Cartel Logo" class="footer-logo">
                <h3>The Cake Cartel</h3>
                <p>Bite Into Bliss</p>
            </div>
            <div class="footer-right">
                <div class="footer-links">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="products.php">Shop</a></li>
                        <li><a href="contact.php">Contact</a></li>
                        <?php if (isset($_SESSION['userId'])): ?>
                            <li>
                                <a href="php/logout.php"
                                    onclick="return confirm('Are you sure you want to log out?');">Logout
                                    (<?php echo htmlspecialchars($_SESSION['userName']); ?>)
                                </a>
                            </li>
                        <?php else: ?>
                            <li>
                                <a href="login.php">Login</a>
                            </li>
                            <li>
                                <a href="register.php">Register</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Customer Service</h4>
                    <ul>
                        <li><a href="#">Shipping Info</a></li>
                        <li><a href="#">Returns</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="footer-socials">
                    <h4>Follow Us</h4>
                    <div class="social-icons">
                        <a href="https://facebook.com" target="_blank" aria-label="Facebook"><i
                                class="fab fa-facebook-f"></i></a>
                        <a href="https://tiktok.com" target="_blank" aria-label="TikTok"><i
                                class="fab fa-tiktok"></i></a>
                        <a href="https://instagram.com" target="_blank" aria-label="Instagram"><i
                                class="fab fa-instagram"></i></a>
                        <a href="https://wa.me/1234567890" target="_blank" aria-label="WhatsApp"><i
                                class="fab fa-whatsapp"></i></a>
                        <a href="mailto:thecakecartel@gmail.com" aria-label="Email"><i class="fas fa-envelope"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 The Cake Cartel. All rights reserved.</p>
        </div>
    </footer>

    <a href="https://wa.me/1234567890" class="whatsapp-float" target="_blank" aria-label="Chat on WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <script src="js/index.js"></script>
</body>

</html>