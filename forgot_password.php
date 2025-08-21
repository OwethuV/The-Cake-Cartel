<?php
include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | The Cake Cartel</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@400;500;600&family=Quicksand:wght@500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #FFF9F6;
            font-family: 'Poppins', 'Quicksand', sans-serif;
        }

        .bakery-forgot-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .bakery-forgot-container {
            width: 100%;
            max-width: 600px;
        }

        .bakery-forgot-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(251, 176, 166, 0.15);
            overflow: hidden;
            padding: 40px;
            margin: 0 auto;
        }

        .forgot-header {
            margin-bottom: 30px;
            text-align: center;
        }

        .forgot-main-title {
            font-family: 'Pacifico', cursive;
            color: #FF7E8A;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .forgot-subtitle {
            font-size: 1rem;
            color: #A38B82;
            max-width: 500px;
            margin: 0 auto 20px;
        }

        .forgot-form-box {
            background: white;
            padding: 25px;
            border-radius: 12px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #5A4A42;
            font-size: 0.95rem;
        }

        .form-control {
            border: 1px solid #F0E6E0;
            border-radius: 8px;
            padding: 12px 15px;
            width: 100%;
            transition: all 0.3s;
            background: #FFF9F6;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: #FFB6C1;
            box-shadow: 0 0 0 3px rgba(255, 182, 193, 0.2);
            background: white;
            outline: none;
        }

        .btn-reset {
            background: linear-gradient(135deg, #FF9A9E 0%, #FAD0C4 100%);
            border: none;
            color: white;
            padding: 12px 25px;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(255, 154, 158, 0.3);
            font-size: 0.9rem;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }

        .btn-reset:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 154, 158, 0.4);
        }

        .back-to-login {
            text-align: center;
            margin-top: 25px;
        }

        .back-to-login a {
            color: #FF7E8A;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 0.95rem;
        }

        .back-to-login a:hover {
            color: #FF5A6A;
            text-decoration: underline;
        }

        .alert {
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 20px;
            font-size: 0.95rem;
            border: 1px solid transparent;
        }

        .alert-info {
            background-color: #FFEBEE;
            border-color: #FFCDD2;
            color: black;
        }

        @media (max-width: 768px) {
            .bakery-forgot-card {
                padding: 30px;
            }

            .forgot-main-title {
                font-size: 2.2rem;
            }
        }

        @media (max-width: 576px) {
            .bakery-forgot-card {
                padding: 25px 20px;
            }

            .bakery-forgot-wrapper {
                padding: 20px 15px;
            }

            .forgot-main-title {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>
    <div class="bakery-forgot-wrapper">
        <div class="bakery-forgot-container">
            <div class="bakery-forgot-card">
                <div class="forgot-header">
                    <h1 class="forgot-main-title">Forgot Password</h1>
                    <p class="forgot-subtitle mt-5">Enter your email and we'll send you a link to reset your password
                    </p>
                </div>
                <div class="forgot-form-box">
                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-info"><?= htmlspecialchars($_SESSION['message']) ?></div>
                        <?php unset($_SESSION['message']); ?>
                    <?php endif; ?>
                    <form action="php/forgot_password.php" method="POST">
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="your@email.com" required>
                        </div>
                        <button type="submit" class="btn btn-reset">
                            <i class="fas fa-paper-plane"></i> Send Reset Link
                        </button>
                    </form>
                    <div class="back-to-login">
                        <a href="login.php"><i class="fas fa-arrow-left"></i> Back to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'includes/footer.php'; ?>
</body>

</html>