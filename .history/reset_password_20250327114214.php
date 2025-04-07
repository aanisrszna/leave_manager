<?php
require 'includes/config.php'; // Include the database connection

if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($conn, $_GET['token']); // Sanitize token input

    // Check if token exists and is still valid
    $query = mysqli_query($conn, "SELECT * FROM tblemployees WHERE reset_token = '$token' AND reset_expiry > NOW()");
    if (mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_array($query);
        $email = $row['EmailId'];

        if (isset($_POST['submit'])) {
            $new_password = mysqli_real_escape_string($conn, $_POST['password']);
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT); // Secure password hashing

            // Update password and clear reset token
            mysqli_query($conn, "UPDATE tblemployees SET Password='$hashed_password', reset_token=NULL, reset_expiry=NULL WHERE EmailId='$email'");
            
            echo "Your password has been updated successfully.";
        }
    } else {
        echo "Invalid or expired reset link.";
    }
} else {
    echo "No reset token provided.";
}
?>

<form method="post">
    <label>Enter New Password:</label>
    <input type="password" name="password" required>
    <button type="submit" name="submit">Reset Password</button>
</form>
