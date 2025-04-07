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

    <link rel="stylesheet" type="text/css" href="../vendors/styles/core.css">
    <link rel="stylesheet" type="text/css" href="../vendors/styles/icon-font.min.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/jquery-steps/jquery.steps.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/datatables/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/datatables/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="../vendors/styles/style.css">
</head>

<?php
// Include necessary files
require '../includes/config.php';
require '../includes/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply'])) {
    // Retrieve form data
    $empid = $session_id; // Ensure $session_id is properly initialized
    $leave_type = $_POST['leave_type'];
    $date_from = $_POST['date_from'];
    $date_to = $_POST['date_to'];
    $requested_days = $_POST['requested_days'];
    $outstanding_days = $_POST['outstanding_days'];
    $reason = $_POST['reason'];
    $datePosting = date("Y-m-d");

    // Fetch the role from the tblemployees table
    $role_query = "SELECT role FROM tblemployees WHERE emp_id = ?";
    $stmt = $conn->prepare($role_query);
    $stmt->bind_param('s', $empid);
    $stmt->execute();
    $role_result = $stmt->get_result();

    if ($role_result->num_rows > 0) {
        $row = $role_result->fetch_assoc();
        $role = $row['role'];
    } else {
        echo "<script>alert('Error fetching employee role');</script>";
        exit();
    }

    // Set HodRemarks based on role
    $hod_remarks = ($role === 'Manager'||$role === 'Admin') ? 3 : 'Pending';

    // File upload handling
    $proof = null;
    if (isset($_FILES['proof']) && $_FILES['proof']['error'] === UPLOAD_ERR_OK) {
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
                $error_message = 'Failed to upload proof file.';
            }
        } else {
            $error_message = 'Invalid file type or file size exceeds 2MB.';
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
        // No overlap, insert leave application into the database
        $insert_query = "
            INSERT INTO tblleave (empid, LeaveType, FromDate, ToDate, RequestedDays, DaysOutstand, Reason, PostingDate, Proof, HodRemarks)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param('ssssssssss', $empid, $leave_type, $date_from, $date_to, $requested_days, $outstanding_days, $reason, $datePosting, $proof, $hod_remarks);

        if ($stmt->execute()) {
            // Redirect to leave history page
            header('Location: leave_history.php');
            exit();
        } else {
            $error_message = 'Error applying leave: ' . $stmt->error;
        }
    }
}

// Output any error or success messages
if (isset($error_message)) {
    echo "<script>alert('$error_message');</script>";
} elseif (isset($success_message)) {
    echo "<script>alert('$success_message');</script>";
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
                                <h4>Admin Leave Application</h4>
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
                        <form method="post" action="" autocomplete="off">
                            <!-- Check Staff ID Section -->
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Staff ID</label>
                                        <input name="staff_id" type="text" class="form-control" required="true" autocomplete="off" value="">
                                        <button type="submit" name="check_staff_id" class="btn btn-info">Check Staff Details</button>
                                    </div>
                                </div>
                            </div>

                            <?php if (isset($staff)): ?>
                            <!-- Displaying Employee Details if Staff ID is found -->
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label>Full Name</label>
                                        <input name="firstname" type="text" class="form-control" required="true" value="<?php echo $firstname; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label>Position</label>
                                        <input name="position" type="text" class="form-control" required="true" value="<?php echo $position; ?>" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label>Leave Type</label>
                                        <select name="leave_type" class="custom-select form-control" required="true">
                                            <option value="">Select leave type...</option>
                                            <?php while ($row = mysqli_fetch_assoc($leave_types_query)): ?>
                                                <option value="<?php echo $row['LeaveType']; ?>"><?php echo $row['LeaveType']; ?></option>
                                            <?php endwhile; ?>
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

                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group d-flex justify-content-center" style="margin-top: 50px;">
                                        <button class="btn btn-primary" name="apply" type="submit">Apply Leave</button>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
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
