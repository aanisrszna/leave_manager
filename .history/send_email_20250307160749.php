<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require 'vendor/autoload.php'; // Load PHPMailer & Dotenv

// Load .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "Environment variables loaded successfully.<br>"; // Debugging message

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

        echo "SMTP configuration set.<br>"; // Debugging message

        // Sender & Recipient
        $mail->setFrom($_ENV['SMTP_FROM_EMAIL'], $_ENV['SMTP_FROM_NAME']);
        $mail->addAddress($to);

        echo "Recipient added: $to<br>"; // Debugging message

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        echo "Email content set.<br>"; // Debugging message

        $mail->send();
        echo "Email sent successfully to $to.<br>"; // Debugging message
        return "Email sent successfully to $to";
    } catch (Exception $e) {
        echo "Email failed: " . $mail->ErrorInfo . "<br>"; // Debugging message
        return "Email failed: " . $mail->ErrorInfo;
    }
}

// Example call to test email sending
send_email('recipient@example.com', 'Test Email', 'This is a test email.');
?>
