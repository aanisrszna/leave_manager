<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Load PHPMailer

function send_email($to, $subject, $message) {
    $mail = new PHPMailer(true);
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.office365.com'; // Office 365 SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'nur.edrinna@riverraven.com.my'; // Your email
        $mail->Password = '@Faiq0104'; // Your email password
        $mail->SMTPSecure = 'STARTTLS';
        $mail->Port = 587;

        // Sender & Recipient
        $mail->setFrom('nur.edrinna@riverraven.com.my', 'River Raven Leave System');
        $mail->addAddress($to);

        // Email Content
        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
        return "Email sent successfully to $to";
    } catch (Exception $e) {
        return "Email failed: " . $mail->ErrorInfo;
    }
}
?>
