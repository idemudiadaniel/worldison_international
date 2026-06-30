<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Recipient address
$to = "idemudiadaniel1@gmail.com"; // Replace with your Gmail

// Email subject
$subject = "Test Email from cPanel Sendmail (worldison.org)";

// HTML message
$message = "
<html>
<head>
  <title>Test Email</title>
</head>
<body>
  <h2>Sendmail Test from Worldison Server</h2>
  <p>This is a test email sent using PHP's built-in mail() function.</p>
  <p>If you receive this, cPanel Sendmail is working correctly ✅</p>
</body>
</html>
";

// Headers
$headers  = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: Worldison Test <info@worldison.org>" . "\r\n";
$headers .= "Reply-To: info@worldison.org" . "\r\n";

// Optional (helps some servers route correctly)
ini_set("sendmail_from", "info@worldison.org");

// Send email
if (mail($to, $subject, $message, $headers)) {
    echo "<h3 style='color:green;'>✅ Email successfully sent to {$to}</h3>";
} else {
    echo "<h3 style='color:red;'>❌ Failed to send email. Check cPanel mail routing or logs.</h3>";
}
?>
