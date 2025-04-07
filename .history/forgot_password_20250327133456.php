<?php
require 'includes/config.php'; // Include database connection
require 'send_email.php'; // Include email function

$successMessage = $errorMessage = "";

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

        $successMessage = "A password reset link has been sent to your email.";
    } else {
        $errorMessage = "No account found with this email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow p-4">
                    <h3 class="text-center">Forgot Password</h3>
                    <p class="text-center">Enter your email to receive a password reset link.</p>

                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <button type="submit" name="submit" class="btn btn-primary w-100">Submit</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="index.php">Back to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
