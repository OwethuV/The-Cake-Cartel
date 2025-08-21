<?php
session_start();
include 'includes/header.php';
require_once 'includes/db_connect.php';


$user_id = $_SESSION['user_id'] ?? null;
if ($user_id) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
}


$user = [
    'name' => '',
    'email' => '',
    'cell' => '',
    'address' => '',
    'profile_picture' => ''
];

// Populate from session if available
// if (isset($_SESSION['user'])) {

// } else {
// Handle case where user isn't logged in
//     header("Location: login.php");
//     exit();
// }

// Or populate from database if you're using one
/*
require_once 'php/db_connection.php';
$user_id = $_SESSION['user_id'] ?? null;
if ($user_id) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
}
*/
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | The Cake Cartel</title>
   
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

        .bakery-profile-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 30px 20px;
        }

        .bakery-profile-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(251, 176, 166, 0.15);
            overflow: hidden;
            padding: 30px;
            width: 100%;
            max-width: 1200px;
        }

        .profile-header {
            margin-bottom: 30px;
            text-align: center;
        }

        .profile-main-title {
            font-family: 'Pacifico', cursive;
            color: #FF7E8A;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .profile-subtitle {
            font-size: 1rem;
            color: #A38B82;
            max-width: 500px;
            margin: 0 auto 20px;
        }

        .profile-picture-card {
            background: #FFF9F6;
            border-radius: 12px;
            padding: 20px;
            height: 100%;
            text-align: center;
        }

        .profile-picture-container {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            margin: 0 auto 20px;
            border: 5px solid #FFD6DD;
            position: relative;
        }

        .profile-picture {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-picture-placeholder {
            width: 100%;
            height: 100%;
            background: #FFD6DD;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FF7E8A;
            font-size: 3rem;
        }

        .btn-upload {
            background: linear-gradient(135deg, #FF9A9E 0%, #FAD0C4 100%);
            border: none;
            color: white;
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(255, 154, 158, 0.3);
            font-size: 0.9rem;
            cursor: pointer;
            display: inline-block;
            margin-top: 10px;
        }

        .btn-upload:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 154, 158, 0.4);
        }

        .profile-info-box {
            background: white;
            padding: 25px;
            border-radius: 12px;
            height: 100%;
        }

        .form-title {
            color: #5A4A42;
            font-size: 1.6rem;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .form-group label {
            font-weight: 500;
            color: #5A4A42;
            font-size: 0.95rem;
        }

        .form-control {
            border: 1px solid #F0E6E0;
            border-radius: 8px;
            padding: 10px 12px;
            background: #FFF9F6;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #FFB6C1;
            box-shadow: 0 0 0 3px rgba(255, 182, 193, 0.2);
            background: white;
        }

        textarea.form-control {
            min-height: 120px;
        }

        .btn-update {
            background: linear-gradient(135deg, #FF9A9E 0%, #FAD0C4 100%);
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

        .btn-update:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 154, 158, 0.4);
        }

        .alert-info {
            background-color: #FFD6DD;
            border-color: #FFB6C1;
            color: #5A4A42;
        }

        @media (max-width: 992px) {

            .profile-picture-card,
            .profile-info-box {
                margin-bottom: 30px;
            }
        }

        @media (max-width: 768px) {
            .profile-main-title {
                font-size: 2rem;
            }

            .form-title {
                font-size: 1.4rem;
            }

            .form-group label {
                font-size: 0.9rem;
            }

            .form-control {
                font-size: 0.9rem;
            }

            .btn-update {
                width: 100%;
                font-size: 1rem;
                padding: 12px;
            }

            .profile-picture-container {
                width: 120px;
                height: 120px;
            }
        }
    </style>
</head>

<body>
    <div class="bakery-profile-wrapper">
        <div class="bakery-profile-card">
            <div class="profile-header">
                <h1 class="profile-main-title">My Profile</h1>
                <p class="profile-subtitle">Update your personal information and manage your account</p>
            </div>

            <?php
            if (isset($_SESSION['message'])) {
                echo '<div class="alert alert-info mb-4">' . htmlspecialchars($_SESSION['message']) . '</div>';
                unset($_SESSION['message']);
            }
            ?>

            <div class="row g-4">
                
                <div class="col-12 col-lg-4">
                    <div class="profile-picture-card">
                        <div class="profile-picture-container">
                            <?php if (isset($user['profile_picture']) && !empty($user['profile_picture'])): ?>
                                <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture"
                                    class="profile-picture">
                            <?php else: ?>
                                <div class="profile-picture-placeholder">
                                    <i class="fas fa-user"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <h3><?php echo htmlspecialchars($user['name']); ?></h3>
                        <p class="text-muted mb-3"><?php echo htmlspecialchars($user['email']); ?></p>
                        <form action="php/upload_profile_picture.php" method="POST" enctype="multipart/form-data">
                            <input type="file" id="profile_picture" name="profile_picture" accept="image/*"
                                style="display: none;">
                            <label for="profile_picture" class="btn-upload">
                                <i class="fas fa-camera"></i> Change Photo
                            </label>
                            <button type="submit" class="btn-upload d-none" id="submit-photo">Upload</button>
                        </form>
                    </div>
                </div>

                
                <div class="col-12 col-lg-8">
                    <div class="profile-info-box">
                        <h2 class="form-title">Personal Information</h2>
                        <?php if ($user): ?>
                            <form action="php/update_profile.php" method="POST"
                                onsubmit="return confirm('Are you sure you want to update your profile?');">
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="email" class="form-label">Email address</label>
                                            <input type="email" class="form-control" id="email" name="email"
                                                value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                                            <small class="form-text text-muted">Email cannot be changed directly
                                                here.</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3 mt-1">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="cell" class="form-label">Cell Number</label>
                                            <input type="text" class="form-control" id="cell" name="cell"
                                                value="<?php echo htmlspecialchars($user['cell']); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mt-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control" id="address" name="address"
                                        rows="3"><?php echo htmlspecialchars($user['address']); ?></textarea>
                                </div>

                                <h4 class="mt-5 mb-3">Change Password</h4>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="current_password" class="form-label">Current Password</label>
                                            <input type="password" class="form-control" id="current_password"
                                                name="current_password" placeholder="Only required if changing password">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="new_password" class="form-label">New Password</label>
                                            <input type="password" class="form-control" id="new_password"
                                                name="new_password">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="confirm_new_password" class="form-label">Confirm New
                                                Password</label>
                                            <input type="password" class="form-control" id="confirm_new_password"
                                                name="confirm_new_password">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <small class="form-text text-muted">Leave password fields blank if you don't want to
                                            change your password.</small>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-update mt-4">
                                    <i class="fas fa-save"></i> Update Profile
                                </button>
                            </form>
                        <?php else: ?>
                            <p class="text-center">Could not load user profile.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        
        document.getElementById('profile_picture').addEventListener('change', function () {
            if (this.files.length > 0) {
                document.getElementById('submit-photo').click();
            }
        });
    </script>

    <?php include 'includes/footer.php'; ?>
</body>

</html>