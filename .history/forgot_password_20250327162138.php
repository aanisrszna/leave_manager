<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="card p-4 shadow-lg" style="width: 400px;">
        <h4 class="text-center mb-3">Reset Password</h4>
        <?php
        require 'includes/config.php'; // Include the database connection
        require 'send_email.php'; // Include email function

        if (isset($_POST['submit'])) {
            $email = mysqli_real_escape_string($conn, $_POST['email']); // Sanitize email input

            // Check if email exists
            $query = mysqli_query($conn, "SELECT * FROM tblemployees WHERE EmailId = '$email'");
            if (mysqli_num_rows($query) > 0) {
                $row = mysqli_fetch_array($query);
                $token = bin2hex(random_bytes(50)); // Secure token
                $exp_time = date("Y-m-d H:i:s", strtotime("+1 hour")); // Token expiry time

                // Store token in database
                mysqli_query($conn, "UPDATE tblemployees SET reset_token='$token', reset_expiry='$exp_time' WHERE EmailId='$email'");

                // Prepare reset link
                $reset_link = "http://localhost/rr_leave_portal/reset_password.php?token=$token";
                $subject = "Password Reset Request";
                $message = "<p>Click the link below to reset your password:</p>";
                $message .= "<a href='$reset_link'>$reset_link</a>";

                // Send email
                send_email($email, $subject, $message);

                echo "<div class='alert alert-success'>A password reset link has been sent to your email.</div>";
            } else {
                echo "<div class='alert alert-danger'>No account found with this email.</div>";
            }
        }
        ?>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Enter your email:</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary w-100">Submit</button>
        </form>
    </div>

    <!-- Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
