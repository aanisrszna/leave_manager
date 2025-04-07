<?php
require 'includes/config.php'; // Include database connection

if (!isset($_GET['token'])) {
    die("Invalid reset link.");
}

$token = mysqli_real_escape_string($conn, $_GET['token']);

// Fetch token details from database
$query = mysqli_query($conn, "SELECT EmailId, reset_token, reset_expiry FROM tblemployees WHERE reset_token='$token'");
$row = mysqli_fetch_assoc($query);

if (!$row) {
    die("Invalid or expired reset link.");
}

// Convert expiry time from UTC to Malaysia time
$expiry_time = new DateTime($row['reset_expiry'], new DateTimeZone('UTC'));
$expiry_time->setTimezone(new DateTimeZone('Asia/Kuala_Lumpur'));

if ($expiry_time->getTimestamp() < time()) {
    die("Token has expired. Please request a new password reset.");
}

// If token is valid, allow password reset
$successMessage = $errorMessage = "";

if (isset($_POST['submit'])) {
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $errorMessage = "Passwords do not match!";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        mysqli_query($conn, "UPDATE tblemployees SET Password='$hashed_password', reset_token=NULL, reset_expiry=NULL WHERE EmailId='{$row['EmailId']}'");
        $successMessage = "Your password has been reset successfully.";
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
                document.getElementById("errorMessage").innerText = "Passwords do not match!";
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
                    <?php if ($errorMessage) echo "<p class='text-danger'>$errorMessage</p>"; ?>
                    <?php if ($successMessage) echo "<p class='text-success'>$successMessage</p>"; ?>
                    <form method="post" onsubmit="return validateForm();">
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                        </div>
                        <p id="errorMessage" class="text-danger"></p>
                        <button type="submit" name="submit" class="btn btn-primary w-100">Reset Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
