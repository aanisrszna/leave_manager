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

echo "Token received: " . $token . "<br>";
echo "Database Token: " . $row['reset_token'] . "<br>";
echo "Token Expiry Time (Malaysia): " . $expiry_time->format('Y-m-d H:i:s') . "<br>";
echo "Current Server Time (Malaysia): " . date("Y-m-d H:i:s") . "<br>";

if ($expiry_time->getTimestamp() < time()) {
    die("Token has expired. Please request a new password reset.");
}

// If token is valid, allow password reset
if (isset($_POST['submit'])) {
    $new_password = mysqli_real_escape_string($conn, $_POST['password']);
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    // Update password and clear reset token
    mysqli_query($conn, "UPDATE tblemployees SET Password='$hashed_password', reset_token=NULL, reset_expiry=NULL WHERE EmailId='{$row['EmailId']}'");

    echo "Your password has been reset successfully.";
}
?>

<form method="post">
    <label>Enter your new password:</label>
    <input type="password" name="password" required>
    <button type="submit" name="submit">Reset Password</button>
</form>
