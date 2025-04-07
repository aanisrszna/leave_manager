<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require 'vendor/autoload.php'; // Load PHPMailer & Dotenv

// Load .env file
$dotenv = Dotenv::createImmutable(__DIR__ . '/../'); // Load .env from one level above
$dotenv->load();

// Example call to test

function send_email($to, $subject, $message) {
    $mail = new PHPMailer(true);
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USERNAME'];
        $mail->Password = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = 'STARTTLS';
        $mail->Port = $_ENV['SMTP_PORT'];

        // Sender & Recipient
        $mail->setFrom($_ENV['SMTP_FROM_EMAIL'], $_ENV['SMTP_FROM_NAME']);
        $mail->addAddress($to);

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
        echo "Email sent successfully to $to.<br>";
    } catch (Exception $e) {
        echo "Email failed: " . $mail->ErrorInfo . "<br>";
    }
}
?>
