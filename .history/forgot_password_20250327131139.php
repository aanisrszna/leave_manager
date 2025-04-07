<?php
require 'includes/config.php'; // Include database connection
require 'send_email.php'; // Include email function

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']); // Sanitize email input

    // Check if email exists
    $query = mysqli_query($conn, "SELECT * FROM tblemployees WHERE EmailId = '$email'");
    if (mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_array($query);
        $token = bin2hex(random_bytes(50)); // Secure random token
        $exp_time = gmdate("Y-m-d H:i:s", strtotime("+1 hour")); // Store expiry time in UTC

        // Store token in database
        mysqli_query($conn, "UPDATE tblemployees SET reset_token='$token', reset_expiry='$exp_time' WHERE EmailId='$email'");

        // Prepare reset link
        $reset_link = "http://localhost/rr_leave_portal/reset_password.php?token=$token";
        $subject = "Password Reset Request";
        $message = "<p>Click the link below to reset your password:</p>";
        $message .= "<a href='$reset_link'>$reset_link</a>";

        // Send email
        send_email($email, $subject, $message);

        echo "A password reset link has been sent to your email.";
    } else {
        echo "No account found with this email.";
    }
}
?>

<form method="post">
    <label>Enter your email:</label>
    <input type="email" name="email" required>
    <button type="submit" name="submit">Submit</button>
</form>
