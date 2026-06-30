<?php
require_once "../inc/email.php"; // <-- path depends on your structure

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    // Basic validation
    if ($name === "" || $email === "" || $message === "") {
        die("All fields are required.");
    }

    // Email body (HTML)
    $body = "
        <h2>New Worldison Contact Form Message</h2>
        <p><strong>Name:</strong> {$name}</p>
        <p><strong>Email:</strong> {$email}</p>
        <p><strong>Subject:</strong> {$subject}</p>
        <p><strong>Message:</strong><br>{$message}</p>
    ";

    // Send email to: info@worldison.org
    $sent = sendEmail("info@worldison.org", "New Contact form Message: $subject", $body);

    if ($sent) {
        echo "<script>alert('Your message was sent successfully!'); window.location.href='contact.php';</script>";
    } else {
        echo "<script>alert('Sorry, message could not be sent. Try again later.'); window.location.href='contact.php';</script>";
    }
} else {
    header("Location: contact.php");
    exit;
}
?>
