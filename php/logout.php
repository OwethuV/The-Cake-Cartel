<?php
session_start();
session_unset(); // Unsetting all session variables
session_destroy(); // Destroying the session
header("Location: ../index.php"); // Redirecting to homepage
exit();
?>
