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
				<!-- <div class="col-md-6 text-md-left">
					<h2 class="h3 mb-0">Approved Leave Record</h2>
				</div> -->
			</div>

			<div class="row">
				<!-- Left Column - Stacked Statistics -->
				<div class="col-md-3">
					<!-- Total Staff -->
					<div class="mb-3">
						<div class="card-box height-100-p widget-style3">
							<?php
							$sql = "SELECT emp_id FROM tblemployees WHERE role != 'Director'";
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


					<!-- Approved Leave -->
					<div class="mb-3">
						<div class="card-box height-100-p widget-style3">
							<?php
							$status = 1;
							$sql = "SELECT id FROM tblleave WHERE RegRemarks = :status";
							$query = $dbh->prepare($sql);
							$query->bindParam(':status', $status, PDO::PARAM_INT); // Ensure it is bound as an integer
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


					<!-- Pending Leave -->
					<div class="mb-3">
						<div class="card-box height-100-p widget-style3">
							<?php
							$status = 0;
							$hodStatus1 = 1;
							$hodStatus2 = 3;
							$sql = "SELECT id FROM tblleave WHERE (HodRemarks = :hodStatus1 OR HodRemarks = :hodStatus2) AND RegRemarks = :status";
							$query = $dbh->prepare($sql);
							$query->bindParam(':hodStatus1', $hodStatus1, PDO::PARAM_INT);
							$query->bindParam(':hodStatus2', $hodStatus2, PDO::PARAM_INT);
							$query->bindParam(':status', $status, PDO::PARAM_INT);
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

					<!-- Rejected Leave -->
					<div>
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

				<!-- Right Column - Two Pie Charts Side by Side -->
				<div class="col-md-9 mb-4">
					<div class="row">
						<!-- First Pie Chart -->
						<div class="col-md-6">
							<?php include('../piechart.php'); ?>
						</div>
						
						<!-- Second Pie Chart -->
						<div class="col-md-6">
							<?php include('../calendar.php'); ?>
						</div>
					</div>
				</div>
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
									AND (tblleave.HodRemarks != 0 OR tblemployees.Role = 'Manager') -- Include Admins' applications
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

			<div class="title pb-10">
                <h2 class="mb-2" style="font-size: 24px; font-weight: 600;">Employee Leave Information</h2> <!-- Smaller title with spacing -->

                <!-- Add the PDF Generation Buttons -->
                <div style="display: flex; gap: 10px; align-items: center; margin-top: 10px;"> <!-- Added margin-top for spacing -->
                    <form method="POST" action="employee_report.php">
                        <button type="submit" name="generate_pdf" class="btn btn-primary btn-sm">Staff Leave (PDF)</button>
                    </form>
                    <form method="POST" action="export_employees_excel.php">
                        <button type="submit" class="btn btn-success btn-sm">Download Staff Data Excel</button>
                    </form>

                    <!-- <form method="POST" action="bar_chart.php">
                        <button type="submit" name="generate_pdf" class="btn btn-success btn-sm">Bar Chart</button>
                    </form> -->
                </div>
            </div>
			<div class="row pb-0">
                <?php
                // Fetch distinct employees
                $sql = "SELECT DISTINCT tblemployees.emp_id, tblemployees.FirstName, tblemployees.Staff_ID, tblemployees.Reporting_To 
                FROM employee_leave
                JOIN tblemployees ON employee_leave.emp_id = tblemployees.emp_id
                WHERE tblemployees.Status = 'Active'";
                $query = $dbh->prepare($sql);
                $query->execute();
                $employees = $query->fetchAll(PDO::FETCH_OBJ);

                // Loop through each employee
                foreach ($employees as $employee) {
                ?>
                    <div class='col-xl-4 col-lg-4 col-md-6 col-sm-12 mb-20'> <!-- Adjusted to 4 columns per row -->
                        <a href="staff_profile.php?staff_id=<?php echo urlencode($employee->emp_id); ?>" style="text-decoration: none;">
                            <div class='card-box height-100-p widget-style3' style="cursor: pointer;">
                                <div class='d-flex justify-content-center align-items-center' 
                                    style='background-color:rgb(70, 142, 209); color: white; padding: 8px;'>
                                    <h6 class='mb-0' style='font-size: 14px; font-weight: 500; text-align: center;'>
                                        <?php echo htmlentities($employee->FirstName); ?> (ID: <?php echo htmlentities($employee->Staff_ID); ?>)
                                        <br><span>Reporting To: <?php echo htmlentities($employee->Reporting_To); ?></span>
                                    </h6>
                                </div>
                                <div class='table-responsive mt-0'>
                                    <table class='table table-bordered table-striped table-hover'>
                                        <thead>
                                            <tr>
                                                <th style='background-color:rgba(119, 124, 120, 0.72); color: white; font-size: 12px;'>Leave Type</th>
                                                <th style='background-color: rgba(119, 124, 120, 0.72); color: white; font-size: 12px;'>Available Days</th>
                                            </tr>
                                        </thead>
                                        <tbody style='background-color: #f9f9f9; font-size: 12px;'>
                                            <?php
                                            // Fetch leave details for each employee
                                            $sql_leaves = "SELECT tblleavetype.LeaveType, employee_leave.available_day
                                                        FROM employee_leave
                                                        JOIN tblleavetype ON employee_leave.leave_type_id = tblleavetype.id
                                                        WHERE employee_leave.emp_id = :emp_id";
                                            $query_leaves = $dbh->prepare($sql_leaves);
                                            $query_leaves->bindParam(':emp_id', $employee->emp_id, PDO::PARAM_INT);
                                            $query_leaves->execute();
                                            $leave_details = $query_leaves->fetchAll(PDO::FETCH_OBJ);

                                            foreach ($leave_details as $leave) {
                                            ?>
                                                <tr>
                                                    <td style='background-color: #f9f9f9;'><?php echo htmlentities($leave->LeaveType); ?></td>
                                                    <td style='background-color: #f9f9f9;'><?php echo htmlentities($leave->available_day); ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php } ?>
            </div>



			<div class="my-5"></div> 

			<?php include('includes/footer.php'); ?>

		</div>
	</div>
	

	<?php include('includes/scripts.php')?>
	
</body>
</html>
