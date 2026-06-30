<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require_once __DIR__ . "/inc/db.php"; // adjust if needed

    // Collect form data safely
    $name    = htmlspecialchars(trim($_POST['name']));
    $email   = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Save to database
    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $message);
    $stmt->execute();
    $stmt->close();

    // Email settings
    $to      = "idemudiadaniel1@gmail.com"; // your email
    $subject = "New Contact Message from Worldison Website";
    $body    = "Name: $name\nEmail: $email\n\nMessage:\n$message";
    $headers = "From: info@worldison.org\r\nReply-To: $email";

    // Try to send the email
    if (mail($to, $subject, $body, $headers)) {
        echo "<script>alert('Your message was sent successfully!'); window.location.href='public/contact.php';</script>";
    } else {
        echo "<script>alert('Sorry, message could not be sent. Try again later.'); window.location.href='Public/contact.php';</script>";
    }
} else {
    header("Location: Public/contact.php");
    exit;
}
?>
