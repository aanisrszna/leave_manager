<?php
    require "vendor/autoload.php";

    use PHPMailer\PHPMailer\PHPMailer;
    
    // require 'PHPMailer/src/Exception.php';
    // require 'PHPMailer/src/PHPMailer.php';
    // require 'PHPMailer/src/SMTP.php';

    require 'credentials.php';

    function send_mail($fullname,$hEmailId, $leave_type, $hodFullname) {

        $mail = new PHPMailer(true);

        //$mail->SMTPDebug = 4;                               // Enable verbose debug output

        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = EMAIL;                 // SMTP username
        $mail->Password = PASS;                           // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;                           // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                    // TCP port to connect to

        $mail->setFrom(EMAIL, 'Application');
        $mail->addAddress($hEmailId);              // Name is optional
        $mail->addReplyTo(EMAIL);
        
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = " ".$leave_type." Application";
        $mail->Body    = "
            <p></p><br>
            Hi ".$hodFullname.",  ".$fullname." has applied<br>
            for ".$leave_type.".<br><br>
            Kindly login into the Application Portal and review.<br>
            THANK YOU.<br><br>";
        $mail->AltBody = 'Application from the CT Management system';

        if(!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo "<script>alert('Application successfully submitted.');</script>";
            echo "<script type='text/javascript'> document.location = 'table.php'; </script>";
        }
    }
?>