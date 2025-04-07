<?php include('includes/header.php')?>
<?php include('../includes/session.php')?>
<body>
	<?php include('includes/navbar.php')?>

	<?php include('includes/right_sidebar.php')?>

	<?php include('includes/left_sidebar.php')?>

	<div class="mobile-menu-overlay"></div>

	<div class="main-container">
		<div class="pd-ltr-20">
			<div class="page-header">
				<div class="row">
						<div class="col-md-6 col-sm-12">
							<div class="title">
								<h4>Application Portal</h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="hod_dashboard.php">Dashboard</a></li>
									<li class="breadcrumb-item active" aria-current="page">All Application</li>
								</ol>
							</nav>
						</div>
				</div>
			</div>

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
							$sql = "SELECT tblleave.id AS lid, tblemployees.FirstName, tblemployees.Role,
							        tblleave.LeaveType, tblleave.PostingDate, tblleave.RegRemarks, tblleave.HodRemarks
							        FROM tblleave
							        JOIN tblemployees ON tblleave.empid = tblemployees.emp_id
							        ORDER BY tblleave.id";
							$query = $dbh->prepare($sql);
							$query->execute();
							$results = $query->fetchAll(PDO::FETCH_OBJ);

							foreach ($results as $row) {
								?>
									<tr>
										<td class="table-plus">
											<div class="txt">
												<div class="weight-600"><?php echo $row->FirstName ; ?></div>
											</div>
										</td>
										<td><?php echo $row->LeaveType; ?></td>
										<td><?php echo $row->PostingDate; ?></td>
										<td>
											<?php
											if ($row->Role === 'Manager'||$row->Role === 'Admin') {
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

			<?php include('includes/footer.php'); ?>
		</div>
	</div>
	<!-- js -->

	<script src="../vendors/scripts/core.js"></script>
	<script src="../vendors/scripts/script.min.js"></script>
	<script src="../vendors/scripts/process.js"></script>
	<script src="../vendors/scripts/layout-settings.js"></script>
	<script src="../src/plugins/apexcharts/apexcharts.min.js"></script>
	<script src="../src/plugins/datatables/js/jquery.dataTables.min.js"></script>
	<script src="../src/plugins/datatables/js/dataTables.bootstrap4.min.js"></script>
	<script src="../src/plugins/datatables/js/dataTables.responsive.min.js"></script>
	<script src="../src/plugins/datatables/js/responsive.bootstrap4.min.js"></script>

	<!-- buttons for Export datatable -->
	<script src="../src/plugins/datatables/js/dataTables.buttons.min.js"></script>
	<script src="../src/plugins/datatables/js/buttons.bootstrap4.min.js"></script>
	<script src="../src/plugins/datatables/js/buttons.print.min.js"></script>
	<script src="../src/plugins/datatables/js/buttons.html5.min.js"></script>
	<script src="../src/plugins/datatables/js/buttons.flash.min.js"></script>
	<script src="../src/plugins/datatables/js/vfs_fonts.js"></script>
	
	<script src="../vendors/scripts/datatable-setting.js"></script></body>
</body>
</html>