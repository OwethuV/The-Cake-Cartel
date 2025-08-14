<?php 
session_start();
include 'includes/header.php';

// Clear session data related to user authentication
if (isset($_SESSION['user_id'])) {
    unset($_SESSION['user_id']);
}
// Redirect to homepage if already registered
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | The Cake Cartel</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --pink: #ff7e8a;
            --light-pink: #ffd6dd;
            --peach: #fad0c4;
            --tan: #5a4a42;
            --light-tan: #8a7369;
            --bg: #fff9f6;
        }
        
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg);
        }
        
        .split-container {
            display: flex;
            min-height: 100vh;
        }
        
        .image-half {
            flex: 1;
            background-image: url('https://images.unsplash.com/photo-1552689486-f6773047d19f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80');
            background-size: cover;
            background-position: center;
            position: relative;
        }
        
        .image-half::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 192, 203, 0.1);
        }
        
        .form-half {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .register-card {
            max-width: 400px;
            width: 100%;
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(251, 176, 166, 0.15);
        }
        
        .register-title {
            font-family: 'Pacifico', cursive;
            color: var(--pink);
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 2.2rem;
        }
        
        .btn-register {
            background: linear-gradient(135deg, var(--pink) 0%, var(--peach) 100%);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 50px;
            width: 100%;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-top: 1rem;
            transition: all 0.3s;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 126, 138, 0.4);
        }
        
        .form-control {
            border: 2px solid #f0e6e0;
            border-radius: 12px;
            padding: 12px 15px;
            margin-bottom: 1.2rem;
        }
        
        .form-control:focus {
            border-color: var(--light-pink);
            box-shadow: 0 0 0 3px rgba(255, 182, 193, 0.2);
        }
        
        .login-link {
            color: var(--pink);
            text-decoration: none;
            font-weight: 500;
            display: block;
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .login-link:hover {
            color: #ff5a6a;
            text-decoration: underline;
        }
        
        .alert {
            border-radius: 12px;
        }
        
        @media (max-width: 768px) {
            .split-container {
                flex-direction: column;
            }
            
            .image-half {
                height: 200px;
                visibility: hidden;
            }
        }
    </style>
</head>
<body>
    <div class="split-container">
        <div class="image-half"></div>
        <div class="form-half">
            <div class="register-card">
                <h2 class="register-title">The Cake Cartel</h2>
                
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-info"><?= htmlspecialchars($_SESSION['message']) ?></div>
                    <?php unset($_SESSION['message']); ?>
                <?php endif; ?>
                
                <!-- Registration Form -->
                <form action="php/register.php" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Your full name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="your@email.com" required>
                    </div>
                    <div class="mb-3">
                        <label for="cell" class="form-label">Cell Number (Optional)</label>
                        <input type="tel" class="form-control" id="cell" name="cell" placeholder="">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address (Optional)</label>
                        <textarea class="form-control" id="address" name="address" rows="3" placeholder="Your delivery address"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn btn-register">
                        <i class="fas fa-user-plus me-2"></i> Register
                    </button>
                </form>
                
                <a href="login.php" class="login-link">
                    Already have an account? Login here
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
