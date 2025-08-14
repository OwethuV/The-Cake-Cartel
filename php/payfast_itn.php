<?php
// payfast_itn.php
// Include your config and DB connection here

// Read POST data from PayFast ITN
$pfData = $_POST;

// Verify ITN signature here (security step)

// Verify payment status is 'COMPLETE'

// Update your order status in DB accordingly

// Respond with 'OK' to PayFast
header("HTTP/1.0 200 OK");
echo "OK";
?>
