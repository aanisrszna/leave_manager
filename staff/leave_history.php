<?php include('includes/header.php')?>
<?php include('../includes/session.php')?>

<?php
if (isset($_GET['delete'])) {
	$delete = intval($_GET['delete']);
	// Ensure only delete leave if RegRemarks == 0
	$check = mysqli_query($conn, "SELECT RegRemarks FROM tblleave WHERE id = $delete AND empid = '$session_id'");
	$row = mysqli_fetch_assoc($check);

	if ($row && $row['RegRemarks'] == 0) {
		$sql = "DELETE FROM tblleave WHERE id = $delete AND empid = '$session_id'";
		$result = mysqli_query($conn, $sql);
		if ($result) {
			echo "<script>alert('Leave request withdrawn successfully');</script>";
			echo "<script type='text/javascript'> document.location = 'leave_history.php'; </script>";
		}
	} else {
		echo "<script>alert('You can only delete pending leave requests.');</script>";
	}
}
?>

<body>

	<?php include('includes/navbar.php')?>

	<?php include('includes/right_sidebar.php')?>

	<?php include('includes/left_sidebar.php')?>

	<div class="mobile-menu-overlay"></div>

	<div class="main-container">
		<div class="pd-ltr-20">
			<div class="title pb-20">
				<h2 class="h3 mb-0">Leave Breakdown</h2>
			</div>
			<div class="row pb-10">
				<div class="col-xl-3 col-lg-3 col-md-6 mb-20">
					<div class="card-box height-100-p widget-style3">

						<?php
						$sql = "SELECT id from tblleave";
						$query = $dbh -> prepare($sql);
						$query->execute();
						$results=$query->fetchAll(PDO::FETCH_OBJ);
						$empcount=$query->rowCount();
						?>

						<div class="d-flex flex-wrap">
							<div class="widget-data">
								<div class="weight-700 font-24 text-dark"><?php echo($empcount);?></div>
								<div class="font-14 text-secondary weight-500">All Applied Leave</div>
							</div>
							<div class="widget-icon">
								<div class="icon" data-color="#00eccf"><i class="icon-copy dw dw-user-2"></i></div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-lg-3 col-md-6 mb-20">
					<div class="card-box height-100-p widget-style3">

						<?php 
						 $status=1;
						 $query = mysqli_query($conn,"select * from tblleave where empid = '$session_id' AND RegRemarks = '$status'")or die(mysqli_error());
						 $count_reg_staff = mysqli_num_rows($query);
						 ?>

						<div class="d-flex flex-wrap">
							<div class="widget-data">
								<div class="weight-700 font-24 text-dark"><?php echo htmlentities($count_reg_staff); ?></div>
								<div class="font-14 text-secondary weight-500">Approved</div>
							</div>
							<div class="widget-icon">
								<div class="icon" data-color="#09cc06"><span class="icon-copy fa fa-hourglass"></span></div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-lg-3 col-md-6 mb-20">
					<div class="card-box height-100-p widget-style3">

						<?php 
						 $status=0;
						 $query_pend = mysqli_query($conn,"select * from tblleave where empid = '$session_id' AND RegRemarks = '$status'")or die(mysqli_error());
						 $count_pending = mysqli_num_rows($query_pend);
						 ?>

						<div class="d-flex flex-wrap">
							<div class="widget-data">
								<div class="weight-700 font-24 text-dark"><?php echo($count_pending); ?></div>
								<div class="font-14 text-secondary weight-500">Pending</div>
							</div>
							<div class="widget-icon">
								<div class="icon"><i class="icon-copy fa fa-hourglass-end" aria-hidden="true"></i></div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-lg-3 col-md-6 mb-20">
					<div class="card-box height-100-p widget-style3">

						<?php 
						 $status=2;
						 $query_reject = mysqli_query($conn,"select * from tblleave where empid = '$session_id' AND RegRemarks = '$status'")or die(mysqli_error());
						 $count_reject = mysqli_num_rows($query_reject);
						 ?>

						<div class="d-flex flex-wrap">
							<div class="widget-data">
								<div class="weight-700 font-24 text-dark"><?php echo($count_reject); ?></div>
								<div class="font-14 text-secondary weight-500">Rejected</div>
							</div>
							<div class="widget-icon">
								<div class="icon" data-color="#ff5b5b"><i class="icon-copy fa fa-hourglass-o" aria-hidden="true"></i></div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="card-box mb-30">
				<div class="pd-20">
						<h2 class="text-blue h4">ALL MY LEAVE</h2>
					</div>
				<div class="pb-20">
					<table class="data-table table stripe hover nowrap">
						<thead>
							<tr>
								<th class="table-plus">LEAVE TYPE</th>
								<th>FROM</th>
								<th>TO</th>
								<th>DAYS</th>
								<th>MANAGER STATUS</th>
								<th>DIRECTOR STATUS</th>
								<th class="datatable-nosort">ACTION</th>
							</tr>
						</thead>
						<tbody>
							<tr>
							<?php 
								$sql = "SELECT * FROM tblleave WHERE empid = '$session_id' ORDER BY PostingDate DESC";
								$query = $dbh->prepare($sql);
								$query->execute();
								$results = $query->fetchAll(PDO::FETCH_OBJ);
								$cnt = 1;
								if ($query->rowCount() > 0) {
									foreach ($results as $result) { ?>  

										<td><?php echo htmlentities($result->LeaveType);?></td>
										<td><?php echo htmlentities($result->FromDate);?></td>
										<td><?php echo htmlentities($result->ToDate);?></td>
										<td><?php echo htmlentities($result->RequestedDays);?></td>
										<td>
											<?php 
											$stats = $result->HodRemarks;
											if ($stats == 1) { ?>
												<span style="color: green">Approved</span>
											<?php } elseif ($stats == 2) { ?>
												<span style="color: red">Not Approved</span>
											<?php } elseif ($stats == 0) { ?>
												<span style="color: blue">Pending</span>
											<?php } ?>
										</td>
										<td>
											<?php 
											$stats = $result->RegRemarks;
											if ($stats == 1) { ?>
												<span style="color: green">Approved</span>
											<?php } elseif ($stats == 2) { ?>
												<span style="color: red">Not Approved</span>
											<?php } elseif ($stats == 0) { ?>
												<span style="color: blue">Pending</span>
											<?php } ?>
										</td>
										<td>
											<div class="dropdown">
												<a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
													<i class="dw dw-more"></i>
												</a>
												<div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
													<a class="dropdown-item" href="view_leaves.php?edit=<?php echo htmlentities($result->id); ?>"><i class="dw dw-eye"></i> View</a>
													<?php if ($result->RegRemarks == 0): ?>
														<a class="dropdown-item" href="leave_history.php?delete=<?php echo htmlentities($result->id); ?>" onclick="return confirm('Are you sure you want to withdraw this leave?');"><i class="dw dw-delete-3"></i> Delete</a>
													<?php endif; ?>
												</div>
											</div>
										</td>

									</tr>
							<?php 
									$cnt++; 
									} 
								} 
							?>

						</tbody>
					</table>
			   </div>
			</div>

			<?php include('includes/footer.php'); ?>
		</div>
	</div>
	<!-- js -->

	<?php include('includes/scripts.php')?>
</body>
</html>