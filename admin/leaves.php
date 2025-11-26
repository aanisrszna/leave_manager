<?php include('includes/header.php')?>
<?php include('../includes/session.php')?>
<?php
if (isset($_GET['delete'])) {
    $delete = $_GET['delete'];

    // Step 1: Get RequestedDays, empid, and LeaveType (as name)
    $query = "SELECT RequestedDays, empid, LeaveType FROM tblleave WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $delete);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $requestedDays, $empid, $leaveType);

    if (mysqli_stmt_fetch($stmt)) {
        mysqli_stmt_close($stmt);

        // Step 2: Get leave_type_id from tblleavetype where LeaveType matches
        $getLeaveTypeIdSql = "SELECT id FROM tblleavetype WHERE LeaveType = ?";
        $stmt_type = mysqli_prepare($conn, $getLeaveTypeIdSql);
        mysqli_stmt_bind_param($stmt_type, "s", $leaveType);
        mysqli_stmt_execute($stmt_type);
        mysqli_stmt_bind_result($stmt_type, $leaveTypeId);

        if (mysqli_stmt_fetch($stmt_type)) {
            mysqli_stmt_close($stmt_type);

            // Step 3: Update employee_leave.available_day
            $update = "UPDATE employee_leave 
                       SET available_day = available_day + ? 
                       WHERE emp_id = ? AND leave_type_id = ?";
            $stmt_update = mysqli_prepare($conn, $update);
            mysqli_stmt_bind_param($stmt_update, "dii", $requestedDays, $empid, $leaveTypeId);
            mysqli_stmt_execute($stmt_update);
            mysqli_stmt_close($stmt_update);

            // Step 4: Delete from tblleave
            $deleteSql = "DELETE FROM tblleave WHERE id = ?";
            $stmt_delete = mysqli_prepare($conn, $deleteSql);
            mysqli_stmt_bind_param($stmt_delete, "i", $delete);
            $result = mysqli_stmt_execute($stmt_delete);
            mysqli_stmt_close($stmt_delete);

            if ($result) {
                echo "<script>alert('Leave record deleted and available days updated successfully');</script>";
                echo "<script type='text/javascript'> document.location = 'leaves.php'; </script>";
            } else {
                echo "<script>alert('Error deleting leave record');</script>";
            }
        } else {
            echo "<script>alert('Leave type not found in tblleavetype');</script>";
        }
    } else {
        echo "<script>alert('Leave record not found');</script>";
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
			<div class="page-header">
				<div class="row">
						<div class="col-md-6 col-sm-12">
							<div class="title">
								<h4>Leave Portal</h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="admin_dashboard.php">Dashboard</a></li>
									<li class="breadcrumb-item active" aria-current="page">All Leave</li>
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
							<th>LEAVE DURATION</th>
							<th>MANAGER STATUS</th>
							<th>DIRECTOR STATUS</th>
							<th class="datatable-nosort">ACTION</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$sql = "SELECT tblleave.id AS lid, tblemployees.FirstName, tblemployees.Role,
								tblleave.FromDate, tblleave.ToDate,
								tblleave.RegRemarks, tblleave.HodRemarks
								FROM tblleave
								JOIN tblemployees ON tblleave.empid = tblemployees.emp_id
								ORDER BY tblleave.id DESC";

						$query = $dbh->prepare($sql);
						$query->execute();
						$results = $query->fetchAll(PDO::FETCH_OBJ);

						foreach ($results as $row) {
						?>
							<tr>
								<td class="table-plus">
									<div class="txt">
										<div class="weight-600"><?php echo $row->FirstName; ?></div>
									</div>
								</td>

								<!-- Leave Duration -->
								<td>
									<?php 
										echo date("d/m/Y", strtotime($row->FromDate)) . 
											" to " . 
											date("d/m/Y", strtotime($row->ToDate)); 
									?>
								</td>

								<!-- Manager Status -->
								<td>
									<?php
									if ($row->Role === 'Manager' || $row->Role === 'Admin') {
										echo '<span style="color: gray">NA</span>';
									} else {
										if ($row->HodRemarks == 1) {
											echo '<span style="color: green">Approved</span>';
										} elseif ($row->HodRemarks == 2) {
											echo '<span style="color: red">Rejected</span>';
										} else {
											echo '<span style="color: blue">Pending</span>';
										}
									}
									?>
								</td>

								<!-- Director Status -->
								<td>
									<?php
									if ($row->RegRemarks == 1) {
										echo '<span style="color: green">Approved</span>';
									} elseif ($row->RegRemarks == 2) {
										echo '<span style="color: red">Rejected</span>';
									} else {
										echo '<span style="color: blue">Pending</span>';
									}
									?>
								</td>

								<!-- Action -->
								<td class="text-center">
									<div class="dropdown">
										<button class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" type="button" data-toggle="dropdown">
											<i class="dw dw-more"></i>
										</button>
										<div class="dropdown-menu dropdown-menu-right">
											<a class="dropdown-item" href="leave_details.php?leaveid=<?php echo $row->lid; ?>">
												<i class="dw dw-eye"></i> View
											</a>
											<a class="dropdown-item text-danger" href="leaves.php?delete=<?php echo $row->lid; ?>" onclick="return confirm('Are you sure you want to delete this leave?');">
												<i class="dw dw-delete-3"></i> Delete
											</a>
										</div>
									</div>
								</td>
							</tr>
						<?php
						}
						?>
					</tbody>
				</table>


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