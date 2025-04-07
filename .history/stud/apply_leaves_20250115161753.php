<?php
include('../includes/config.php');
include('../includes/session.php');

if (isset($_POST['apply'])) {
    $empid = $session_id;
    $leave_type = $_POST['leave_type'];
    $date_from = $_POST['date_from'];
    $date_to = $_POST['date_to'];
    $requested_days = $_POST['requested_days'];
    $outstanding_days = $_POST['outstanding_days'];
    $datePosting = date("Y-m-d");
    $proof_path = ''; // Initialize the variable for proof file

    // Handle file upload if proof is required
    if ($_FILES['proof']['error'] == 0) {
        $proof_name = $_FILES['proof']['name'];
        $proof_tmp_name = $_FILES['proof']['tmp_name'];
        $proof_size = $_FILES['proof']['size'];
        $proof_ext = pathinfo($proof_name, PATHINFO_EXTENSION);

        // Check file size (max 2MB)
        if ($proof_size <= 2 * 1024 * 1024) {
            // Allow only specific file types
            if (in_array($proof_ext, ['jpg', 'jpeg', 'png', 'pdf'])) {
                $proof_new_name = uniqid() . '.' . $proof_ext;
                $proof_upload_path = '../uploads/' . $proof_new_name;

                if (move_uploaded_file($proof_tmp_name, $proof_upload_path)) {
                    $proof_path = $proof_upload_path;
                } else {
                    echo "<script>alert('Error uploading proof file.');</script>";
                    exit();
                }
            } else {
                echo "<script>alert('Invalid file type. Only jpg, jpeg, png, pdf are allowed.');</script>";
                exit();
            }
        } else {
            echo "<script>alert('File size exceeds 2MB.');</script>";
            exit();
        }
    } else {
        // If proof is required but no file is uploaded, show an error
        $leave_query = mysqli_query($conn, "SELECT NeedProof FROM tblleavetype WHERE LeaveType = '$leave_type'");
        $leave_row = mysqli_fetch_assoc($leave_query);
        if ($leave_row['NeedProof'] == 'Yes') {
            echo "<script>alert('Proof file is required for this leave type.');</script>";
            exit();
        }
    }

    // Insert leave application into the database
    $query = "INSERT INTO tblleave (empid, LeaveType, FromDate, ToDate, RequestedDays, DaysOutstand, PostingDate, ProofFile)
        VALUES ('$empid', '$leave_type', '$date_from', '$date_to', '$requested_days', '$outstanding_days', '$datePosting', '$proof_path')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Leave Applied Successfully!');</script>";
        header('Location: leave_history.php'); // Redirect to leave history page
        exit();
    } else {
        echo "<script>alert('Error applying leave: " . mysqli_error($conn) . "');</script>";
    }
}
?>

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
    <link href="../assets/css/jquery.signature.css" rel="stylesheet">
    <script src="../src/js/jquery.signature.js"></script>
  
    <style>
        .kbw-signature { width: 100%; height: 100px;}
        #sig canvas{
            width: 100% !important;
            height: auto;
        }
    </style>
  
</head>

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
                        <form method="post" action="" enctype="multipart/form-data">
                            <section>
                                <?php if ($role_id = 'Staff'): ?>
                                <?php $query = mysqli_query($conn, "SELECT * FROM tblemployees WHERE emp_id = '$session_id'") or die(mysqli_error());
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
                                            <label>Leave Type :</label>
                                            <select name="leave_type" id="leave_type" class="custom-select form-control" required="true" onchange="updateProofRequirement()">
                                                <option value="">Select leave type...</option>
                                                <?php
                                                $query = mysqli_query($conn, "
                                                    SELECT 
                                                        lt.LeaveType, 
                                                        lt.Description, 
                                                        lt.NeedProof, 
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
                                                    echo '<option value="' . $row['LeaveType'] . '" data-need-proof="' . $row['NeedProof'] . '" data-available-days="' . $row['available_day'] . '">'
                                                        . htmlentities($row['LeaveType']) . ' (' . $row['Description'] . ') - Available: ' . $row['available_day'] . ' days</option>';
                                                }
                                                ?>
                                            </select>

                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="proof-container" style="display: none;">
                                    <div class="col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <label>Proof of Leave (Upload file):</label>
                                            <input type="file" name="proof" id="proof" accept=".jpg,.jpeg,.png,.pdf" class="form-control">
                                            <small class="text-muted">Only .jpg, .jpeg, .png, .pdf files allowed. Max size 2MB.</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <label>From Date:</label>
                                            <input name="date_from" type="text" class="form-control" required="true" id="date_from">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <label>To Date:</label>
                                            <input name="date_to" type="text" class="form-control" required="true" id="date_to">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <label>Requested Days:</label>
                                            <input name="requested_days" type="text" class="form-control" id="requested_days" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <label>Outstanding Days:</label>
                                            <input name="outstanding_days" type="text" class="form-control" id="outstanding_days" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <button type="submit" name="apply" class="btn btn-success btn-block">Apply for Leave</button>
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
  
    <script>
        function updateProofRequirement() {
            const leaveTypeSelect = document.getElementById('leave_type');
            const proofContainer = document.getElementById('proof-container');
            const selectedOption = leaveTypeSelect.options[leaveTypeSelect.selectedIndex];
            const needProof = selectedOption.getAttribute('data-need-proof');

            if (needProof === 'Yes') {
                proofContainer.style.display = 'block';
            } else {
                proofContainer.style.display = 'none';
            }
        }

        $(function () {
            $("#date_from, #date_to").datepicker({
                dateFormat: "yy-mm-dd"
            });

            // Additional date validation and leave calculation logic here
        });
    </script>
<script>
    function calc() {
        const date_from = document.getElementById('date_form');
        const date_to = document.getElementById('date_to');
        const isHalfDay = document.getElementById('is_half_day').value;

        const startDate = new Date(date_from.value);
        const endDate = new Date(date_to.value);

        if (endDate < startDate) {
            alert("End date cannot be earlier than start date!");
            resetInputs();
            return;
        }

        let requestedDays = getBusinessDateCount(startDate, endDate);

        // Adjust for half-day leave if applicable
        if (isHalfDay === '1') {
            if (requestedDays > 1) {
                requestedDays -= 0.5; // Add half-day only if the total duration spans 1 full day or more
            } else {
                requestedDays = 0.5; // For single-day half-day leave
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

    document.getElementById('date_form').addEventListener('input', calc);
    document.getElementById('date_to').addEventListener('input', calc);
    document.getElementById('is_half_day').addEventListener('change', calc);
</script>






    <script src="../vendors/scripts/core.js"></script>
    <script src="../vendors/scripts/script.min.js"></script>
    <script src="../vendors/scripts/process.js"></script>
    <script src="../vendors/scripts/layout-settings.js"></script>

</body>
</html>
