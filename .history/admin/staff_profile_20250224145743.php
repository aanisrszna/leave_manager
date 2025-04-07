<?php 
include('includes/header.php'); 
include('../includes/session.php'); 

// Check if staff_id is provided in the URL
if (isset($_GET['staff_id'])) {
    $staff_id = mysqli_real_escape_string($conn, $_GET['staff_id']);

    // Fetch staff details based on the provided staff_id
    $query = "SELECT * FROM tblemployees WHERE emp_id = '$staff_id'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $employee = mysqli_fetch_assoc($result);
    } else {
        echo "<script>alert('Staff not found!'); document.location='dashboard.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('Invalid access!'); document.location='dashboard.php';</script>";
    exit();
}
?>

<body>
    <div class="container-fluid page-body-wrapper">
        <?php include('includes/navbar.php'); ?>
        <?php include('includes/right_sidebar.php'); ?>

        <div class="main-panel">
            <div class="content-wrapper">
                <div class="row">
                    <!-- Staff Details -->
                    <div class="col-md-6 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Staff Details</h4>
                                <table class="table">
                                    <tr><th>First Name:</th><td><?php echo htmlentities($employee['FirstName']); ?></td></tr>
                                    <tr><th>Last Name:</th><td><?php echo htmlentities($employee['LastName']); ?></td></tr>
                                    <tr><th>Email:</th><td><?php echo htmlentities($employee['EmailId']); ?></td></tr>
                                    <tr><th>Phone:</th><td><?php echo htmlentities($employee['Phonenumber']); ?></td></tr>
                                    <tr><th>Department:</th><td><?php echo htmlentities($employee['Department']); ?></td></tr>
                                    <tr><th>Position:</th><td><?php echo htmlentities($employee['Position_Staff']); ?></td></tr>
                                    <tr><th>Hire Date:</th><td><?php echo htmlentities($employee['date_joined']); ?></td></tr>
                                    <tr><th>Profile Picture:</th>
                                        <td>
                                            <img src="uploads/<?php echo htmlentities($employee['location']); ?>" width="100" height="100" alt="Profile">
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Update Profile -->
                    <div class="col-md-6 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Update Profile</h4>
                                <form method="post">
                                    <div class="form-group">
                                        <label>First Name</label>
                                        <input type="text" class="form-control" name="first_name" value="<?php echo htmlentities($employee['FirstName']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Last Name</label>
                                        <input type="text" class="form-control" name="last_name" value="<?php echo htmlentities($employee['LastName']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Phone Number</label>
                                        <input type="text" class="form-control" name="phone_number" value="<?php echo htmlentities($employee['PhoneNumber']); ?>" required>
                                    </div>
                                    <button type="submit" name="update_profile" class="btn btn-primary">Update</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Update Profile Picture -->
                    <div class="col-md-6 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Update Profile Picture</h4>
                                <form method="post" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label>Upload Image</label>
                                        <input type="file" class="form-control" name="profile_image" required>
                                    </div>
                                    <button type="submit" name="update_picture" class="btn btn-primary">Upload</button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>

<?php 
// Update Profile
if (isset($_POST['update_profile'])) {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);

    $update_query = "UPDATE tblemployees SET FirstName='$first_name', LastName='$last_name', PhoneNumber='$phone_number' WHERE emp_id='$staff_id'";
    $update_result = mysqli_query($conn, $update_query);

    if ($update_result) {
        echo "<script>alert('Profile Updated Successfully!'); document.location='staff_profile.php?staff_id=$staff_id';</script>";
    } else {
        echo "<script>alert('Error Updating Profile!');</script>";
    }
}

// Update Profile Picture
if (isset($_POST['update_picture'])) {
    $image = $_FILES['profile_image']['name'];
    $target = "uploads/" . basename($image);

    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target)) {
        $update_image_query = "UPDATE tblemployees SET ProfileImage='$image' WHERE emp_id='$staff_id'";
        $update_image_result = mysqli_query($conn, $update_image_query);

        if ($update_image_result) {
            echo "<script>alert('Profile Picture Updated!'); document.location='staff_profile.php?staff_id=$staff_id';</script>";
        } else {
            echo "<script>alert('Error Updating Picture!');</script>";
        }
    } else {
        echo "<script>alert('Error Uploading Image!');</script>";
    }
}
?>

<?php include('includes/footer.php'); ?>
