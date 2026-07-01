<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php'; // Load Composer autoloader

function sendUserCredentials($toEmail, $toName, $username, $plainPassword) {
    $mail = new PHPMailer(true);
    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host       = 'mail.worldison.org';      // Replace with your actual mail server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'noreply@worldison.org';      // Your cPanel email
        $mail->Password   = 'Worldison123!';     // Your email password or app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Sender and recipient
        $mail->setFrom('noreply@worldison.org', 'Worldison HR System');
        $mail->addAddress($toEmail, $toName);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Your Account Login Details';
        $mail->Body = "
            <h3>Welcome to Worldison!</h3>
            <p>Dear <b>{$toName}</b>,</p>
            <p>Your user account has been created successfully.</p>
            <p><b>Login details:</b></p>
            <ul>
                <li>Username: <b>{$username}</b></li>
                <li>Password: <b>{$plainPassword}</b></li>
            </ul>
            <p>Please log in and change your password after your first login.</p>
            <br>
            <p>— Worldison HR System</p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("📧 Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
