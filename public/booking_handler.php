<?php
require_once __DIR__ . '/../inc/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $service_requested = trim($_POST['service_requested']);
    $message = trim($_POST['message']);

    $stmt = $pdo->prepare("INSERT INTO bookings (full_name, email, phone, service_requested, message) 
                           VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$full_name, $email, $phone, $service_requested, $message])) {
        header("Location: booking.php?success=1");
        exit;
    } else {
        header("Location: booking.php?error=1");
        exit;
    }
}
