<?php include('includes/header.php')?>
<?php include('../includes/session.php')?>
<body>

	<?php include('includes/navbar.php')?>
	<?php include('includes/right_sidebar.php')?>
	<?php include('includes/left_sidebar.php')?>

	<div class="mobile-menu-overlay"></div>

	<div class="main-container">
		<div class="pd-ltr-20">
			<div class="row align-items-center pb-20">
				<div class="col-md-6">
					<h2 class="h3 mb-0">Data Information</h2>
				</div>
				<div class="col-md-6 text-md-left">
					<h2 class="h3 mb-0">Approved Leave Record</h2>
				</div>
			</div>

			<div class="row">
				<!-- Data Information (Left) -->
				<div class="col-xl-3 col-lg-3">
					<div class="row">
						<!-- First Row: Total Staff & Approved Leave -->
						<div class="col-md-6 mb-20">
							<div class="card-box height-100-p widget-style3">
								<?php
								$sql = "SELECT emp_id FROM tblemployees";
								$query = $dbh->prepare($sql);
								$query->execute();
								$empcount = $query->rowCount();
								?>
								<div class="d-flex flex-wrap">
									<div class="widget-data">
										<div class="weight-700 font-24 text-dark"><?php echo $empcount; ?></div>
										<div class="font-14 text-secondary weight-500">Total Staffs</div>
									</div>
									<div class="widget-icon">
										<div class="icon" data-color="#00eccf"><i class="icon-copy dw dw-user-2"></i></div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-3 mb-20">
							<div class="card-box height-100-p widget-style3">
								<?php
								$status = 1;
								$sql = "SELECT id FROM tblleave WHERE RegRemarks=:status";
								$query = $dbh->prepare($sql);
								$query->bindParam(':status', $status, PDO::PARAM_STR);
								$query->execute();
								$leavecount = $query->rowCount();
								?>
								<div class="d-flex flex-wrap">
									<div class="widget-data">
										<div class="weight-700 font-24 text-dark"><?php echo $leavecount; ?></div>
										<div class="font-14 text-secondary weight-500">Approved Leave</div>
									</div>
									<div class="widget-icon">
										<div class="icon" data-color="#09cc06"><span class="icon-copy fa fa-hourglass"></span></div>
									</div>
								</div>
							</div>
						</div>

						<!-- Second Row: Pending Leave & Rejected Leave -->
						<div class="col-md-3 mb-20">
							<div class="card-box height-100-p widget-style3">
								<?php
								$status = 0;
								$sql = "SELECT id FROM tblleave WHERE RegRemarks=:status";
								$query = $dbh->prepare($sql);
								$query->bindParam(':status', $status, PDO::PARAM_STR);
								$query->execute();
								$leavecount = $query->rowCount();
								?>
								<div class="d-flex flex-wrap">
									<div class="widget-data">
										<div class="weight-700 font-24 text-dark"><?php echo $leavecount; ?></div>
										<div class="font-14 text-secondary weight-500">Pending Leave</div>
									</div>
									<div class="widget-icon">
										<div class="icon"><i class="icon-copy fa fa-hourglass-end" aria-hidden="true"></i></div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-3 mb-20">
							<div class="card-box height-100-p widget-style3">
								<?php
								$status = 2;
								$sql = "SELECT id FROM tblleave WHERE RegRemarks=:status";
								$query = $dbh->prepare($sql);
								$query->bindParam(':status', $status, PDO::PARAM_STR);
								$query->execute();
								$leavecount = $query->rowCount();
								?>
								<div class="d-flex flex-wrap">
									<div class="widget-data">
										<div class="weight-700 font-24 text-dark"><?php echo $leavecount; ?></div>
										<div class="font-14 text-secondary weight-500">Rejected Leave</div>
									</div>
									<div class="widget-icon">
										<div class="icon" data-color="#ff5b5b"><i class="icon-copy fa fa-hourglass-o" aria-hidden="true"></i></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

                <!-- Include Pie Chart -->

			</div>




			<!-- Leave Applications -->
			<div class="card-box mb-30">
				<div class="pd-20">
					<h2 class="text-blue h4">ALL APPLICATIONS</h2>
				</div>
				<div class="pb-20">
					<table class="data-table table stripe hover nowrap">
						<thead>
							<tr>
								<th class="table-plus datatable-nosort">STAFF NAME</th>
								<th>LEAVE TYPE</th>
								<th>APPLIED DATE</th>
								<th>MANAGER STATUS</th>
								<th>DIRECTOR STATUS</th>
								<th class="datatable-nosort">ACTION</th>
							</tr>
						</thead>
						<tbody>
							<?php
							// Modify the query to exclude leaves where HodRemarks = 0
							$sql = "SELECT tblleave.id AS lid, tblemployees.FirstName, tblemployees.Role,
									tblleave.LeaveType, tblleave.PostingDate, tblleave.RegRemarks, tblleave.HodRemarks
									FROM tblleave
									JOIN tblemployees ON tblleave.empid = tblemployees.emp_id
									WHERE tblleave.RegRemarks = 0 -- Pending at director's level
									AND (tblleave.HodRemarks != 0 OR tblemployees.Role = 'Admin') -- Include Admins' applications
									ORDER BY tblleave.id DESC LIMIT 10";

							$query = $dbh->prepare($sql);
							$query->execute();
							$results = $query->fetchAll(PDO::FETCH_OBJ);

							foreach ($results as $row) {
								?>
								<tr>
									<td class="table-plus">
										<div class="txt">
											<div class="weight-600"><?php echo $row->FirstName ?></div>
										</div>
									</td>
									<td><?php echo $row->LeaveType; ?></td>
									<td><?php echo $row->PostingDate; ?></td>
									<td>
										<?php
										if ($row->Role === 'Manager' || $row->Role === 'Admin') {
											echo '<span style="color: gray">NA</span>';
										} else {
											$hodStatus = $row->HodRemarks;
											if ($hodStatus == 1) {
												echo '<span style="color: green">Approved</span>';
											} elseif ($hodStatus == 2) {
												echo '<span style="color: red">Rejected</span>';
											} else {
												echo '<span style="color: blue">Pending</span>';
											}
										}
										?>
									</td>
									<td>
										<?php
										$regStatus = $row->RegRemarks;
										if ($regStatus == 1) {
											echo '<span style="color: green">Approved</span>';
										} elseif ($regStatus == 2) {
											echo '<span style="color: red">Rejected</span>';
										} else {
											echo '<span style="color: blue">Pending</span>';
										}
										?>
									</td>
									<td>
										<div class="table-actions">
											<a title="View" href="leave_details.php?leaveid=<?php echo $row->lid; ?>"><i class="dw dw-eye"></i></a>
										</div>
									</td>
								</tr>

							<?php
							}
							?>
						</tbody>
					</table>
				</div>
			</div>

					<!-- Include Calendar Section -->
			<!-- Include Calendar Section -->
			<!-- Row for Calendar and Pie Chart -->
			<div class="row">
				<!-- Calendar Section (Half Width) -->
				<div class="col-md-6">
					<?php include('calendar.php'); ?>
				</div>

				<!-- Pie Chart Section (Half Width) -->
				<div class="col-md-6">
					<?php include('piechart.php'); ?>
				</div>
			</div>




			<?php include('includes/footer.php'); ?>
		</div>
	</div>

	<?php include('includes/scripts.php')?>
</body>
</html>
