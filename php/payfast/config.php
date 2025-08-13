<?php
// payfast/config.php

// Direct path to .env in project root
$envPath = realpath(__DIR__ . '/../../.env');

if (!$envPath || !file_exists($envPath)) {
    die('.env file missing! Make sure it is in the project root.');
}

// Load .env lines
$env = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($env as $line) {
    if (strpos($line, '=') === false || trim($line)[0] === '#') continue;
    [$key, $value] = explode('=', $line, 2);
    $_ENV[trim($key)] = trim($value);
}

// Assign PayFast variables
$merchant_id  = $_ENV['PAYFAST_MERCHANT_ID'] ?? '';
$merchant_key = $_ENV['PAYFAST_MERCHANT_KEY'] ?? '';
$passphrase   = $_ENV['PAYFAST_PASSPHRASE'] ?? '';
$env          = $_ENV['PAYFAST_ENV'] ?? 'sandbox';
$baseUrl      = $_ENV['PAYFAST_BASE_URL'] ?? 'http://localhost/cake';
