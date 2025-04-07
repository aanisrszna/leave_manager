<?php
require 'includes/config.php'; // Include database connection

if (!isset($_GET['token'])) {
    echo "<script>alert('Invalid reset link.'); window.location.href='index.php';</script>";
    exit;
}

$token = mysqli_real_escape_string($conn, $_GET['token']);

// Fetch token details from database
$query = mysqli_query($conn, "SELECT EmailId, reset_token, reset_expiry FROM tblemployees WHERE reset_token='$token'");
$row = mysqli_fetch_assoc($query);

if (!$row) {
    echo "<script>alert('Invalid or expired reset link.'); window.location.href='index.php';</script>";
    exit;
}

// Convert expiry time from UTC to Malaysia time
$expiry_time = new DateTime($row['reset_expiry'], new DateTimeZone('UTC'));
$expiry_time->setTimezone(new DateTimeZone('Asia/Kuala_Lumpur'));

if ($expiry_time->getTimestamp() < time()) {
    echo "<script>alert('Token has expired. Please request a new password reset.'); window.location.href='index.php';</script>";
    exit;
}

// If token is valid, allow password reset
if (isset($_POST['submit'])) {
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!');</script>";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        mysqli_query($conn, "UPDATE tblemployees SET Password='$hashed_password', reset_token=NULL, reset_expiry=NULL WHERE EmailId='{$row['EmailId']}'");
        echo "<script>alert('Your password has been reset successfully! Redirecting to login...'); window.location.href='index.php';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function validateForm() {
            let password = document.getElementById("password").value;
            let confirmPassword = document.getElementById("confirm_password").value;
            if (password !== confirmPassword) {
                alert("Passwords do not match!");
                return false;
            }
            return true;
        }
    </script>
</head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow p-4">
                    <h3 class="text-center">Reset Password</h3>
                    <form method="post" onsubmit="return validateForm();">
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                        </div>
                        <button type="submit" name="submit" class="btn btn-primary w-100">Reset Password</button>
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
