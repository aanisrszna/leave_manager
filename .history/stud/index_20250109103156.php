<?php include('includes/header.php')?>
<?php include('../includes/session.php')?>
<body>

	<?php include('includes/navbar.php')?>

	<?php include('includes/right_sidebar.php')?>

	<?php include('includes/left_sidebar.php')?>

	<div class="mobile-menu-overlay"></div>
	
	<div class="main-container">
		<div class="pd-ltr-20">
			<div class="row">
				<div class="col-lg-6 col-md-12 mb-20">
					<div class="card-box height-100-p pd-20 min-height-200px">
						<div class="d-flex justify-content-between pb-10">
							<div class="h5 mb-0">Employee Leave Types</div>
						</div>
						<div class="user-list">
							<ul>
								<?php
									// Query to fetch employee leave types and available days
									$query = mysqli_query($conn, "SELECT 
										lt.leave_type AS LeaveType, 
										lt.available_days AS AvailableDays,
										e.FirstName, 
										e.LastName 
									FROM tblleavetype lt 
									JOIN tblemployees e ON e.emp_id = lt.emp_id 
									WHERE e.Department = '$session_depart' 
									ORDER BY lt.leave_type ASC 
									LIMIT 10") or die(mysqli_error($conn));
									
									while ($row = mysqli_fetch_array($query)) {
								?>
								<li class="d-flex align-items-center justify-content-between">
									<div class="name-avatar d-flex align-items-center pr-2">
										<div class="txt">
											<div class="font-14 weight-600"><?php echo $row['FirstName'] . " " . $row['LastName']; ?></div>
											<div class="font-12 weight-500" data-color="#b2b1b6">
												<?php echo "Leave Type: " . $row['LeaveType']; ?>
											</div>
											<div class="font-12 weight-500" data-color="#17a2b8">
												<?php echo "Available Days: " . $row['AvailableDays']; ?>
											</div>
										</div>
									</div>
								</li>
								<?php } ?>
							</ul>
						</div>
					</div>
				</div>
				<div class="col-lg-6 col-md-12 mb-20">
					<div class="card-box height-100-p pd-20 min-height-200px">
						<div class="d-flex justify-content-between">
							<div class="h5 mb-0">Apply for Leave</div>
						</div>
						<p>To apply for leave, click <a href="apply_leave.php">here</a>.</p>
					</div>
				</div>
			</div>

			<?php include('includes/footer.php'); ?>
		</div>
	</div>
	<!-- js -->

	<?php include('includes/scripts.php')?>
</body>
</html>
