<?php
// Database connection settings.
// Adjust values or set environment variables before running the application.
$DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
$DB_USER = getenv('DB_USER') ?: 'worldison_user';
$DB_PASS = getenv('DB_PASS') ?: 'worldison_pass';
$DB_NAME = getenv('DB_NAME') ?: 'worldison';
$DB_PORT = getenv('DB_PORT') ?: 3306;

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');
