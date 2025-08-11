<?php
include 'includes/header.php';
include 'includes/db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['userId'])) {
    $_SESSION['message'] = "Please login to view your profile.";
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['userId'];
$user = null;

// Fetch user data from the database
$stmt = $conn->prepare("SELECT name, email, cell, address FROM USERS WHERE userId = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    // This should ideally not happen if userId is from session
    $_SESSION['message'] = "User data not found.";
    header("Location: logout.php"); // Log out if user data is missing
    exit();
}
$stmt->close();
$conn->close();
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <h2 class="mb-4">My Profile</h2>

        <?php
        if (isset($_SESSION['message'])) {
            echo '<div class="alert alert-info">' . htmlspecialchars($_SESSION['message']) . '</div>';
            unset($_SESSION['message']);
        }
        ?>

        <?php if ($user): ?>
            <form action="php/update_profile.php" method="POST"
                onsubmit="return confirm('Are you sure you want to update your profile?');">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name"
                        value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" name="email"
                        value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                    <small class="form-text text-muted">Email cannot be changed directly here.</small>
                </div>
                <div class="mb-3">
                    <label for="cell" class="form-label">Cell Number</label>
                    <input type="text" class="form-control" id="cell" name="cell"
                        value="<?php echo htmlspecialchars($user['cell']); ?>">
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control" id="address" name="address"
                        rows="3"><?php echo htmlspecialchars($user['address']); ?></textarea>
                </div>

                <h4 class="mt-5 mb-3">Change Password (Optional)</h4>
                <div class="mb-3">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input type="password" class="form-control" id="current_password" name="current_password"
                        placeholder="Only required if changing password">
                </div>
                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="new_password" name="new_password">
                    <small class="form-text text-muted">Leave blank if you don't want to change your password.</small>
                </div>
                <div class="mb-3">
                    <label for="confirm_new_password" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password">
                </div>

                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>

        <?php else: ?>
            <p class="text-center">Could not load user profile.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>