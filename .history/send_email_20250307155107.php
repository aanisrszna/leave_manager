<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require __DIR__ . '/vendor/autoload.php'; // Autoload dependencies

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

function send_email($to, $subject, $message) {
    $mail = new PHPMailer(true);
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USER'];
        $mail->Password   = $_ENV['SMTP_PASS'];
        $mail->SMTPSecure = $_ENV['SMTP_SECURE'];
        $mail->Port       = $_ENV['SMTP_PORT'];

        // Debug: Output the retrieved environment variables
        echo "SMTP_HOST: " . $_ENV['SMTP_HOST'] . "\n";
        echo "SMTP_USER: " . $_ENV['SMTP_USER'] . "\n";
        echo "SMTP_PASS: " . $_ENV['SMTP_PASS'] . "\n";
        echo "SMTP_SECURE: " . $_ENV['SMTP_SECURE'] . "\n";
        echo "SMTP_PORT: " . $_ENV['SMTP_PORT'] . "\n";

        // Sender & Recipient
        $mail->setFrom($_ENV['SMTP_USER'], 'River Raven Leave System');
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

// Example usage
echo send_email('recipient@example.com', 'Test Subject', 'Test Message');
?>
