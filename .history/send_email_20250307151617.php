<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    // SMTP Settings
    $mail->isSMTP();
    $mail->Host = 'smtp.office365.com';  // Office 365 SMTP Server
    $mail->SMTPAuth = true;
    $mail->Username = 'nur.edrinna@riverraven.com.my';  // Sender Email (Nur Edrinna)
    $mail->Password = '@Faiq0104';  // Office 365 Password (or App Password if enabled)
    $mail->SMTPSecure = 'STARTTLS';  // Encryption
    $mail->Port = 587;  // SMTP Port for Office 365

    // Sender & Recipient
    $mail->setFrom('nur.edrinna@riverraven.com.my', 'Nur Edrinna');  // From Nur Edrinna
    $mail->addAddress('anis@riverraven.com.my', 'Anis Ruszanna');  // To Anis Ruszanna

    // Email Content
    $mail->isHTML(true);
    $mail->Subject = 'SMTP Test - Office 365';
    $mail->Body = '<h3>This is a test email from Nur Edrinna to Anis Ruszanna.</h3>';
    $mail->AltBody = 'This is a test email from Nur Edrinna to Anis Ruszanna.';

    // Send Email
    $mail->send();
    echo '✅ Email sent successfully from Nur Edrinna to Anis Ruszanna!';
} catch (Exception $e) {
    echo "❌ Email failed to send. Error: {$mail->ErrorInfo}";
}
?>
