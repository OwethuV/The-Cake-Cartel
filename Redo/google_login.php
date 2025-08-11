<?php
session_start();
require 'db_connect.php'; // Include your database connection

if (isset($_POST['id_token'])) {
    $id_token = $_POST['id_token'];

    // Verify the ID token with Google
    $client = new Google_Client(['client_id' => 'YOUR_CLIENT_ID.apps.googleusercontent.com']); // Specify the CLIENT_ID of the app that accesses the backend
    $payload = $client->verifyIdToken($id_token);
    
    if ($payload) {
        $google_id = $payload['sub'];
        $email = $payload['email'];
        $name = $payload['name'];

        // Check if the user already exists in your database
        $stmt = $conn->prepare("SELECT * FROM USERS WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // User exists, log them in
            $user = $result->fetch_assoc();
            $_SESSION['userId'] = $user['userId'];
            $_SESSION['userName'] = $user['name'];
            $_SESSION['message'] = "Welcome back, " . $user['name'] . "!";
        } else {
            // User does not exist, create a new account
            $stmt = $conn->prepare("INSERT INTO USERS (name, email) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $email);
            $stmt->execute();
            $_SESSION['userId'] = $conn->insert_id; // Get the new user ID
            $_SESSION['userName'] = $name;
            $_SESSION['message'] = "Welcome, " . $name . "!";
        }

        $stmt->close();
        $conn->close();
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid ID token']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID token not provided']);
}
?>
