<?php include('includes/header.php'); ?>
<?php include('../includes/session.php'); ?>

<body>
    <div class="container-fluid page-body-wrapper">
        <?php include('includes/navbar.php')?>

        <?php include('includes/right_sidebar.php')?>

        <?php include('includes/left_sidebar.php')?>
        <div class="main-panel">
            <div class="content-wrapper">
                <div class="row">
                    <div class="col-md-6 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Update Profile</h4>
                                <form method="post" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label>First Name</label>
                                        <input type="text" class="form-control" name="first_name" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Last Name</label>
                                        <input type="text" class="form-control" name="last_name" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Phone Number</label>
                                        <input type="text" class="form-control" name="phone_number" required>
                                    </div>
                                    <button type="submit" name="update_profile" class="btn btn-primary">Update</button>
                                </form>
                            </div>
                        </div>
                    </div>

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
if (isset($_POST['update_profile'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone_number = $_POST['phone_number'];
    
    $query = "UPDATE staff SET first_name='$first_name', last_name='$last_name', phone_number='$phone_number' WHERE id='$session_id'";
    $result = mysqli_query($conn, $query);
    if ($result) {
        echo "<script>alert('Profile Updated Successfully!'); document.location='staff_profile.php';</script>";
    } else {
        echo "<script>alert('Error Updating Profile!');</script>";
    }
}

if (isset($_POST['update_picture'])) {
    $image = $_FILES['profile_image']['name'];
    $target = "uploads/" . basename($image);
    
    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target)) {
        $query = "UPDATE staff SET profile_image='$image' WHERE id='$session_id'";
        $result = mysqli_query($conn, $query);
        if ($result) {
            echo "<script>alert('Profile Picture Updated!'); document.location='staff_profile.php';</script>";
        } else {
            echo "<script>alert('Error Updating Picture!');</script>";
        }
    } else {
        echo "<script>alert('Error Uploading Image!');</script>";
    }
}
?>

<?php include('includes/footer.php'); ?>
