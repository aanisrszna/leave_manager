<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php'; // ✅ Correct path to load PHPMailer
$credentials = require __DIR__ . '/../includes/credentials.php'; // ✅ Correct path to load credentials

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
        $mail->isHTML(true); // ✅ Allow HTML email formatting
        return $mail;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $e->getMessage());
        return null;
    }
}

// 📌 Function to notify staff that their leave request was submitted
c    $mail = setupMailer();
    if (!$mail) {
        return false; // Exit if mailer setup failed
    }

    try {
        $mail->addAddress($staffEmail);
        $mail->Subject = "Leave Application Submitted";
        $mail->Body = "
            <p>Dear <strong>$staffName</strong>,</p>
            <p>Your leave request has been successfully submitted. Below are the details:</p>
            <ul>
                <li><strong>Leave Type:</strong> $leaveType</li>
                <li><strong>From:</strong> $dateFrom</li>
                <li><strong>To:</strong> $dateTo</li>
            </ul>
            <p>Please wait for approval from the Head of Department and the Director.</p>
            <p>Thank you,</p>
            <p><strong>Leave Management System</strong></p>
        ";

        return $mail->send();
    } catch (Exception $e) {
        error_log("Email to $staffEmail failed: " . $mail->ErrorInfo);
        return false;
    }
}
?>


<!-- // 📌 Function to notify manager to approve/reject leave
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
//  -->
