<?php 
error_reporting(0);
include('includes/header.php');
include('../includes/session.php');
include('../send_email.php'); // Include email function

// Code to update the read notification status
$isread = 1;
$did = intval($_GET['leaveid']);  
date_default_timezone_set('Asia/Kolkata');
$admremarkdate = date('Y-m-d G:i:s', strtotime("now"));

$sql = "UPDATE tblleave SET IsRead=:isread WHERE id=:did";
$query = $dbh->prepare($sql);
$query->bindParam(':isread', $isread, PDO::PARAM_STR);
$query->bindParam(':did', $did, PDO::PARAM_STR);
$query->execute();

// Code for action taken on leave
if (isset($_POST['update'])) { 
    $did = intval($_GET['leaveid']);
    $status = $_POST['status'];   

    // Fetch employee's available leave days and requested leave days
    $query = mysqli_query($conn, "
        SELECT 
            emp.FirstName,
            emp.EmailId,
            el.available_day, 
            tl.RequestedDays, 
            tl.LeaveType,
            tl.FromDate,
            tl.ToDate,
            tl.reason,
            tl.empid,
            lt.id AS leave_type_id
        FROM tblleave AS tl
        JOIN tblemployees AS emp ON tl.empid = emp.emp_id
        JOIN tblleavetype AS lt ON tl.LeaveType = lt.LeaveType
        JOIN employee_leave AS el ON tl.empid = el.emp_id AND lt.id = el.leave_type_id
        WHERE tl.id = '$did'
    ") or die(mysqli_error($conn));

    $row = mysqli_fetch_assoc($query);

    $emp_name = $row['FirstName'];
    $staff_email = $row['EmailId'];
    $available_day = $row['available_day'];
    $requested_days = $row['RequestedDays'];
    $leave_type = $row['LeaveType'];
	$date_from = date("d/m/Y", strtotime($row['FromDate']));
	$date_to = date("d/m/Y", strtotime($row['ToDate']));

    $reason = $row['reason'];
    $leave_type_id = $row['leave_type_id'];
    $emp_id = $row['empid'];

    // Calculate remaining leave
    $REMLEAVE = $available_day - $requested_days;

    // Get the signature of the approver
    $query = mysqli_query($conn, "SELECT signature FROM tblemployees WHERE emp_id = '$session_id'") or die(mysqli_error($conn));
    $row = mysqli_fetch_assoc($query);

    $signature = $row['signature'];

    date_default_timezone_set('Asia/Kolkata');
    $admremarkdate = date('Y-m-d', strtotime("now"));

    if ($status === '2') { // Leave rejected
        $sql = "UPDATE tblleave SET RegRemarks=:status, RegDate=:admremarkdate WHERE id=:did";
        $query = $dbh->prepare($sql);
        $query->bindParam(':status', $status, PDO::PARAM_STR);
        $query->bindParam(':admremarkdate', $admremarkdate, PDO::PARAM_STR);
        $query->bindParam(':did', $did, PDO::PARAM_STR);
        $query->execute();

        // Send Rejection Email
        $subject = "Leave Application Rejected";
        $message = "
            <p>Dear $emp_name,</p>
            <p>We regret to inform you that your leave application has been <b>rejected</b>.</p>
            <table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; width: 100%;'>
                <tr><th align='left'>Leave Type</th><td>$leave_type</td></tr>
                <tr><th align='left'>From</th><td>$date_from</td></tr>
                <tr><th align='left'>To</th><td>$date_to</td></tr>
                <tr><th align='left'>Requested Days</th><td>$requested_days</td></tr>
                <tr><th align='left'>Reason</th><td>$reason</td></tr>
            </table>
            <p>If you need further clarification, please contact your manager.</p>
            <p>Best regards,<br><strong>e-Leave Manager System</strong></p>
        ";

        //send_email($staff_email, $subject, $message);

        echo "<script>alert('Leave rejected Successfully');</script>";
    } elseif ($status === '1') { // Leave approved
        $result = mysqli_query($conn, "
            UPDATE tblleave AS tl
            JOIN tblleavetype AS lt ON tl.LeaveType = lt.LeaveType
            JOIN employee_leave AS el ON tl.empid = el.emp_id AND lt.id = el.leave_type_id
            SET 
                tl.RegRemarks = '$status',
                tl.RegSign = '$signature',
                tl.RegDate = '$admremarkdate',
                tl.DaysOutstand = '$REMLEAVE',
                el.available_day = '$REMLEAVE'
            WHERE tl.id = '$did'
        ");

        if ($result) {
            // Send Approval Email
            $subject = "Leave Application Approved";
            $message = "
                <p>Dear $emp_name,</p>
                <p>Your leave application has been <b>approved</b>.</p>
                <table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; width: 100%;'>
                    <tr><th align='left'>Leave Type</th><td>$leave_type</td></tr>
                    <tr><th align='left'>From</th><td>$date_from</td></tr>
                    <tr><th align='left'>To</th><td>$date_to</td></tr>
                    <tr><th align='left'>Requested Days</th><td>$requested_days</td></tr>
                    <tr><th align='left'>Remaining Leave Balance</th><td>$REMLEAVE days</td></tr>
                    <tr><th align='left'>Reason</th><td>$reason</td></tr>
                </table>
                <p>Please ensure to adhere to the leave schedule.</p>
                <p>Best regards,<br><strong>e-Leave Manager System</strong></p>
            ";

            //send_email($staff_email, $subject, $message);

            echo "<script>alert('Leave approved Successfully');</script>";
        } else {
            die(mysqli_error($conn));
        }
    }
}
?>


<style>
	input[type="text"]
	{
	    font-size:16px;
	    color: #0f0d1b;
	    font-family: Verdana, Helvetica;
	}

	.btn-outline:hover {
	  color: #fff;
	  background-color: #524d7d;
	  border-color: #524d7d; 
	}

	textarea { 
		font-size:16px;
	    color: #0f0d1b;
	    font-family: Verdana, Helvetica;
	}

	textarea.text_area{
        height: 8em;
        font-size:16px;
	    color: #0f0d1b;
	    font-family: Verdana, Helvetica;
      }

	</style>

	</div>

	<?php include('includes/navbar.php')?>

	<?php include('includes/right_sidebar.php')?>

	<?php include('includes/left_sidebar.php')?>

	<div class="mobile-menu-overlay"></div>

	<div class="main-container">
		<div class="pd-ltr-20">
			<div class="min-height-200px">
				<div class="page-header">
					<div class="row">
						<div class="col-md-6 col-sm-12">
							<div class="title">
								<h4>LEAVE DETAILS</h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="admin_dashboard.php">Home</a></li>
									<li class="breadcrumb-item active" aria-current="page">Leave</li>
								</ol>
							</nav>
						</div>
						<div class="col-md-6 col-sm-12 text-right">
							<div class="dropdown show">
								<a class="btn btn-primary" href="report_pdf.php?leave_id=<?php echo $_GET['leaveid'] ?>">
									Generate Form PDF
								</a>
							</div>
						</div>
					</div>
				</div>

				<div class="pd-20 card-box mb-30">
					<div class="clearfix">
						<div class="pull-left">
							<h4 class="text-blue h4">Leave Details</h4>
							<p class="mb-20"></p>
						</div>
					</div>
					<form method="post" action="">

						<?php 
						if(!isset($_GET['leaveid']) && empty($_GET['leaveid'])){
							header('Location: admin_dashboard.php');
						}
						else {
						
						$lid=intval($_GET['leaveid']);
						$sql = "SELECT tblleave.id as lid,tblemployees.FirstName,tblemployees.emp_id,tblemployees.Gender,tblemployees.Phonenumber,tblemployees.EmailId,tblemployees.Position_Staff,tblemployees.Staff_ID,tblleave.LeaveType,tblleave.ToDate,tblleave.FromDate,tblleave.PostingDate,tblleave.RequestedDays,tblleave.DaysOutstand,tblleave.Sign,tblleave.HodRemarks,tblleave.RegRemarks,tblleave.HodSign,tblleave.RegSign,tblleave.HodDate,tblleave.RegDate, tblleave.proof, tblleave.reason from tblleave join tblemployees on tblleave.empid=tblemployees.emp_id where tblleave.id=:lid";
						$query = $dbh -> prepare($sql);
						$query->bindParam(':lid',$lid,PDO::PARAM_STR);
						$query->execute();
						$results=$query->fetchAll(PDO::FETCH_OBJ);
						$cnt=1;
						if($query->rowCount() > 0)
						{
						foreach($results as $result)
						{         
						?>  

						<div class="row">
							<div class="col-md-4 col-sm-12">
								<div class="form-group">
									<label style="font-size:16px;"><b>Full Name</b></label>
									<input type="text" class="selectpicker form-control" data-style="btn-outline-primary" readonly value="<?php echo htmlentities($result->FirstName);?>">
								</div>
							</div>
							<div class="col-md-4 col-sm-12">
								<div class="form-group">
									<label style="font-size:16px;"><b>Staff Position</b></label>
									<input type="text" class="selectpicker form-control" data-style="btn-outline-info" readonly value="<?php echo htmlentities($result->Position_Staff);?>">
								</div>
							</div>
							<div class="col-md-4 col-sm-12">
								<div class="form-group">
									<label style="font-size:16px;"><b>Staff ID</b></label>
									<input type="text" class="selectpicker form-control" data-style="btn-outline-success" readonly value="<?php echo htmlentities($result->Staff_ID);?>">
								</div>
							</div>
							<div class="col-md-4 col-sm-12">
								<div class="form-group">
									<label style="font-size:16px;"><b>Phone Number</b></label>
									<input type="text" class="selectpicker form-control" data-style="btn-outline-primary" readonly value="<?php echo htmlentities($result->Phonenumber);?>">
								</div>
							</div>
							<div class="col-md-4 col-sm-12">
								<div class="form-group">
									<label style="font-size:16px;"><b>Gender</b></label>
									<input type="text" class="selectpicker form-control" data-style="btn-outline-success" readonly value="<?php echo htmlentities($result->Gender);?>">
								</div>
							</div>
							<div class="col-md-4 col-sm-12">
								<div class="form-group">
									<label style="font-size:16px;"><b>Leave Type</b></label>
									<input type="text" class="selectpicker form-control" data-style="btn-outline-info" readonly value="<?php echo htmlentities($result->LeaveType);?>">
								</div>
							</div>


							<div class="col-md-4 col-sm-12">
								<div class="form-group">
									<label style="font-size:16px;"><b>Requested Number of Days</b></label>
									<input type="text" class="selectpicker form-control" data-style="btn-outline-info" readonly name="RequestedDays" value="<?php echo htmlentities($result->RequestedDays);?>">
								</div>
							</div>
							<div class="col-md-4 col-sm-12">
								<div class="form-group">
									<label style="font-size:16px;"><b>Number Days still outstanding</b></label>
									<input type="text" class="selectpicker form-control" data-style="btn-outline-info" readonly name="DaysOutstand" value="<?php echo htmlentities($result->DaysOutstand);?>">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label style="font-size:16px;"><b>Leave Period</b></label>
									<input type="text" class="selectpicker form-control" data-style="btn-outline-info" readonly 
									    value="From <?php echo date('d/m/Y', strtotime($result->FromDate)); ?> to <?php echo date('d/m/Y', strtotime($result->ToDate)); ?>">
								</div>
							</div>

						</div>
						<div class="form-group row">
								<div class="col-md-6 col-sm-12">
								<div class="form-group">
									<label style="font-size:16px;"><b>Email Address</b></label>
									<input type="text" class="selectpicker form-control" data-style="btn-outline-info" readonly value="<?php echo htmlentities($result->EmailId);?>">
								</div>
							</div>
							<div class="col-md-6 col-sm-12">
								<div class="form-group">
									<label style="font-size:16px;"><b>Applied Date</b></label>
									<input type="text" class="selectpicker form-control" data-style="btn-outline-success" readonly 
										value="<?php echo date('d/m/Y', strtotime($result->PostingDate)); ?>">
								</div>
							</div>

							<!-- <div class="col-md-4 col-sm-12">
								<div class="form-group">
									<label style="font-size:16px;"><b>Staff Signature</b></label>
									<div class="avatar mr-2 flex-shrink-0">
										<img src="<?php echo '../signature/'.($result->Sign);?>" width="60" height="40" alt="">
									</div>
								</div>
							</div> -->
						</div>
						<div class="form-group row">
								<div class="col-md-12 col-sm-12">
								<div class="form-group">
									<label style="font-size:16px;"><b>Reason</b></label>
									<input type="text" class="selectpicker form-control" data-style="btn-outline-info" readonly value="<?php echo htmlentities($result->reason);?>">
								</div>
							</div>
						</div>

						<div class="form-group row">
							<div class="col-md-12">
								<label style="font-size:16px;"><b>Proof Picture</b></label>
								<div class="avatar mr-2 flex-shrink-0">
									<?php if (!empty($result->proof)) { 
										// Remove any leading "proof/" if it's already in the value
										$proofPath = preg_replace('#^proof/#', '', $result->proof);
										$fullPath = '../proof/' . htmlentities($proofPath);
										$extension = pathinfo($proofPath, PATHINFO_EXTENSION);
										
										if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
									?>
											<img src="<?php echo $fullPath; ?>" style="max-width: 100%; height: auto;" alt="Proof Picture">
									<?php 
										} else {
											// Fallback for non-image files like PDF
									?>
											<a href="<?php echo $fullPath; ?>" target="_blank">View uploaded file (<?php echo strtoupper($extension); ?>)</a>
									<?php 
										} 
									} else { ?>
										<p>No proof picture uploaded.</p>
									<?php } ?>
								</div>
							</div>
						</div>


						<div class="form-group row">
							<div class="col-md-6 col-sm-12">
							    <div class="form-group">
									<label style="font-size:16px;"><b>Manager's Approval</b></label>
									<?php
									if ($result->HodSign==""): ?>
									  <input type="text" class="selectpicker form-control" data-style="btn-outline-primary" readonly value="<?php echo "NA"; ?>">
									<?php else: ?>
									  <div class="avatar mr-2 flex-shrink-0">
										<img src="<?php echo '../signature/'.($result->HodSign);?>" width="100" height="40" alt="">
									  </div>
									<?php endif ?>
							    </div>
							</div>
							<div class="col-md-6 col-sm-12">
								<div class="form-group">
									<label style="font-size:16px;"><b>Director's Approval</b></label>
									<?php
									if ($result->RegSign==""): ?>
									  <input type="text" class="selectpicker form-control" data-style="btn-outline-primary" readonly value="<?php echo "NA"; ?>">
									<?php else: ?>
									  <div class="avatar mr-2 flex-shrink-0">
										<img src="<?php echo '../signature/'.($result->RegSign);?>" width="100" height="40" alt="">
									  </div>
									<?php endif ?>
								</div>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-md-6 col-sm-12">
							    <div class="form-group">
									<label style="font-size:16px;"><b>Date For Manager's Action</b></label>
									<?php if ($result->HodDate == ""): ?>
									<input type="text" class="selectpicker form-control" data-style="btn-outline-primary" readonly value="NA">
									<?php else: ?>
									<div class="avatar mr-2 flex-shrink-0">
										<input type="text" class="selectpicker form-control" data-style="btn-outline-primary" readonly 
										value="<?php echo date('d/m/Y', strtotime($result->HodDate)); ?>">
									</div>
									<?php endif; ?>
							    </div>
							</div>
							<div class="col-md-6 col-sm-12">
								<div class="form-group">
									<label style="font-size:16px;"><b>Date For Director's Action</b></label>
									<?php if ($result->RegDate == ""): ?>
									<input type="text" class="selectpicker form-control" data-style="btn-outline-primary" readonly value="NA">
									<?php else: ?>
									<div class="avatar mr-2 flex-shrink-0">
										<input type="text" class="selectpicker form-control" data-style="btn-outline-primary" readonly 
										value="<?php echo date('d/m/Y', strtotime($result->RegDate)); ?>">
									</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label style="font-size:16px;"><b>Leave Status From Manager</b></label>
									<?php 
									$stats = $result->HodRemarks;  // Employee's status from HOD
									$emp_id = $result->emp_id; // Employee who applied for leave (emp_id)
									
									// Fetch the role of the employee who applied for the leave
									$roleQuery = $dbh->prepare("SELECT role FROM tblemployees WHERE emp_id = :emp_id");
									$roleQuery->bindParam(':emp_id', $emp_id, PDO::PARAM_INT);
									$roleQuery->execute();
									$roleResult = $roleQuery->fetch(PDO::FETCH_ASSOC);
									$role = $roleResult['role']; // Role of the employee who applied for leave

									// Check if the employee's role is Manager
									if ($role == 'Manager'|| $role == 'Admin'): ?>
										<input type="text" style="color: grey; font-size: 16px;" class="selectpicker form-control" data-style="btn-outline-primary" readonly value="NA">
									<?php elseif ($stats == 1): ?>
										<input type="text" style="color: green;" class="selectpicker form-control" data-style="btn-outline-primary" readonly value="Approved">
									<?php elseif ($stats == 2): ?>
										<input type="text" style="color: red; font-size: 16px;" class="selectpicker form-control" data-style="btn-outline-primary" readonly value="Rejected">
									<?php else: ?>
										<input type="text" style="color: blue;" class="selectpicker form-control" data-style="btn-outline-primary" readonly value="Pending">
									<?php endif ?>
								</div>
							</div>


							<div class="col-md-6">
								<div class="form-group">
									<label style="font-size:16px;"><b>Leave Status From Director</b></label>
									<?php $ad_stats=$result->RegRemarks;?>
									<?php
									if ($ad_stats==1): ?>
									  <input type="text" style="color: green;" class="selectpicker form-control" data-style="btn-outline-primary" readonly value="<?php echo "Approved"; ?>">
									<?php
									 elseif ($ad_stats==2): ?>
									  <input type="text" style="color: red; font-size: 16px;" class="selectpicker form-control" data-style="btn-outline-primary" readonly value="<?php echo "Rejected"; ?>">
									  <?php
									else: ?>
									  <input type="text" style="color: blue;" class="selectpicker form-control" data-style="btn-outline-primary" readonly value="<?php echo "Pending"; ?>">
									<?php endif ?>
								</div>
							</div>


						</div>

						<div class="row justify-content-center align-items-center">
							<?php 
							if(($stats==0 AND $ad_stats==0) OR ($stats==1 AND $ad_stats==0) OR ($stats==1 AND $ad_stats==2)OR ($stats==3 AND $ad_stats==0) OR  ($stats==3 AND $ad_stats==2))
							{
							?>
							<div class="col-md-4">
								<div class="form-group">
									<label style="font-size:16px;"><b></b></label>
									<div class="modal-footer justify-content-center">
										<button class="btn btn-primary" id="action_take" data-toggle="modal" data-target="#success-modal">Take&nbsp;Action</button>
									</div>
								</div>
							</div>

							<form name="adminaction" method="post">
								<div class="modal fade" id="success-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
									<div class="modal-dialog modal-dialog-centered" role="document">
										<div class="modal-content">
											<div class="modal-body text-center font-18">
												<h4 class="mb-20">Leave take action</h4>
												<select name="status" required class="custom-select form-control">
													<option value="">Choose your option</option>
													<option value="1">Approve</option>
													<option value="2">Reject</option>
												</select>
											</div>
											<div class="modal-footer justify-content-center">
												<input type="submit" class="btn btn-primary" name="update" value="Submit">
											</div>
										</div>
									</div>
								</div>
							</form>

							<?php }?> 
						</div>


						<?php $cnt++;} } }?>
					</form>
				</div>

			</div>
			
			<?php include('includes/footer.php'); ?>
		</div>
	</div>
	<!-- js -->

	<?php include('includes/scripts.php')?>
</body>
</html>