<?php include('includes/header.php') ?>
<?php include('../includes/session.php') ?>

<?php 


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
    <?php include('includes/navbar.php') ?>
    <?php include('includes/right_sidebar.php') ?>
    <?php include('includes/left_sidebar.php') ?>
    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header text-center">
                    <h4 class="font-weight-bold">Staff Profile</h4>
                </div>
                <div class="container-fluid page-body-wrapper">
                    <?php include('includes/navbar.php'); ?>
                    <?php include('includes/right_sidebar.php'); ?>
                    <?php include('includes/left_sidebar.php'); ?>
                    <div class="main-panel">
                        <div class="content-wrapper">
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="card text-center p-4">
                                        <div class="card-body">
                                            <!-- Profile Picture -->
                                            <div class="profile-img mb-3">
                                                <img src="../uploads/<?php echo htmlentities($employee['location']); ?>" class="rounded-circle" width="150" height="150" alt="Profile">
                                            </div>
                                            <h4 class="card-title"> <?php echo htmlentities($employee['FirstName']); ?> </h4>
                                            <p class="text-muted"> <?php echo htmlentities($employee['Position_Staff']); ?> </p>
                                            <hr>
                                            <!-- Staff Details -->
                                            <div class="row text-left">
                                                <div class="col-md-6">
                                                    <p><strong>Email:</strong> <?php echo htmlentities($employee['EmailId']); ?></p>
                                                    <p><strong>Phone Number:</strong> <?php echo htmlentities($employee['Phonenumber']); ?></p>
                                                    <p><strong>Date of Birth:</strong> <?php echo htmlentities($employee['Dob']); ?></p>
                                                    <p><strong>Gender:</strong> <?php echo htmlentities($employee['Gender']); ?></p>
                                                    <p><strong>Address:</strong> <?php echo htmlentities($employee['Address']); ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Staff ID:</strong> <?php echo htmlentities($employee['Staff_ID']); ?></p>

                                                    <p><strong>Role:</strong> <?php echo htmlentities($employee['role']); ?></p>
                                                    <p><strong>Date Joined:</strong> <?php echo htmlentities($employee['date_joined']); ?></p>
                                                    <p><strong>Car Plate:</strong> <?php echo htmlentities($employee['Car_Plate']); ?></p>
                                                    <p><strong>Reporting To:</strong> <?php echo htmlentities($employee['Reporting_To']); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8 mt-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h4 class="card-title">Emergency Contact</h4>
                                            <p><strong>Name:</strong> <?php echo htmlentities($employee['Emergency_Name']); ?></p>
                                            <p><strong>Relation:</strong> <?php echo htmlentities($employee['Emergency_Relation']); ?></p>
                                            <p><strong>Contact:</strong> <?php echo htmlentities($employee['Emergency_Contact']); ?></p>
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
