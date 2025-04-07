<?php
require 'includes/config.php'; // Include the database connection

if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($conn, $_GET['token']); // Sanitize token input

    echo "Token received: " . htmlspecialchars($token) . "<br>";

    // Check if token exists and is still valid
    $query = mysqli_query($conn, "SELECT * FROM tblemployees WHERE reset_token = '$token' AND reset_expiry > NOW()");
    
    if (!$query) {
        die("Query failed: " . mysqli_error($conn)); // Debug query error
    }

    if (mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_array($query);
        $email = $row['EmailId'];

        echo "Email found: " . htmlspecialchars($email) . "<br>";

        if (isset($_POST['submit'])) {
            $new_password = mysqli_real_escape_string($conn, $_POST['password']);

            if (empty($new_password)) {
                echo "Password cannot be empty.<br>";
            } else {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT); // Secure password hashing

                // Update password and clear reset token
                $update_query = "UPDATE tblemployees SET Password='$hashed_password', reset_token=NULL, reset_expiry=NULL WHERE EmailId='$email'";
                $update_result = mysqli_query($conn, $update_query);

                if ($update_result) {
                    echo "Your password has been updated successfully.<br>";
                } else {
                    echo "Error updating password: " . mysqli_error($conn) . "<br>";
                }
            }
        }
    } else {
        echo "Invalid or expired reset link.<br>";
    }
} else {
    echo "No reset token provided.<br>";
}
?>

<form method="post">
    <label>Enter New Password:</label>
    <input type="password" name="password" required>
    <button type="submit" name="submit">Reset Password</button>
</form>
