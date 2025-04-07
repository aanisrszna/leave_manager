<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv; // Import Dotenv

require 'vendor/autoload.php'; // Load dependencies

function send_email($to, $subject, $message) {
    // Load environment variables from .env file
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();

    $mail = new PHPMailer(true);
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = getenv('SMTP_HOST');    // SMTP Host
        $mail->SMTPAuth   = true;
        $mail->Username   = getenv('SMTP_USER');    // SMTP Username
        $mail->Password   = getenv('SMTP_PASS');    // SMTP Password
        $mail->SMTPSecure = getenv('SMTP_SECURE');  // Encryption (STARTTLS)
        $mail->Port       = getenv('SMTP_PORT');    // TCP Port (587)

        // Sender & Recipient
        $mail->setFrom(getenv('SMTP_USER'), 'River Raven Leave System');
        $mail->addAddress($to);

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
        return "Email sent successfully to $to";
    } catch (Exception $e) {
        return "Email failed: " . $mail->ErrorInfo;
    }
}
?>
