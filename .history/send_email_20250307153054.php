<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv; // Import dotenv

require 'vendor/autoload.php';

// Load environment variables from .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Debugging: Check if SMTP_USER is loaded correctly
echo "SMTP User: " . getenv('SMTP_USER') . "<br>";

$mail = new PHPMailer(true);

try {
    // SMTP Settings from .env
    $mail->isSMTP();
    $mail->Host       = getenv('SMTP_HOST');
    $mail->SMTPAuth   = true;
    $mail->Username   = getenv('SMTP_USER');
    $mail->Password   = getenv('SMTP_PASS');
    $mail->SMTPSecure = getenv('SMTP_SECURE'); // STARTTLS
    $mail->Port       = getenv('SMTP_PORT');   // 587

    // Sender & Recipient
    $mail->setFrom(getenv('SMTP_USER'), 'Nur Edrinna');
    $mail->addAddress('anis@riverraven.com.my', 'Anis Ruszanna');

    // Email Content
    $mail->isHTML(true);
    $mail->Subject = 'SMTP Test - Office 365';
    $mail->Body    = '<h3>This is a test email from Nur Edrinna to Anis Ruszanna.</h3>';
    $mail->AltBody = 'This is a test email from Nur Edrinna to Anis Ruszanna.';

    // Send Email
    $mail->send();
    echo '✅ Email sent successfully!';
} catch (Exception $e) {
    echo "❌ Email failed to send. Error: " . $mail->ErrorInfo;
}
?>
