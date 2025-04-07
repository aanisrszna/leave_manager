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
    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title">
                                <h4>Profile</h4>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="container-fluid page-body-wrapper">
                    <?php include('includes/navbar.php'); ?>
                    <?php include('includes/right_sidebar.php'); ?>
                    <?php include('includes/left_sidebar.php'); ?>

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
                                                <tr><th>Nickname:</th><td><?php echo htmlentities($employee['LastName']); ?></td></tr>
                                                <tr><th>Staff ID:</th><td><?php echo htmlentities($employee['Staff_ID']); ?></td></tr>
                                                <tr><th>Email:</th><td><?php echo htmlentities($employee['EmailId']); ?></td></tr>
                                                <tr><th>Phone:</th><td><?php echo htmlentities($employee['Phonenumber']); ?></td></tr>
                                                <tr><th>Date of birth:</th><td><?php echo htmlentities($employee['dob']); ?></td></tr>

                                                <tr><th>Role:</th><td><?php echo htmlentities($employee['Role']); ?></td></tr>
                                                <tr><th>Position:</th><td><?php echo htmlentities($employee['Position_Staff']); ?></td></tr>
                                                <tr><th>Hire Date:</th><td><?php echo htmlentities($employee['date_joined']); ?></td></tr>
                                                <tr><th>Available Leave:</th><td><?php echo htmlentities($employee['available_day']); ?></td></tr>
                                                <tr><th>Profile Picture:</th>
                                                    <td>
                                                        <img src="../uploads/<?php echo htmlentities($employee['location']); ?>" width="100" height="100" alt="Profile">
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

<?php include('includes/footer.php'); ?>
