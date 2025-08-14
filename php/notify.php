<?php
// PayFast will POST data here
$raw_post_data = file_get_contents('php://input');

// Log the data to check it's being received (for testing)
file_put_contents('payfast_notify_log.txt', $raw_post_data . PHP_EOL, FILE_APPEND);

// Later you can validate and update order status in your DB
