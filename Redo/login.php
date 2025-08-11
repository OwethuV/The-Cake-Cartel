<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | The Cake Cartel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        .login-card {
            max-width: 400px;
            width: 100%;
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(251, 176, 166, 0.15);
        }

        .login-title {
            font-family: 'Pacifico', cursive;
            color: var(--pink);
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 2.2rem;
        }

        .btn-login {
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

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 126, 138, 0.4);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
            color: var(--light-tan);
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #f0e6e0;
        }

        .divider:not(:empty)::before {
            margin-right: 1em;
        }

        .divider:not(:empty)::after {
            margin-left: 1em;
        }

        .btn-google {
            background: white;
            color: var(--tan);
            border: 2px solid #f0e6e0;
            border-radius: 50px;
            padding: 12px;
            width: 100%;
            font-weight: 500;
            transition: all 0.3s;
            margin-bottom: 1rem;
        }

        .btn-google:hover {
            background: #f8f9fa;
            border-color: #ddd;
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

        .register-link {
            color: var(--pink);
            text-decoration: none;
            font-weight: 500;
            display: block;
            text-align: center;
            margin-top: 1.5rem;
        }

        .register-link:hover {
            color: #ff7e8a;
            text-decoration: none;
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
            }
        }

        .underline {
            text-decoration: underline;
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@400;500;600&display=swap"
        rel="stylesheet">
    <script src="https://apis.google.com/js/platform.js" async defer></script>
    <meta name="google-signin-client_id" content="YOUR_CLIENT_ID.apps.googleusercontent.com">
    <!-- Replace with your Client ID -->
</head>

<body>
    <div class="split-container">
        <div class="image-half"></div>
        <div class="form-half">
            <div class="login-card">
                <h2 class="login-title">The Cake Cartel</h2>

                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-info"><?= htmlspecialchars($_SESSION['message']) ?></div>
                    <?php unset($_SESSION['message']); ?>
                <?php endif; ?>

                <div class="g_id_signin" data-type="standard" data-shape="rectangular" data-theme="outline"
                    data-text="continue_with" data-size="large" data-logo_alignment="left"></div>

                <div class="divider">or login with email</div>
                <form action="php/login.php" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="your@email.com"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="••••••••"
                            required>
                    </div>
                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i> Login
                    </button>
                </form>

                <p>Don't have an account? <a href="register.php" class="register-link underline">
                     Register here
                </a></p>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function onSignIn(googleUser) {
            var profile = googleUser.getBasicProfile();
            var id_token = googleUser.getAuthResponse().id_token;

            // Send the ID token to your server for verification
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'php/google_login.php'); // Create a new PHP file to handle Google login
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                // Handle the response from your server
                console.log('Signed in as: ' + profile.getName());
                window.location.href = 'index.php'; // Redirect to the homepage or dashboard
            };
            xhr.send('id_token=' + id_token);
        }
    </script>
</body>

</html>