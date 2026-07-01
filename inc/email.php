<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php'; // adjust path if needed

function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp-relay.brevo.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = '9b2b9e001@smtp-brevo.com';
        $mail->Password   = 'xsmtpsib-686c62349d531f429d1f4f650f1eb66cab4756659d9133fad03363a2c3191c0a-tx67Nqg3DftH7GVU'; // Brevo SMTP key
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('noreply@worldison.org', 'Worldison HR System');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email failed: {$mail->ErrorInfo}");
        return false;
    }
}
?>
