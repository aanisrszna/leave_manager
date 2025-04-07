<html>
<head>
    <!-- Basic Page Info -->
    <meta charset="utf-8">
    <title>E-Leave System</title>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.css">
  
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> 
    <link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/south-street/jquery-ui.css" rel="stylesheet"> 
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

    <!-- Site favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="../vendors/images/riverraven.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../vendors/images/riverraven.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../vendors/images/riverraven.png">

    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- CSS -->

    <!-- jQuery UI Signature core CSS -->
    <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/south-street/jquery-ui.css" rel="stylesheet">
    <link href="../assets/css/jquery.signature.css" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="../vendors/styles/core.css">
    <link rel="stylesheet" type="text/css" href="../vendors/styles/icon-font.min.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/jquery-steps/jquery.steps.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/datatables/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/datatables/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="../vendors/styles/style.css">

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-119386393-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-119386393-1');
    </script>

    <link href="../src/css/jquery.signature.css" rel="stylesheet">
    <script src="../src/js/jquery.signature.js"></script>
  
    <style>
        .kbw-signature { width: 100%; height: 100px;}
        #sig canvas{
            width: 100% !important;
            height: auto;
        }
    </style>
  
</head>

<?php 
include('../includes/config.php');
include('../includes/session.php');
require '../send_email.php'; // Include email function


$staffDetails = null;
$leaveTypes = [];

// Fetch staff details and leave types if Staff_ID is provided
if(isset($_POST['fetch']) && !empty($_POST['staff_id'])) {
    $staff_id = $_POST['staff_id'];

    // Fetch staff details based on Staff_ID
    $query = "SELECT * FROM tblemployees WHERE Staff_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $staff_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $staffDetails = $result->fetch_assoc();

    // Fetch leave types based on employee_leave
    if ($staffDetails) {
        $emp_id = $staffDetails['emp_id'];
        $leaveQuery = "
            SELECT lt.id, lt.LeaveType, el.available_day 
            FROM tblleavetype lt 
            JOIN employee_leave el ON lt.id = el.leave_type_id 
            WHERE el.emp_id = ?";
        $leaveStmt = $conn->prepare($leaveQuery);
        $leaveStmt->bind_param("i", $emp_id);
        $leaveStmt->execute();
        $leaveResult = $leaveStmt->get_result();
        $leaveTypes = $leaveResult->fetch_all(MYSQLI_ASSOC);
    }
}

// Insert leave record
if (isset($_POST['submit'])) {
    $staff_id = $_POST['staff_id'];
    $leave_type_id = $_POST['leave_type'];
    $date_from = $_POST['date_from'];
    $date_to = $_POST['date_to'];
    $requested_days = $_POST['requested_days'];
    $outstanding_days = $_POST['outstanding_days'];
    $datePosting = date("Y-m-d");
    

    // Ensure 'reason' is set to avoid NULL values
    $reason = isset($_POST['reason']) && trim($_POST['reason']) !== '' ? $_POST['reason'] : 'No reason provided';

    // File upload handling
    $proof = null;
    if (isset($_FILES['proof']) && $_FILES['proof']['error'] == UPLOAD_ERR_OK) {
        $targetDir = "../proof/";
        $fileName = basename($_FILES['proof']['name']);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // Validate file type and size
        $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png'];
        if (in_array(strtolower($fileType), $allowedTypes) && $_FILES['proof']['size'] <= 2 * 1024 * 1024) {
            if (move_uploaded_file($_FILES['proof']['tmp_name'], $targetFilePath)) {
                $proof = $fileName;
            } else {
                echo "<script>alert('Failed to upload proof file.');</script>";
            }
        } else {
            echo "<script>alert('Invalid file type or file size exceeds 2MB.');</script>";
        }
    }

    // Retrieve emp_id and role based on staff_id
    $empQuery = "SELECT emp_id, role, Department, FirstName, EmailId FROM tblemployees WHERE Staff_ID = ?";
    $empStmt = $conn->prepare($empQuery);
    $empStmt->bind_param("s", $staff_id);
    $empStmt->execute();
    $empResult = $empStmt->get_result();

    if ($empResult->num_rows > 0) {
        $empRow = $empResult->fetch_assoc();
        $empid = $empRow['emp_id'];
        $role = $empRow['role'];
        $department = $empRow['Department'];
        $empName = $empRow['FirstName'];
        $empEmail = $empRow['EmailId'];

        // Get manager email
        $hodQuery = "SELECT EmailId FROM tblemployees WHERE role = 'Manager' AND Department = ?";
        $hodStmt = $conn->prepare($hodQuery);
        $hodStmt->bind_param("s", $department);
        $hodStmt->execute();
        $hodResult = $hodStmt->get_result();
        $hodRow = $hodResult->fetch_assoc();
        $hodEmail = $hodRow['EmailId'];

        // Determine HodRemarks based on role
        $hod_remarks = ($role == 'Manager') ? 3 : 'Pending';

        // Retrieve leave type name
        $leaveTypeQuery = "SELECT LeaveType FROM tblleavetype WHERE id = ?";
        $leaveTypeStmt = $conn->prepare($leaveTypeQuery);
        $leaveTypeStmt->bind_param("i", $leave_type_id);
        $leaveTypeStmt->execute();
        $leaveTypeResult = $leaveTypeStmt->get_result();
        $leaveTypeRow = $leaveTypeResult->fetch_assoc();
        $leave_type = $leaveTypeRow['LeaveType'];


        if ($leaveTypeResult->num_rows > 0) {
            $leaveTypeRow = $leaveTypeResult->fetch_assoc();
            $leave_type = $leaveTypeRow['LeaveType'];

            // Insert the leave record into tblleave
            $insertQuery = "INSERT INTO tblleave (empid, LeaveType, FromDate, ToDate, RequestedDays, DaysOutstand, reason, PostingDate, Proof, HodRemarks) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $insertStmt = $conn->prepare($insertQuery);

            if ($insertStmt) {
                $insertStmt->bind_param('ssssssssss', $empid, $leave_type, $date_from, $date_to, $requested_days, $outstanding_days, $reason, $datePosting, $proof, $hod_remarks);

                // Execute the statement
                if ($insertStmt->execute()) {
                    echo "<script>alert('Leave record added successfully.');</script>";
                
                    // Fetch staff email
                    $emailQuery = "SELECT EmailId FROM tblemployees WHERE emp_id = ?";
                    $emailStmt = $conn->prepare($emailQuery);
                    $emailStmt->bind_param("i", $empid);
                    $emailStmt->execute();
                    $emailResult = $emailStmt->get_result();
                    
                    if ($emailResult->num_rows > 0) {
                        $emailRow = $emailResult->fetch_assoc();
                        $staffEmail = $emailRow['EmailId'];
                
                        // Email details
                        $subject = "Leave Application Submitted";
                        $message = "
                            <p>Dear Employee,</p>
                            <p>Your leave application has been submitted successfully by admin.</p>
                            <table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; width: 100%;'>
                                <tr><th align='left'>Leave Type</th><td>$leave_type</td></tr>
                                <tr><th align='left'>From</th><td>$date_from</td></tr>
                                <tr><th align='left'>To</th><td>$date_to</td></tr>
                                <tr><th align='left'>Requested Days</th><td>$requested_days</td></tr>
                                <tr><th align='left'>Reason</th><td>$reason</td></tr>
                            </table>
                            <p>Please review the application.</p>
                            <p>Best regards,<br><strong>e-Leave Manager System</strong></p>
                        ";
                
                        // Send email
                        // $emailStatus = send_email($staffEmail, $subject, $message);
                        echo "<script>alert('$emailStatus');</script>";
                    }
                    // Send email to manager
                    if (!empty($hodEmail)) {
                        $subject = "New Leave Application from $empName";
                        $message = "
                            <p>Dear Manager,</p>
                            <p>$empName has submitted a leave application with the following details:</p>
                            <ul>
                                <li><strong>Leave Type:</strong> $leave_type</li>
                                <li><strong>From:</strong> $date_from</li>
                                <li><strong>To:</strong> $date_to</li>
                                <li><strong>Requested Days:</strong> $requested_days</li>
                                <li><strong>Reason:</strong> $reason</li>
                            </ul>
                            <p>Please review the application in the system.</p>
                        ";
                        send_email($hodEmail, $subject, $message);
                    }

                } else {
                    echo "<script>alert('Failed to add leave record: " . $conn->error . "');</script>";
                }
                
            } else {
                die("Prepare failed: " . $conn->error);
            }
        } else {
            echo "<script>alert('Invalid Leave Type ID.');</script>";
        }
    } else {
        echo "<script>alert('Invalid Staff ID.');</script>";
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
                                <h3>Admin Leave Application</h3>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="admin_dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Leave Application</li>
                                </ol>
                            </nav>

                        </div>
                    </div>
                </div>
                <div style="margin-left: 30px; margin-right: 30px;" class="pd-20 card-box mb-30">
                    <div class="clearfix">
                        <div class="pull-left">
                            <h4 class="text-blue h4">Admin Form to Apply Leave</h4>
                        </div>
                    </div>
                    <div class="wizard-content">
                        <form method="post" action="">
                            <div class="form-group">
                                <label for="staff_id">Staff ID:</label>
                                <input type="text" id="staff_id" name="staff_id" class="form-control mb-3" value="<?php echo $_POST['staff_id'] ?? ''; ?>" placeholder="Enter Staff ID" required>
                            </div>
                            <div class="d-flex justify-content-start mb-4">
                                <button type="submit" name="fetch" class="btn btn-primary">Fetch Details</button>
                            </div>

                            <?php if ($staffDetails): ?>
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label>Full Name</label>
                                        <input name="firstname" type="text" class="form-control" required="true" value="<?php echo $staffDetails['FirstName'] . ' ' . $staffDetails['LastName']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label>Position</label>
                                        <input name="position" type="text" class="form-control" required="true" value="<?php echo $staffDetails['Position_Staff']; ?>" readonly>
                                    </div>
                                </div>
                            </div>
                                <!-- <div class="mt-3">
                                    <p><strong>Department:</strong> <?php echo $staffDetails['Department']; ?></p>
                                    <p><strong>Email:</strong> <?php echo $staffDetails['EmailId']; ?></p>
                                </div> -->
                            <div class="row">
                                <div class="col-md-12 col-sm-12">

                                    <div class="form-group">
                                        <label for="leave_type">Leave Type:</label>
                                        <select id="leave_type" name="leave_type" class="form-control" required="true" onchange="toggleProofField()">
                                            <option value="">Select Leave Type...</option>
                                            <?php
                                            // Assuming staff_id is retrieved from a form or session
                                            $staff_id = $_POST['staff_id'] ?? ''; // Replace as necessary

                                            // Query to retrieve leave types based on staff_id
                                            $query = mysqli_query($conn, "
                                                SELECT 
                                                    el.emp_id,
                                                    lt.id AS leave_type_id,
                                                    lt.LeaveType,
                                                    lt.Description,
                                                    el.available_day,
                                                    lt.NeedProof
                                                FROM 
                                                    employee_leave el
                                                INNER JOIN 
                                                    tblleavetype lt 
                                                ON 
                                                    el.leave_type_id = lt.id
                                                INNER JOIN 
                                                    tblemployees te 
                                                ON 
                                                    el.emp_id = te.emp_id
                                                WHERE 
                                                    te.Staff_ID = '$staff_id'
                                            ") or die(mysqli_error($conn));

                                            while ($row = mysqli_fetch_assoc($query)) {
                                                echo '<option value="' . $row['leave_type_id'] . '" 
                                                        data-available-days="' . $row['available_day'] . '" 
                                                        data-need-proof="' . $row['NeedProof'] . '">'
                                                    . htmlentities($row['LeaveType']) . ' (' . $row['Description'] . ') - Available: ' . $row['available_day'] . ' days</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label>Start Leave Date :</label>
                                        <input id="date_form" name="date_from" type="date" class="form-control" required="true" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label>End Leave Date :</label>
                                        <input id="date_to" name="date_to" type="date" class="form-control" required="true" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                                <!-- Half-Day Options -->
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label>Is Half-Day Leave?</label>
                                        <select id="is_half_day" name="is_half_day" class="custom-select form-control" onchange="toggleHalfDayType()">
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12" id="half_day_type_container" style="display: none;">
                                    <div class="form-group">
                                        <label>Half-Day Type</label>
                                        <select id="half_day_type" name="half_day_type" class="custom-select form-control">
                                            <option value="AM">Morning (AM)</option>
                                            <option value="PM">Afternoon (PM)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>                        
                            
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label>Leave Days Requested</label>
                                        <input id="requested_days" name="requested_days" type="text" class="form-control" required="true" autocomplete="off" readonly value="">
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label>Number of Days Outstanding</label>
                                        <input id="outstanding_days" name="outstanding_days" type="text" class="form-control" required="true" autocomplete="off" readonly value="">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Reason</label>
                                        <input name="reason" type="text" class="form-control" required>
                                    </div>
                                </div>
                            </div>    

                            <div class="row" id="proof_container" style="display: none;">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Proof:</label>
                                        <div class="j-input j-append-small-btn">
                                            <div class="j-file-button">
                                                Browse
                                                <input type="file" name="proof" id="proof" accept=".pdf, .jpg, .jpeg, .png">
                                            </div>
                                            <span class="j-hint">Only: pdf, jpg, jpeg, png, less than 2MB</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group d-flex justify-content-center" style="margin-top: 50px;">
                                        <button type="submit" name="submit" class="btn btn-primary">Submit Leave</button>
                                    </div>
                                </div>
                            </div>
                                    
                                    
                            <?php elseif (isset($_POST['fetch'])): ?>
                                <p class="text-danger mt-3">Invalid Staff ID or no leave types found for this staff.</p>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <p class="text-muted mt-3">Enter the Staff ID to fetch details and assign leave.</p>
    </div>
    <script>
        function calc() {
            const date_from = document.getElementById('date_form');
            const date_to = document.getElementById('date_to');
            const isHalfDay = document.getElementById('is_half_day').value;


            const startDate = new Date(date_from.value);
            const endDate = new Date(date_to.value);
            const currentDate = new Date();

            // Check if FromDate is at least 5 business days from today

            if (endDate < startDate) {
                alert("End date cannot be earlier than start date!");
                resetInputs();
                return;
            }

            let requestedDays = getBusinessDateCount(startDate, endDate);
            // Adjust for half-day leave if applicable
            if (isHalfDay === '1') {
                if (requestedDays === 1) {
                    requestedDays -= 0.5; // Add half-day only if the total duration spans 1 full day or more
                } else {
                    requestedDays -= 0.5; // For single-day half-day leave
                }
            }

            document.getElementById('requested_days').value = requestedDays;
            updateAvailableDays();
        }

        function toggleHalfDayType() {
            const isHalfDay = document.getElementById('is_half_day').value;
            const halfDayTypeContainer = document.getElementById('half_day_type_container');
            halfDayTypeContainer.style.display = isHalfDay === '1' ? 'block' : 'none';
        }

        function updateAvailableDays() {
            const leaveTypeSelect = document.getElementById('leave_type');
            const selectedOption = leaveTypeSelect.options[leaveTypeSelect.selectedIndex];
            const availableDays = parseFloat(selectedOption.getAttribute('data-available-days')) || 0;
            const requestedDays = parseFloat(document.getElementById('requested_days').value) || 0;

            const outstandingDays = availableDays - requestedDays;
            document.getElementById('outstanding_days').value = outstandingDays >= 0 ? outstandingDays : 0;

            if (outstandingDays < 0) {
                alert("Requested days exceed available leave days!");
            }
        }

        function getBusinessDateCount(startDate, endDate) {
            let count = 0;
            const currentDate = new Date(startDate);

            while (currentDate <= endDate) {
                const day = currentDate.getDay();
                if (day !== 0 && day !== 6) {
                    count++;
                }
                currentDate.setDate(currentDate.getDate() + 1);
            }

            return count;
        }

        function getMinBusinessDate(currentDate, businessDays) {
            let count = 0;
            const minBusinessDate = new Date(currentDate);

            while (count < businessDays) {
                minBusinessDate.setDate(minBusinessDate.getDate() + 1);
                const day = minBusinessDate.getDay();
                if (day !== 0 && day !== 6) {
                    count++;
                }
            }

            return minBusinessDate;
        }

        function resetInputs() {
            document.getElementById('requested_days').value = '';
            document.getElementById('outstanding_days').value = '';
            document.getElementById('is_half_day').addEventListener('change', calc);

        }

        document.getElementById('date_form').addEventListener('input', calc);
        document.getElementById('date_to').addEventListener('input', calc);
    </script>
    <script>
        function toggleProofField() {
            const leaveTypeSelect = document.getElementById('leave_type');
            const proofContainer = document.getElementById('proof_container');
            const selectedOption = leaveTypeSelect.options[leaveTypeSelect.selectedIndex];
            const needProof = selectedOption.getAttribute('data-need-proof');

            if (needProof === 'Yes') {
                proofContainer.style.display = 'block';
            } else {
                proofContainer.style.display = 'none';
            }
        }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let dateInputs = document.querySelectorAll("#date_form, #date_to");

            dateInputs.forEach(function (input) {
                input.addEventListener("input", function () {
                    let selectedDate = new Date(this.value);
                    let day = selectedDate.getDay();

                    if (day === 0 || day === 6) { // 0 = Sunday, 6 = Saturday
                        alert("You cannot select weekends (Saturday & Sunday). Please choose a weekday.");
                        this.value = ""; // Reset input field
                    }
                });
            });
        });
    </script>
    <script src="../vendors/scripts/core.js"></script>
    <script src="../vendors/scripts/script.min.js"></script>
    <script src="../vendors/scripts/process.js"></script>
    <script src="../vendors/scripts/layout-settings.js"></script>
</body>
</html>
