<?php
// Set correct timezone before anything else
date_default_timezone_set('Africa/Lagos');

// Database connection settings
$host = "127.0.0.1";
$user = "worldison_user";
$pass = "worldison_pass";
$db   = "worldison";

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure MySQL uses Lagos timezone (UTC+1)
mysqli_query($conn, "SET time_zone = '+01:00'");

// Optional: uncomment to verify connection & timezone
// echo "Connected successfully. Server time: " . date('Y-m-d H:i:s');
?>
