<?php
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
echo "Current Server Time: " . date("Y-m-d H:i:s") . "<br>";

// Check if token has expired
if (strtotime($row['reset_expiry']) < time()) {
    die("Token has expired. Please request a new password reset.");
}

// If token is valid, proceed with password reset form
?>

<h2>Reset Your Password</h2>
<form method="post">
    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
    <label>New Password:</label>
    <input type="password" name="new_password" required><br>
    <label>Confirm Password:</label>
    <input type="password" name="confirm_password" required><br>
    <button type="submit" name="reset">Reset Password</button>
</form>

<?php
if (isset($_POST['reset'])) {
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Check if passwords match
    if ($new_password !== $confirm_password) {
        die("Passwords do not match.");
    }

    // Hash new password before storing
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    // Update password and remove token
    $update_query = mysqli_query($conn, "UPDATE tblemployees SET Password='$hashed_password', reset_token=NULL, reset_expiry=NULL WHERE reset_token='$token'");

    if ($update_query) {
        echo "Password successfully updated. <a href='index.php'>Login here</a>";
    } else {
        echo "Something went wrong. Please try again.";
    }
}
?>
