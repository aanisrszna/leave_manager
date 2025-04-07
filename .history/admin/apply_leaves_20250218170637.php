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

<?php include('../includes/config.php'); ?>
<?php include('../includes/session.php');?>

<?php
if (isset($_POST['apply'])) {
    $empid = $session_id; // Ensure this is properly set from the session
    $leave_type = $_POST['leave_type'];
    $date_from = $_POST['date_from'];
    $date_to = $_POST['date_to'];
    $requested_days = $_POST['requested_days'];
    $outstanding_days = $_POST['outstanding_days'];
    $datePosting = date("Y-m-d");
    $reason = $_POST['reason'];
    
    // File upload handling
    $proof = null;
    if (!empty($_FILES['proof']['name']) && $_FILES['proof']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "../proof/";
        $fileName = basename($_FILES['proof']['name']);
        $targetFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png'];

        if (in_array($fileType, $allowedTypes) && $_FILES['proof']['size'] <= 2 * 1024 * 1024) {
            if (move_uploaded_file($_FILES['proof']['tmp_name'], $targetFilePath)) {
                $proof = $fileName;
            } else {
                die('Failed to upload proof file.');
            }
        } else {
            die('Invalid file type or file size exceeds 2MB.');
        }
    }

    // Check for overlapping leave dates
    $overlap_query = "
        SELECT * FROM tblleave WHERE empid = ?
        AND (? BETWEEN FromDate AND ToDate 
        OR ? BETWEEN FromDate AND ToDate 
        OR FromDate BETWEEN ? AND ?
        OR ToDate BETWEEN ? AND ?)
    ";
    $stmt = $conn->prepare($overlap_query);
    $stmt->bind_param('sssssss', $empid, $date_from, $date_to, $date_from, $date_to, $date_from, $date_to);
    $stmt->execute();
    $overlap_result = $stmt->get_result();

    if ($overlap_result->num_rows > 0) {
        die('You already applied for this date. Delete it first before submitting a new application.');
    }

    // Fetch employee role
    $role_query = "SELECT role, FirstName, EmailId FROM tblemployees WHERE emp_id = ?";
    $stmt = $conn->prepare($role_query);
    $stmt->bind_param('s', $empid);
    $stmt->execute();
    $role_result = $stmt->get_result();
    $employee = $role_result->fetch_assoc();
    
    if (!$employee) {
        die('Error fetching employee details.');
    }
    
    $role = $employee['role'];
    $employee_email = $employee['EmailId'];
    $employee_name = $employee['FirstName'];

    // Set HOD remarks based on role
    $hod_remarks = ($role == 'Manager') ? 3 : 'Pending';
    
    // Insert leave application into the database
    $insert_query = "
        INSERT INTO tblleave (empid, LeaveType, FromDate, ToDate, RequestedDays, DaysOutstand, PostingDate, HodRemarks, Proof, reason)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param('ssssssssss', $empid, $leave_type, $date_from, $date_to, $requested_days, $outstanding_days, $datePosting, $hod_remarks, $proof, $reason);
    
    if ($stmt->execute()) {
        // Send email notification
        if (sendNotificationStaff($employee_email, $employee_name, $leave_type, $date_from, $date_to)) {
            echo "<script>alert('Leave Applied Successfully! Email sent.'); window.location.href='leave_history.php';</script>";
        } else {
            echo "<script>alert('Leave Applied Successfully! But email notification failed.'); window.location.href='leave_history.php';</script>";
        }
    } else {
        die('Error applying leave: ' . $stmt->error);
    }
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
                                    <li class="breadcrumb-item active" aria-current="page">Signature Module</li>
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
                        <form method="post" action="">
                            <section>

                                <?php if ($role_id = 'Staff'): ?>
                                <?php $query= mysqli_query($conn,"select * from tblemployees where emp_id = '$session_id'")or die(mysqli_error());
                                    $row = mysqli_fetch_array($query);
                                ?>
                        
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <label >Full Name Name </label>
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
                                            <label>Staff ID Number </label>
                                            <input name="staff_id" type="text" class="form-control" required="true" autocomplete="off" readonly value="<?php echo $row['Staff_ID']; ?>">
                                        </div>
                                    </div>
                                    <?php endif ?>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <label>Leave Type :</label>
                                            <select name="leave_type" id="leave_type" class="custom-select form-control" required="true" onchange="updateAvailableDays()">
                                                <option value="">Select leave type...</option>
                                                <?php
                                                $query = mysqli_query($conn, "
                                                    SELECT 
                                                        el.emp_id,
                                                        lt.LeaveType,
                                                        lt.Description,
                                                        el.available_day
                                                    FROM 
                                                        employee_leave el
                                                    INNER JOIN 
                                                        tblleavetype lt 
                                                    ON 
                                                        el.leave_type_id = lt.id
                                                    WHERE 
                                                        el.emp_id = '$session_id'
                                                ") or die(mysqli_error($conn));

                                                while ($row = mysqli_fetch_assoc($query)) {
                                                    echo '<option value="' . $row['LeaveType'] . '" data-available-days="' . $row['available_day'] . '">'
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
                                    <div class="col-md-12 text-center">
                                        <button class="btn btn-primary" name="apply" type="submit">Apply Leave</button>
                                    </div>
                                </div>
                                

                            </section>
                        </form>
                    </div>
                </div>
            </div>
        </div>
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
            const minBusinessDate = getMinBusinessDate(currentDate, 5);
            if (startDate < minBusinessDate) {
                alert("Leave application must be submitted at least 5 business days before the FromDate!");
                resetInputs();
                return;
            }

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
        }

        document.getElementById('date_form').addEventListener('input', calc);
        document.getElementById('date_to').addEventListener('input', calc);
        document.getElementById('is_half_day').addEventListener('change', calc);
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




    <script src="../vendors/scripts/core.js"></script>
    <script src="../vendors/scripts/script.min.js"></script>
    <script src="../vendors/scripts/process.js"></script>
    <script src="../vendors/scripts/layout-settings.js"></script>
  
</body>
</html>


