<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Load PHPMailer
$credentials = require 'includes/credentials.php'; // Load credentials

// 📌 Function to setup PHPMailer
function setupMailer() {
    global $credentials;
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.office365.com';
        $mail->SMTPAuth = true;
        $mail->Username = $credentials['email'];
        $mail->Password = $credentials['password'];
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->setFrom($credentials['email'], 'Leave Management System');
        return $mail;
    } catch (Exception $e) {
        die("Mailer Error: " . $e->getMessage());
    }
}

// 📌 Function to notify staff that their leave request was submitted
function sendNotificationStaff($staffEmail, $staffName, $leaveDetails) {
    $mail = setupMailer();
    $mail->addAddress($staffEmail);
    $mail->Subject = "Leave Application Submitted";
    $mail->Body = "<p>Dear $staffName,</p><p>Your leave request has been submitted.</p><p>$leaveDetails</p>";
    
    return $mail->send();
}

// 📌 Function to notify manager to approve/reject leave
// function sendNotificationManager($managerEmail, $staffName, $leaveDetails) {
//     $mail = setupMailer();
//     $mail->addAddress($managerEmail);
//     $mail->Subject = "Leave Approval Request for $staffName";
//     $mail->Body = "<p>Dear Manager,</p><p>Please review $staffName's leave request.</p><p>$leaveDetails</p>";
    
//     return $mail->send();
// }

// // 📌 Function to notify director to approve/reject (only when manager submits)
// function sendNotificationDirector($directorEmail, $staffName, $leaveDetails) {
//     $mail = setupMailer();
//     $mail->addAddress($directorEmail);
//     $mail->Subject = "Director Approval Needed for $staffName's Leave";
//     $mail->Body = "<p>Dear Director,</p><p>Manager has approved $staffName's leave request.</p><p>$leaveDetails</p>";
    
//     return $mail->send();
// }

// // 📌 Function to notify staff that leave was approved/rejected
// function updateStaff($staffEmail, $staffName, $status) {
//     $mail = setupMailer();
//     $mail->addAddress($staffEmail);
//     $mail->Subject = "Leave Request $status";
//     $mail->Body = "<p>Dear $staffName,</p><p>Your leave request has been <b>$status</b>.</p>";
    
//     return $mail->send();
// }
// ?>
