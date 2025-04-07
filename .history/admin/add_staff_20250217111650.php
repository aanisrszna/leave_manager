<?php include('includes/header.php') ?>
<?php include('../includes/session.php') ?>

<?php
if (isset($_POST['add_staff'])) {
    $fname = $_POST['firstname'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $department = $_POST['department'];
    $address = $_POST['address'];
    $user_role = $_POST['user_role'];
    $phonenumber = $_POST['phonenumber'];
    $position_staff = $_POST['position_staff'];
    $staff_id = $_POST['staff_id'];
    $status = 1;
    $date_joined = $_POST['date_joined']; // New field
    $emergency_contact = $_POST['emergency_contact']; // New field
    $car_plate = $_POST['car_plate']; // New field
    $reporting_to = $_POST['reporting_to']; // New field

    // Check if the email already exists in the database
    $query = mysqli_query($conn, "SELECT * FROM tblemployees WHERE EmailId = '$email'") or die(mysqli_error($conn));
    $count = mysqli_num_rows($query);

    if ($count > 0) {
        echo "<script>alert('Data Already Exists');</script>";
    } else {
        // Insert the new staff record
        $sql = "INSERT INTO tblemployees (
            FirstName, EmailId, Password, Gender, Dob, Department, Address, role, Phonenumber, Status, location, Staff_ID, Position_Staff, Date_Joined, Emergency_Contact, Car_Plate, Reporting_To
        ) VALUES (
            '$fname', '$email', '$password', '$gender', '$dob', '$department', '$address', '$user_role', '$phonenumber', '$status', 'NO-IMAGE-AVAILABLE.jpg', '$staff_id', '$position_staff', '$date_joined', '$emergency_contact', '$car_plate', '$reporting_to'
        )";
        mysqli_query($conn, $sql) or die(mysqli_error($conn));
        echo "<script>alert('Staff Successfully Added');</script>";
        echo "<script>window.location = 'add_staff.php';</script>";
    }
}
?>

<body>
    <!-- <div class="pre-loader">
        <div class="pre-loader-box">
            <div class="loader-logo">
                <img src="../vendors/images/logoMCT.png" alt="" style="width:400px; height:150px;">
            </div>
            <div class='loader-progress' id="progress_div">
                <div class='bar' id='bar1'></div>
            </div>
            <div class='percent' id='percent1'>0%</div>
            <div class="loading-text">Loading...</div>
        </div>
    </div> -->

    <?php include('includes/navbar.php') ?>
    <?php include('includes/right_sidebar.php') ?>
    <?php include('includes/left_sidebar.php') ?>

    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="title">
                                <h4>Add Staff</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="admin_dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Add Staff</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="pd-20 card-box mb-30">
                    <div class="clearfix">
                        <div class="pull-left">
                            <h4 class="text-blue h4">Staff Form</h4>
                            <p class="mb-20">Fill in the details to add new staff.</p>
                        </div>
                    </div>
                    <div class="wizard-content">
                        <form method="post" action="">
                            <section>
                                <div class="row">
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Full Name:</label>
                                            <input name="firstname" type="text" class="form-control wizard-required" required autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Email Address:</label>
                                            <input name="email" type="email" class="form-control" autocomplete="off" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Password:</label>
                                            <!-- Hidden dummy input to prevent autofill -->
                                            <input type="password" style="opacity: 0; position: absolute; height: 0;" autocomplete="new-password">
                                            <input name="password" type="password" placeholder="**********" class="form-control" autocomplete="new-password" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Position:</label>
                                            <input name="position_staff" type="text" class="form-control wizard-required" required autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Staff ID:</label>
                                            <input name="staff_id" type="text" class="form-control" required autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Date Joined:</label>
                                            <input name="date_joined" type="date" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>User Role:</label>
                                            <select name="user_role" class="custom-select form-control" required>
                                                <option value="">Select Role</option>
                                                <option value="Admin">Admin</option>
                                                <option value="Manager">Manager</option>
                                                <option value="Staff">Staff</option>
                                                <option value="Director">Director</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Department:</label>
                                            <select name="department" class="custom-select form-control" required>
                                                <option value="">Select Department</option>
                                                <?php
                                                $query = mysqli_query($conn, "SELECT * FROM tbldepartments");
                                                while ($row = mysqli_fetch_array($query)) {
                                                    echo "<option value='" . $row['DepartmentShortName'] . "'>" . $row['DepartmentName'] . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Gender:</label>
                                            <select name="gender" class="custom-select form-control" required>
                                                <option value="">Select Gender</option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Phone Number:</label>
                                            <input name="phonenumber" type="text" class="form-control" required autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Date Of Birth:</label>
                                            <input name="dob" type="text" class="form-control date-picker" required autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Address:</label>
                                            <input name="address" type="text" class="form-control" required autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Emergency Contact:</label>
                                            <input name="emergency_contact" type="text" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Car Plate:</label>
                                            <input name="car_plate" type="text" class="form-control">
                                        </div>
                                    </div>
                                </div>
\

                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <button class="btn btn-primary" name="add_staff" type="submit">Add Staff</button>
                                    </div>
                                </div>
                            </section>
                        </form>
                    </div>
                </div>
            </div>
            <?php include('includes/footer.php'); ?>
        </div>
    </div>

    <?php include('includes/scripts.php') ?>
</body>
</html>
