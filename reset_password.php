<?php
session_start();
include 'includes/db_connect.php'; // Correct path to the database connection

$token = isset($_GET['token']) ? $_GET['token'] : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Check if the passwords match
    if ($password !== $confirmPassword) {
        $_SESSION['message'] = "Passwords do not match. Please try again.";
        header("Location: reset_password.php?token=" . htmlspecialchars($token));
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if the token is valid
    $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND expires > ?");
    $expires = date("U");
    $stmt->bind_param("si", $token, $expires);
    if (!$stmt->execute()) {
        $_SESSION['message'] = "Failed to execute query: " . $stmt->error;
        header("Location: login.php");
        exit();
    }
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($email);
        $stmt->fetch();

        // Fetch the user's name from the USERS table
        $stmt = $conn->prepare("SELECT name FROM USERS WHERE email = ?");
        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            $_SESSION['message'] = "Failed to fetch user name: " . $stmt->error;
            header("Location: login.php");
            exit();
        }
        $stmt->bind_result($userName);
        $stmt->fetch();

        // Update the user's password
        $stmt = $conn->prepare("UPDATE USERS SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashedPassword, $email);
        if ($stmt->execute()) {
            // Delete the token from the database
            $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
            $stmt->bind_param("s", $token);
            $stmt->execute();

            $_SESSION['message'] = "Your password has been reset successfully!";
            header("Location: login.php");
            exit();
        } else {
            $_SESSION['message'] = "Failed to update password: " . $stmt->error;
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['message'] = "This token is invalid or has expired.";
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | The Cake Cartel</title>
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

        .bakery-reset-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .bakery-reset-container {
            width: 100%;
            max-width: 600px;
        }

        .bakery-reset-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(251, 176, 166, 0.15);
            overflow: hidden;
            padding: 40px;
            margin: 0 auto;
        }

        .reset-header {
            margin-bottom: 30px;
            text-align: center;
        }

        .reset-main-title {
            font-family: 'Pacifico', cursive;
            color: #FF7E8A;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .reset-subtitle {
            font-size: 1rem;
            color: #A38B82;
            max-width: 500px;
            margin: 0 auto 20px;
        }

        .reset-form-box {
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
    </style>
</head>

<body>
    <div class="bakery-reset-wrapper">
        <div class="bakery-reset-container">
            <div class="bakery-reset-card">
                <div class="reset-header">
                    <h1 class="reset-main-title">Reset Password</h1>
                    <p class="reset-subtitle">Enter your new password below.</p>
                </div>
                <div class="reset-form-box">
                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-info"><?= htmlspecialchars($_SESSION['message']) ?></div>
                        <?php unset($_SESSION['message']); ?>
                    <?php endif; ?>
                    <form action="reset_password.php" method="POST">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                required>
                        </div>
                        <button type="submit" class="btn btn-reset">Reset Password</button>
                    </form>
                    <div class="back-to-login">
                        <a href="login.php"><i class="fas fa-arrow-left"></i> Back to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>