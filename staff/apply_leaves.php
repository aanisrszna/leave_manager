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
// Include necessary files
require '../includes/config.php';
require '../includes/session.php';
include('../send_email.php'); // Include email function file

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply'])) {
    // Retrieve form data
    $empid = $session_id; // Ensure $session_id is properly initialized
    $leave_type = $_POST['leave_type'];
    $date_from = $_POST['date_from'];
    $date_to = $_POST['date_to'];
    $requested_days = $_POST['requested_days'];
    $outstanding_days = $_POST['outstanding_days'];
    $reason = $_POST['reason'];
    $is_half_day = isset($_POST['is_half_day']) ? $_POST['is_half_day'] : 0;
    $half_day_type = isset($_POST['half_day_type']) ? $_POST['half_day_type'] : null;
    $datePosting = date("Y-m-d");

    // Fetch employee details including department
    $emp_query = "SELECT FirstName, Department, EmailId FROM tblemployees WHERE emp_id = ?";
    $stmt = $conn->prepare($emp_query);
    $stmt->bind_param('s', $empid);
    $stmt->execute();
    $result = $stmt->get_result();
    $emp_data = $result->fetch_assoc();

    $emp_name = $emp_data['FirstName'];
    $emp_department = $emp_data['Department'];
    $emp_email = $emp_data['EmailId']; // Employee's email

    // Fetch HOD email based on department
    $hod_query = "SELECT EmailId FROM tblemployees WHERE role = 'Manager' AND Department = ?";
    $stmt = $conn->prepare($hod_query);
    $stmt->bind_param('s', $emp_department);
    $stmt->execute();
    $result = $stmt->get_result();
    $hod_data = $result->fetch_assoc();

    $hod_email = $hod_data['EmailId'];

    // File upload handling
    // File upload handling
    $proof = null;
    if (isset($_FILES['proof']) && $_FILES['proof']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../proof/'; // Full server path
        $relativeDir = 'proof/';             // For DB or web access

        // Ensure directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $originalName = basename($_FILES['proof']['name']);
        $fileType = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png'];

        if (in_array($fileType, $allowedTypes) && $_FILES['proof']['size'] <= 100 * 1024 * 1024) {
            // Create unique filename
            $newFileName = time() . '_' . preg_replace('/\s+/', '_', $originalName);
            $targetFilePath = $uploadDir . $newFileName;

            if (move_uploaded_file($_FILES['proof']['tmp_name'], $targetFilePath)) {
                $proof = $relativeDir . $newFileName; // Save relative path to DB
            } else {
                die('❌ Failed to upload proof file.');
            }
        } else {
            die('❌ Invalid file type or size exceeds 100MB.');
        }
    }


    // Check for overlapping leave dates
    $overlap_check_query = "
        SELECT * FROM tblleave 
        WHERE empid = ? 
        AND (? BETWEEN FromDate AND ToDate 
        OR ? BETWEEN FromDate AND ToDate 
        OR FromDate BETWEEN ? AND ? 
        OR ToDate BETWEEN ? AND ?)
    ";
    $stmt = $conn->prepare($overlap_check_query);
    $stmt->bind_param('sssssss', $empid, $date_from, $date_to, $date_from, $date_to, $date_from, $date_to);
    $stmt->execute();
    $overlap_result = $stmt->get_result();

    if ($overlap_result->num_rows > 0) {
        $error_message = 'You already applied for this date. Delete it first before submitting a new application.';
    } else {
        // Insert leave application into the database
    $insert_query = "
        INSERT INTO tblleave (empid, LeaveType, FromDate, ToDate, RequestedDays, DaysOutstand, Reason, PostingDate, Proof, IsHalfDay, HalfDayType)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param('sssssssssis', $empid, $leave_type, $date_from, $date_to, $requested_days, $outstanding_days, $reason, $datePosting, $proof, $is_half_day, $half_day_type);


        if ($stmt->execute()) {
            // Email notification to HOD
            if (!empty($hod_email)) {
                $redirectLink = "http://192.168.1.15/leave_manager/index.php";

                $subject_hod = "New Leave Application from $emp_name";
                $message_hod = "
                    <p>Dear Manager,</p>
                    <p>$emp_name has applied for leave.</p>
                    <table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; width: 100%;'>
                        <tr><th align='left'>Leave Type</th><td>$leave_type</td></tr>
                        <tr><th align='left'>From</th><td>$date_from</td></tr>
                        <tr><th align='left'>To</th><td>$date_to</td></tr>
                        <tr><th align='left'>Requested Days</th><td>$requested_days</td></tr>
                        <tr><th align='left'>Reason</th><td>$reason</td></tr>
                    </table>
                    <p>Please review the application through the system.</p>
                    <p>Best regards,<br><strong>e-Leave Manager System</strong></p>
                    <p><a href='$redirectLink'>E-Leave Manager</a></p>
                ";
                
                send_email($hod_email, $subject_hod, $message_hod);
            }

            // Email confirmation to the employee
            // if (!empty($emp_email)) {
            //     $subject_emp = "Leave Application Submitted Successfully";
            //     $message_emp = "
            //         <p>Dear $emp_name,</p>
            //         <p>Your leave application has been successfully submitted. Below are the details:</p>
            //         <table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; width: 100%;'>
            //             <tr><th align='left'>Leave Type</th><td>$leave_type</td></tr>
            //             <tr><th align='left'>From</th><td>$date_from</td></tr>
            //             <tr><th align='left'>To</th><td>$date_to</td></tr>
            //             <tr><th align='left'>Requested Days</th><td>$requested_days</td></tr>
            //             <tr><th align='left'>Reason</th><td>$reason</td></tr>
            //         </table>
            //         <p>You will receive an update once your leave request has been processed.</p>
            //         <p>Best regards,<br><strong>e-Leave Manager System</strong></p>
            //     ";
            //     send_email($emp_email, $subject_emp, $message_emp);
            // }

            // Redirect to leave history page
            header('Location: leave_history.php');
            exit();
        } else {
            $error_message = 'Error applying leave: ' . $stmt->error;
        }
    }
}

if (isset($error_message)) {
    echo "<script>alert('$error_message'); console.error('$error_message');</script>";
}
?>


<body>
    <?php include('includes/navbar.php')?>
    <?php include('includes/right_sidebar.php')?>
    <?php include('includes/left_sidebar.php')?>

    <div class="mobile-menu-overlay"></div>

    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="title">
                                <h4>Leave Type List</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="admin_dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Apply Leave</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <div style="margin-left: 30px; margin-right: 30px;" class="pd-20 card-box mb-30">
                    <div class="clearfix">
                        <div class="pull-left">
                            <h4 class="text-blue h4">Staff Form</h4>
                            <p class="mb-20"></p>
                        </div>
                    </div>
                    <div class="wizard-content">
                        <form method="post" action="" enctype="multipart/form-data">
                            <section>
                                <?php if ($role_id = 'Staff'): ?>
                                <?php $query= mysqli_query($conn,"select * from tblemployees where emp_id = '$session_id'")or die(mysqli_error());
                                    $row = mysqli_fetch_array($query);
                                ?>

                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <label>Full Name</label>
                                            <input name="firstname" type="text" class="form-control wizard-required" required="true" readonly autocomplete="off" value="<?php echo $row['FirstName']; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label>Position</label>
                                            <input name="postion" type="text" class="form-control" required="true" autocomplete="off" readonly value="<?php echo $row['Position_Staff']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label>Staff ID Number</label>
                                            <input name="staff_id" type="text" class="form-control" required="true" autocomplete="off" readonly value="<?php echo $row['Staff_ID']; ?>">
                                        </div>
                                    </div>
                                    <?php endif ?>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <label>Leave Type</label>
                                            <select name="leave_type" id="leave_type" class="custom-select form-control" required="true" onchange="toggleProofField()">
                                                <option value="">Select leave type...</option>
                                                <?php
                                                    $query = mysqli_query($conn, "
                                                        SELECT 
                                                            el.emp_id,
                                                            lt.LeaveType,
                                                            lt.Description,
                                                            el.available_day,
                                                            lt.NeedProof
                                                        FROM 
                                                            employee_leave el
                                                        INNER JOIN 
                                                            tblleavetype lt 
                                                            ON el.leave_type_id = lt.id
                                                        WHERE 
                                                            el.emp_id = '$session_id'
                                                            AND TRIM(lt.LeaveType) NOT IN ('Emergency Leave', 'Unpaid Leave')
                                                    ") or die(mysqli_error($conn));

                                                    while ($row = mysqli_fetch_assoc($query)) {
                                                        if ((int)$row['available_day'] <= 0) {
                                                            continue; // Skip leave types with 0 or negative available days
                                                        }
                                                        echo '<option value="' . $row['LeaveType'] . '" 
                                                                data-id="' . $row['id'] . '" 
                                                                data-available-days="' . $row['available_day'] . '" 
                                                                data-need-proof="' . $row['NeedProof'] . '">'
                                                            . htmlentities($row['LeaveType']) . ' - Available: ' . $row['available_day'] . ' days</option>';
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label>Start Leave Date</label>
                                            <input id="date_form" name="date_from" type="date" class="form-control" required="true" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label>End Leave Date</label>
                                            <input id="date_to" name="date_to" type="date" class="form-control" required="true" autocomplete="off">
                                        </div>
                                    </div>
                                </div>

                                <!-- Half-Day Options -->
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label>Half-Day</label>
                                            <select id="is_half_day" name="is_half_day" class="custom-select form-control" onchange="toggleHalfDayType()">
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12" id="half_day_type_container" style="display: none;">
                                        <div class="form-group">
                                            <label>Half-Day Type</label>
                                            <select id="half_day_type" name="half_day_type" class="custom-select form-control" >
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
                                                <span class="j-hint">Only: pdf, jpg, jpeg, png, less than 100MB</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Other fields -->
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <div class="form-group d-flex justify-content-center" style="margin-top: 50px;">
                                            <div class="modal-footer">
                                                <button class="btn btn-primary" name="apply" id="apply" type="submit" disabled>Apply Leave</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </form>


                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php include('apply_leave_scripts.php'); ?>

  
</body>
</html>


