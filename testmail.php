<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // make sure PHPMailer is installed via composer

$mail = new PHPMailer(true);

try {
    // === SMTP SETTINGS ===
    $mail->isSMTP();
    $mail->Host       = 'smtp-relay.brevo.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = '9b2b9e001@smtp-brevo.com'; // your Brevo login
    $mail->Password   = 'xsmtpsib-686c62349d531f429d1f4f650f1eb66cab4756659d9133fad03363a2c3191c0a-tx67Nqg3DftH7GVU';       // replace with your actual Brevo API key
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // === SENDER AND RECEIVER ===
    $mail->setFrom('idemudiadaniel1@gmail.com', 'Worldison Academy'); 
    $mail->addAddress('danielodion17@gmail.com', 'Student Name'); // replace with the recipient's email

    // === EMAIL CONTENT ===
    $mail->isHTML(true);
    $mail->Subject = 'Welcome to Worldison Academy!';
    $mail->Body    = '
        <h2>Welcome to Worldison Academy 🎓</h2>
        <p>Hi there, thank you for joining our academy!<br>
        Start learning now at <a href="https://worldison.org">Worldison.org</a></p>
    ';
    $mail->AltBody = 'Welcome to Worldison Academy! Visit https://worldison.org to start learning.';

    // === SEND EMAIL ===
    $mail->send();
    echo '✅ Email sent successfully!';
} catch (Exception $e) {
    echo "❌ Email could not be sent. Error: {$mail->ErrorInfo}";
}
?>
