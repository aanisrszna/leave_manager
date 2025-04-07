<?php

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $query = mysqli_query($conn, "SELECT * FROM tblemployees WHERE reset_token='$token' AND reset_expiry >= NOW()");
    
    if (mysqli_num_rows($query) > 0) {
        if (isset($_POST['reset'])) {
            $new_password = mysqli_real_escape_string($conn, $_POST['password']);
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update password and remove token
            mysqli_query($conn, "UPDATE tblemployees SET Password='$hashed_password', reset_token=NULL, reset_expiry=NULL WHERE reset_token='$token'");
            
            echo "Password has been reset successfully. <a href='index.php'>Login</a>";
        }
    } else {
        echo "Invalid or expired token.";
    }
} else {
    echo "Invalid request.";
}
?>

<form method="post">
    <label>Enter new password:</label>
    <input type="password" name="password" required>
    <button type="submit" name="reset">Reset Password</button>
</form>
