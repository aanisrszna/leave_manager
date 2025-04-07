<?php 
include('includes/header.php'); 
include('../includes/session.php'); 
$get_id = $_GET['edit']; 

// Update employee and assign leave types
if (isset($_POST['update_staff'])) {
    $fname = $_POST['firstname'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $department = $_POST['department'];
    $address = $_POST['address'];
    $user_role = $_POST['user_role'];
    $phonenumber = $_POST['phonenumber'];
    $position_staff = $_POST['position_staff'];
    $staff_id = $_POST['staff_id'];
    $date_joined = $_POST['date_joined'];
    $emergency_contact = $_POST['emergency_contact'];
    $car_plate = $_POST['car_plate'];
    $reporting_to = $_POST['reporting_to'];
    $ic_number = $_POST['ic_number'];
    $service_year = $_POST['service_year'];
    $emergency_name = $_POST['emergency_name'];
    $emergency_relation = $_POST['emergency_relation'];

    // Update employee details with new fields
    $result = mysqli_query($conn, "UPDATE tblemployees SET 
        FirstName='$fname', 
        EmailId='$email', 
        Gender='$gender', 
        Dob='$dob', 
        Department='$department', 
        Address='$address', 
        role='$user_role', 
        Phonenumber='$phonenumber', 
        Position_Staff='$position_staff', 
        Staff_ID='$staff_id', 
        Date_Joined='$date_joined',
        Emergency_Contact='$emergency_contact',
        Car_Plate='$car_plate',
        Reporting_To='$reporting_to',
        IC_Number='$ic_number', 
        Service_Year='$service_year', 
        Emergency_Name='$emergency_name', 
        Emergency_Relation='$emergency_relation'
        WHERE emp_id='$get_id'");

    if ($result) {
        // Get the current assigned leave types for the employee
        $current_leave_types = [];
        $current_leave_query = mysqli_query($conn, "SELECT leave_type_id FROM employee_leave WHERE emp_id = '$get_id'");
        while ($leave = mysqli_fetch_array($current_leave_query)) {
            $current_leave_types[] = $leave['leave_type_id'];
        }

        // Get the submitted leave types from the form
        $leave_type_ids = isset($_POST['leave_type_ids']) ? $_POST['leave_type_ids'] : [];

        // 1. Remove any leave types that are unchecked (present in current but not in the submitted leave types)
        $leave_to_remove = array_diff($current_leave_types, $leave_type_ids);
        foreach ($leave_to_remove as $leave_id) {
            $remove_leave = mysqli_query($conn, "DELETE FROM employee_leave WHERE emp_id = '$get_id' AND leave_type_id = '$leave_id'");
            if (!$remove_leave) {
                die(mysqli_error($conn));
            }
        }

        // 2. Add new leave types that are checked (present in the submitted leave types but not in current assigned leave types)
        $leave_to_add = array_diff($leave_type_ids, $current_leave_types);
        foreach ($leave_to_add as $leave_id) {
            // Fetch available_day from tblleavetype
            $leave_query = mysqli_query($conn, "SELECT assigned_day FROM tblleavetype WHERE id = '$leave_id'");
            $leave_data = mysqli_fetch_array($leave_query);
            $available_leave = $leave_data['assigned_day'];

            // Insert new leave type assignment
            $insert_leave = mysqli_query($conn, "INSERT INTO employee_leave (emp_id, leave_type_id, available_day) VALUES ('$get_id', '$leave_id', '$available_leave')");
            if (!$insert_leave) {
                die(mysqli_error($conn));
            }
        }

        echo "<script>alert('Record Successfully Updated');</script>";
        echo "<script type='text/javascript'> document.location = 'staff.php'; </script>";
    } else {
        die(mysqli_error($conn));
    }
}
?>

<body>
<?php include('includes/navbar.php')?>
<?php include('includes/right_sidebar.php')?>
<?php include('includes/left_sidebar.php')?>

<!-- <div class="mobile-menu-overlay"></div> -->
<div class="main-container">
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="title">
                            <h4>Edit Staff</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="admin_dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Staff</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="pd-20 card-box mb-30">
                <div class="clearfix">
                    <div class="pull-left">
                        <h4 class="text-blue h4">Update Staff Information</h4>
                        <p class="mb-20"></p>
                    </div>
                </div>
                <div class="wizard-content">
                    <form method="post" action="">
                        <section>
                            <?php
                                $query = mysqli_query($conn, "SELECT * FROM tblemployees WHERE emp_id = '$get_id'") or die(mysqli_error($conn));
                                $row = mysqli_fetch_array($query);
                            ?>

                            <div class="row">
                                <div class="col-md-4 col-sm-12">
                                    <div class="form-group">
                                        <label>Full Name :</label>
                                        <input name="firstname" type="text" class="form-control wizard-required" required autocomplete="off" value="<?php echo $row['FirstName']; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <div class="form-group">
                                        <label>Email Address :</label>
                                        <input name="email" type="email" class="form-control wizard-required" required autocomplete="off" value="<?php echo $row['EmailId']; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <div class="form-group">
                                        <label>Staff ID :</label>
                                        <input name="staff_id" type="text" class="form-control wizard-required" required autocomplete="off" value="<?php echo $row['Staff_ID']; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-sm-12">
                                    <div class="form-group">
                                        <label>IC Number :</label>
                                        <input name="ic_number" type="text" class="form-control wizard-required" required autocomplete="off" value="<?php echo $row['IC_Number']; ?>">
                                    </div>
                                </div>                                
                                <div class="col-md-4 col-sm-12">
                                    <div class="form-group">
                                        <label>Date of Birth :</label>
                                        <input name="dob" type="text" class="form-control date-picker" required autocomplete="off" value="<?php echo $row['Dob']; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <div class="form-group">
                                        <label>Gender :</label>
                                        <select name="gender" class="custom-select form-control" required autocomplete="off">
                                            <option value="<?php echo $row['Gender']; ?>"><?php echo $row['Gender']; ?></option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 col-sm-12">
                                    <div class="form-group">
                                        <label>Phone Number :</label>
                                        <input name="phonenumber" type="text" class="form-control wizard-required" required autocomplete="off" value="<?php echo $row['Phonenumber']; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <div class="form-group">
                                        <label>Date Joined :</label>
                                        <input name="date_joined" type="date" class="form-control" required 
                                            oninput="generateServiceYear()" 
                                            value="<?php echo !empty($row['date_joined']) ? date('Y-m-d', strtotime($row['date_joined'])) : ''; ?>">
                                    </div>
                                </div>

                                <div class="col-md-4 col-sm-12">
                                    <div class="form-group">
                                        <label>Service Year :</label>
                                        <input name="service_year" type="text" class="form-control wizard-required" required autocomplete="off" value="<?php echo $row['Service_Year']; ?>">
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-4 col-sm-12">
                                    <div class="form-group">
                                        <label>User Role :</label>
                                        <select name="user_role" class="custom-select form-control" required autocomplete="off">
                                            <option value="<?php echo $row['role']; ?>"><?php echo $row['role']; ?></option>
                                            <option value="Admin">Admin</option>
                                            <option value="Manager">Manager</option>
                                            <option value="Staff">Staff</option>
                                            <option value="Director">Director</option>
                                        </select>
                                    </div>         
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <div class="form-group">
                                        <label>Position :</label>
                                        <input name="position_staff" type="text" class="form-control wizard-required" required autocomplete="off" value="<?php echo $row['Position_Staff']; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <div class="form-group">
                                        <label>Department :</label>
                                        <select name="department" class="custom-select form-control" required>
                                            <?php
                                                $query_dept = mysqli_query($conn, "SELECT * FROM tbldepartments");
                                                while ($dept_row = mysqli_fetch_array($query_dept)) {
                                                    echo "<option value='" . $dept_row['DepartmentShortName'] . "'>" . $dept_row['DepartmentName'] . "</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label>Car Plate :</label>
                                        <input name="car_plate" type="text" class="form-control wizard-required" required autocomplete="off" value="<?php echo $row['Car_Plate']; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label>Reporting To :</label>
                                        <input name="reporting_to" type="text" class="form-control wizard-required" required autocomplete="off" value="<?php echo $row['Reporting_To']; ?>">
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Address :</label>
                                        <input name="address" type="text" class="form-control wizard-required" required autocomplete="off" value="<?php echo $row['Address']; ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="clearfix">
                                <div class="pull-left">
                                    <h4 class="text-blue h4">Update Staff Information</h4>
                                    <p class="mb-20"></p>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-4 col-sm-12">
                                    <div class="form-group">
                                        <label>Emergency Contact Name :</label>
                                        <input name="emergency_name" type="text" class="form-control wizard-required" required autocomplete="off" value="<?php echo $row['Emergency_Name']; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <div class="form-group">
                                        <label>Emergency Contact Relation :</label>
                                        <input name="emergency_relation" type="text" class="form-control wizard-required" required autocomplete="off" value="<?php echo $row['Emergency_Relation']; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <div class="form-group">
                                        <label>Emergency Contact :</label>
                                        <input name="emergency_contact" type="text" class="form-control wizard-required" required autocomplete="off" value="<?php echo $row['Emergency_Contact']; ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Leave Type:</label>
                                        <div class="leave-type-container p-3 border rounded bg-light">
                                            <?php
                                            // Retrieve assigned leave types for the current employee
                                            $assigned_leave_query = mysqli_query($conn, "SELECT leave_type_id FROM employee_leave WHERE emp_id = '$get_id'");
                                            $assigned_leave_types = [];
                                            while ($assigned_leave = mysqli_fetch_array($assigned_leave_query)) {
                                                $assigned_leave_types[] = $assigned_leave['leave_type_id'];
                                            }

                                            // Fetch all available leave types
                                            $query_leave_types = mysqli_query($conn, "SELECT * FROM tblleavetype");
                                            while ($leave_type_row = mysqli_fetch_array($query_leave_types)) {
                                                $checked = in_array($leave_type_row['id'], $assigned_leave_types) ? "checked" : "";
                                                echo "
                                                    <div class='form-check'>
                                                        <input class='form-check-input' type='checkbox' name='leave_type_ids[]' value='" . $leave_type_row['id'] . "' id='leave_type_" . $leave_type_row['id'] . "' $checked>
                                                        <label class='form-check-label' for='leave_type_" . $leave_type_row['id'] . "'>
                                                            " . htmlspecialchars($leave_type_row['LeaveType']) . "
                                                        </label>
                                                    </div>";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 text-center">                        
                                <button type="submit" name="update_staff" class="btn btn-primary">Update Staff</button>
                            </div>
                        </section>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<?php include('includes/footer.php');?>
<?php include('scripts.php'); ?>
