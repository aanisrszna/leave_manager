<?php include('includes/header.php') ?>
<?php include('../includes/session.php') ?>

<?php
if (isset($_POST['add_staff'])) {
    $fname = $_POST['firstname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $gender = $_POST['gender'];
    $dob = ''; // Will be generated
    $department = $_POST['department'];
    $address = $_POST['address'];
    $user_role = $_POST['user_role'];
    $phonenumber = $_POST['phonenumber'];
    $position_staff = $_POST['position_staff'];
    $staff_id = $_POST['staff_id'];
    $status = 1;
    $date_joined = $_POST['date_joined'];
    $emergency_contact = $_POST['emergency_contact'];
    $car_plate = $_POST['car_plate'];
    $reporting_to = $_POST['reporting_to'];
    $ic_number = $_POST['ic_number'];
    $emergency_name = $_POST['emergency_name'];
    $emergency_relation = $_POST['emergency_relation'];

    // Auto-generate service year (current year minus year joined)
    $current_year = date('Y');
    $joined_year = date('Y', strtotime($date_joined));
    $service_year = $current_year - $joined_year;

    // Check if the email already exists in the database
    $query = mysqli_query($conn, "SELECT * FROM tblemployees WHERE EmailId = '$email'") or die(mysqli_error($conn));
    $count = mysqli_num_rows($query);

    if ($count > 0) {
        echo "<script>alert('Data Already Exists');</script>";
    } else {
        // Insert the new staff record
        $sql = "INSERT INTO tblemployees (
            FirstName, EmailId, Password, Gender, Dob, Department, Address, role, Phonenumber, Status, location, Staff_ID, Position_Staff, Date_Joined, Emergency_Contact, Car_Plate, Reporting_To, IC_Number, Service_Year, Emergency_Name, Emergency_Relation
        ) VALUES (
            '$fname', '$email', '$password', '$gender', '$dob', '$department', '$address', '$user_role', '$phonenumber', '$status', 'NO-IMAGE-AVAILABLE.jpg', '$staff_id', '$position_staff', '$date_joined', '$emergency_contact', '$car_plate', '$reporting_to', '$ic_number', '$service_year', '$emergency_name', '$emergency_relation'
        )";
        mysqli_query($conn, $sql) or die(mysqli_error($conn));
        echo "<script>alert('Staff Successfully Added');</script>";
        echo "<script>window.location = 'add_staff.php';</script>";
    }
}
?>

<body>
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
                                            <label>Staff ID:</label>
                                            <input name="staff_id" type="text" class="form-control" required autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>IC Number:</label>
                                            <input name="ic_number" type="text" class="form-control" required autocomplete="off"  placeholder="YYMMDD-XX-XXXX" oninput="generateDOB()">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Date Of Birth:</label>
                                            <input name="dob" id="dob" type="text" class="form-control date-picker" required readonly>
                                        </div>
                                    </div>
                                    <!-- 

 -->
                                </div>
                                <div class="row">
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
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Date Joined:</label>
                                            <input name="date_joined" type="date" class="form-control" required oninput="generateServiceYear()">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Service Year:</label>
                                            <input name="service_year" id="service_year" type="text" class="form-control" required readonly>
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
                                            <label>Position:</label>
                                            <input name="position_staff" type="text" class="form-control wizard-required" required autocomplete="off">
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
                                            <label>Car Plate:</label>
                                            <input name="car_plate" type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Reporting To:</label>
                                            <input name="reporting_to" type="text" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix">
                                    <div class="pull-left">
                                        <h4 class="text-blue h4">Emergency Information</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Emergency Relation:</label>
                                            <input name="emergency_relation" type="text" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Emergency Contact:</label>
                                            <input name="emergency_contact" type="text" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label>Emergency Name:</label>
                                            <input name="emergency_name" type="text" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <label>Address:</label>
                                            <input name="address" type="text" class="form-control" required autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                
                                
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
    <script>
        // Function to generate DOB based on IC Number
function generateDOB() {
    let icNumber = document.querySelector('input[name="ic_number"]').value;
    if (/^\d{6}-\d{2}-\d{4}$/.test(icNumber)) {
        let year = parseInt(icNumber.substring(0, 2), 10);
        let month = icNumber.substring(2, 4);
        let day = icNumber.substring(4, 6);
        const currentYear = new Date().getFullYear();
        const currentYearPrefix = currentYear.toString().substring(0, 2);
        year = year > parseInt(currentYearPrefix, 10) ? '19' + year : '20' + year;
        document.getElementById('dob').value = `${year}-${month}-${day}`;
    }
}





        // Function to calculate Service Year based on Date Joined
        function generateServiceYear() {
            let dateJoined = document.querySelector('input[name="date_joined"]').value;
            if (dateJoined) {
                let currentYear = new Date().getFullYear();
                let joinedYear = new Date(dateJoined).getFullYear();
                let serviceYear = currentYear - joinedYear;
                document.getElementById('service_year').value = serviceYear;
            }
        }
    </script>

    <?php include('includes/scripts.php') ?>
</body>
</html>
