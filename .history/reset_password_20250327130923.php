<?php
session_start();
date_default_timezone_set('Asia/Kuala_Lumpur'); // Set Malaysia Timezone
require 'includes/config.php'; // Ensure DB connection is included

if (!isset($_GET['token'])) {
    die("No token provided.");
}

$token = mysqli_real_escape_string($conn, $_GET['token']);
echo "Token received: " . $token . "<br>";

// Fetch user with this token
$query = mysqli_query($conn, "SELECT EmailId, reset_token, reset_expiry FROM tblemployees WHERE reset_token = '$token' LIMIT 1");

if (mysqli_num_rows($query) == 0) {
    die("Invalid or expired reset link.");
}

$row = mysqli_fetch_assoc($query);

// Debugging: Display fetched values
echo "Database Token: " . $row['reset_token'] . "<br>";
echo "Token Expiry Time: " . $row['reset_expiry'] . "<br>";
echo "Current Server Time (Malaysia): " . date("Y-m-d H:i:s") . "<br>";

// Check if token has expired
if (strtotime($row['reset_expiry']) < time()) {
    die("Token has expired. Please request a new password reset.");
}

$email = $row['EmailId'];

// Handle form submission
if (isset($_POST['reset_password'])) {
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Check if passwords match
    if ($new_password !== $confirm_password) {
        echo "<p style='color:red;'>Passwords do not match.</p>";
    } else {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password in the database and remove the reset token
        $update_query = mysqli_query($conn, "UPDATE tblemployees SET Password='$hashed_password', reset_token=NULL, reset_expiry=NULL WHERE EmailId='$email'");

        if ($update_query) {
            echo "<p style='color:green;'>Password reset successful. You can now <a href='index.php'>login</a>.</p>";
        } else {
            echo "<p style='color:red;'>Error updating password. Please try again.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body>
    <h2>Reset Your Password</h2>
    <form method="post">
        <label>New Password:</label>
        <input type="password" name="new_password" required><br>

        <label>Confirm Password:</label>
        <input type="password" name="confirm_password" required><br>

        <button type="submit" name="reset_password">Reset Password</button>
    </form>
</body>
</html>
