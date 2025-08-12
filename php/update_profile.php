<?php
session_start();
include '../includes/db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['userId'])) {
    $_SESSION['message'] = "Please login to update your profile.";
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['userId'];
    $name = trim($_POST['name']);
    $cell = trim($_POST['cell']);
    $address = trim($_POST['address']);

    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_new_password = $_POST['confirm_new_password'] ?? '';

    // Basic validation
    if (empty($name)) {
        $_SESSION['message'] = "Name cannot be empty.";
        header("Location: ../profile.php");
        exit();
    }

    // Start a transaction for atomicity
    $conn->begin_transaction();
    $update_success = true;

    // 1. Update general user information (name, cell, address)
    $stmt = $conn->prepare("UPDATE USERS SET name = ?, cell = ?, address = ? WHERE userId = ?");
    $stmt->bind_param("sssi", $name, $cell, $address, $userId);

    if (!$stmt->execute()) {
        $update_success = false;
        $_SESSION['message'] = "Error updating profile: " . $stmt->error;
    }
    $stmt->close();

    // Update session name if successful
    if ($update_success) {
        $_SESSION['userName'] = $name;
    }

    // 2. Handle password change if new password fields are filled
    if (!empty($new_password)) {
        if (empty($current_password)) {
            $update_success = false;
            $_SESSION['message'] = "Current password is required to change password.";
        } elseif ($new_password !== $confirm_new_password) {
            $update_success = false;
            $_SESSION['message'] = "New passwords do not match.";
        } elseif (strlen($new_password) < 6) { // Example: minimum password length
            $update_success = false;
            $_SESSION['message'] = "New password must be at least 6 characters long.";
        } else {
            // Verify current password
            $stmt_check_pass = $conn->prepare("SELECT password FROM USERS WHERE userId = ?");
            $stmt_check_pass->bind_param("i", $userId);
            $stmt_check_pass->execute();
            $result_check_pass = $stmt_check_pass->get_result();
            $user_data = $result_check_pass->fetch_assoc();
            $stmt_check_pass->close();

            if ($user_data && password_verify($current_password, $user_data['password'])) {
                // Hash and update new password
                $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt_update_pass = $conn->prepare("UPDATE USERS SET password = ? WHERE userId = ?");
                $stmt_update_pass->bind_param("si", $hashed_new_password, $userId);
                if (!$stmt_update_pass->execute()) {
                    $update_success = false;
                    $_SESSION['message'] = "Error updating password: " . $stmt_update_pass->error;
                }
                $stmt_update_pass->close();
            } else {
                $update_success = false;
                $_SESSION['message'] = "Incorrect current password.";
            }
        }
    }

    // Commit or rollback transaction
    if ($update_success) {
        $conn->commit();
        $_SESSION['message'] = $_SESSION['message'] ?? "Profile updated successfully!"; // Keep existing message if password updated
    } else {
        $conn->rollback();
        // Message already set by the specific error
    }

    $conn->close();
    header("Location: ../profile.php");
    exit();

} else {
    header("Location: ../profile.php"); // Redirect if accessed directly
    exit();
}
?>
