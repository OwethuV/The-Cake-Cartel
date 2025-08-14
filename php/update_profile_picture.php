<?php
session_start();
include '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'] ?? null;

    if ($user_id && isset($_FILES['profile_picture'])) {
        $file = $_FILES['profile_picture'];

        // Check for errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['message'] = "Error uploading file.";
            header("Location: ../profile.php");
            exit();
        }

        // Validate file type (e.g., only allow images)
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowed_types)) {
            $_SESSION['message'] = "Invalid file type. Please upload an image.";
            header("Location: ../profile.php");
            exit();
        }

        // Move the uploaded file to a designated directory
        $upload_dir = '../uploads/'; // Ensure this directory exists and is writable
        $file_name = uniqid() . '-' . basename($file['name']);
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            // Update the user's profile picture in the database
            $stmt = $pdo->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
            $stmt->execute([$file_path, $user_id]);

            $_SESSION['message'] = "Profile picture updated successfully!";
        } else {
            $_SESSION['message'] = "Failed to move uploaded file.";
        }
    } else {
        $_SESSION['message'] = "User not logged in.";
    }

    header("Location: ../profile.php");
    exit();
} else {
    header("Location: ../profile.php");
    exit();
}
