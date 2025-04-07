<?php include('includes/header.php'); ?>
<?php include('../includes/session.php'); ?>
<body>
    <?php include('includes/navbar.php'); ?>
    <?php include('includes/right_sidebar.php'); ?>
    <?php include('includes/left_sidebar.php'); ?>

    <div class="mobile-menu-overlay"></div>

    <div class="main-container">
        <div class="pd-ltr-20">
        <div class="title pb-20">
                <h2 class="h3 mb-0">Leave Entitlement</h2>
            </div>

            <?php
            // Fetch the most recent leave status for the logged-in employee
            $latestLeaveQuery = mysqli_query($conn, "
                SELECT id, RegRemarks, empid, notification_shown 
                FROM tblleave 
                WHERE empid = '$session_id' 
                ORDER BY id DESC 
                LIMIT 1;
            ") or die(mysqli_error($conn));

            // Check if any leave records are found
            if (mysqli_num_rows($latestLeaveQuery) > 0) {
                $leave = mysqli_fetch_array($latestLeaveQuery);
                $leave_id = $leave['id'];  // Get the leave ID
                $emp_id = $leave['empid'];  // Get the empid associated with this leave
                $notification_shown = $leave['notification_shown']; // Get the notification_shown status

                // Only show notification if it hasn't been shown yet
                if ($notification_shown == 0) {
                    // Initialize a flag to track whether the notification was shown
                    $notification_displayed = false;

                    // Notify based on the RegRemarks value
                    if ($leave['RegRemarks'] == 1) {
                        // Leave approved notification
                        echo "<div class='alert alert-success'>🎉 Your Leave Has Been Approved!</div>";
                        $notification_displayed = true;  // Set flag to true since the notification is shown
                    } elseif ($leave['RegRemarks'] == 2) {
                        // Leave rejected notification
                        echo "<div class='alert alert-danger'>😔 Sorry, Your Leave Has Been Rejected.</div>";
                        $notification_displayed = true;  // Set flag to true since the notification is shown
                    }

                    // Only update the notification_shown field if a notification was displayed
                    if ($notification_displayed) {
                        $updateQuery = mysqli_query($conn, "
                            UPDATE tblleave 
                            SET notification_shown = 1 
                            WHERE id = '$leave_id';
                        ") or die(mysqli_error($conn));
                    }
                }
            } else {
                echo "<div class='alert alert-warning'>No leave record found for this employee.</div>";
            }
            ?>

            <div class="row pb-10">
                <?php
                // Secure session variable and connection handling
                $session_id = mysqli_real_escape_string($conn, $session_id);

                // Fetch Leave Types and available days directly from employee_leave
                $query = mysqli_query($conn, "
                    SELECT 
                        el.leave_type_id,
                        lt.LeaveType,
                        el.available_day,
                        lt.assigned_day
                    FROM 
                        employee_leave el
                    INNER JOIN 
                        tblleavetype lt 
                        ON el.leave_type_id = lt.id
                    WHERE 
                        el.emp_id = '$session_id';
                ") or die(mysqli_error($conn));

                // Iterate through the fetched results
                while ($row = mysqli_fetch_array($query)) { 
                    $leaveType = $row['LeaveType'];

                    // Exclude Emergency Leave and Unpaid Leave
                    if ($leaveType === 'Emergency Leave' || $leaveType === 'Unpaid Leave') {
                        continue;
                    }

                    // Avoid division by zero
                    $assigned_day = max($row['assigned_day'], 1); 
                    $progress_percentage = ($row['available_day'] / $assigned_day) * 100;
                ?>
                    <div class="col-xl-3 col-lg-6 col-md-12 mb-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-body text-center">
                                <i class="fas fa-calendar-check fa-2x text-primary mb-3"></i>
                                <h5 class="card-title font-weight-bold text-dark" style="font-size: 1rem;">
                                    <?php echo htmlspecialchars($leaveType); ?>
                                </h5>
                                <p class="text-muted" style="font-size: 0.8rem;">
                                    Available Days: <strong><?php echo htmlspecialchars($row['available_day']); ?></strong> /
                                    <strong><?php echo htmlspecialchars($row['assigned_day']); ?></strong>
                                </p>
                                
                                <!-- Dynamic Progress Bar -->
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: <?php echo min($progress_percentage, 150); ?>%;" 
                                        aria-valuenow="<?php echo $row['available_day']; ?>" 
                                        aria-valuemin="0" 
                                        aria-valuemax="<?php echo $assigned_day; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

            </div>
            <div class="title pb-20">
                <h2 class="h3 mb-0">Data Information</h2>
            </div>
			<div class="row">
				<!-- Left Column - Stacked Statistics -->
				<div class="col-md-3">
					<!-- Total Staff -->
					<div class="mb-3">
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

					<!-- Approved Leave -->
					<div class="mb-3">
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

					<!-- Pending Leave -->
					<div class="mb-3">
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

            <div class="title pb-10">
                <h2 class="mb-2" style="font-size: 24px; font-weight: 600;">Employee Leave Information</h2> <!-- Smaller title with spacing -->

                <!-- Add the PDF Generation Buttons -->
                <div style="display: flex; gap: 10px; align-items: center; margin-top: 10px;"> <!-- Added margin-top for spacing -->
                    <form class="mb-4" method="POST" action="employee_report.php">
                        <button type="submit" name="generate_pdf" class="btn btn-primary btn-sm">Generate PDF Report</button> <!-- Smaller button -->
                    </form>
                    <!-- <form method="POST" action="bar_chart.php">
                        <button type="submit" name="generate_pdf" class="btn btn-success btn-sm">Bar Chart</button>
                    </form> -->
                </div>
            </div>



            <div class="row pb-0">
                <?php
                // Fetch distinct employees
                $sql = "SELECT DISTINCT tblemployees.emp_id, tblemployees.FirstName, tblemployees.Staff_ID,tblemployees.Reporting_To 
                        FROM employee_leave
                        JOIN tblemployees ON employee_leave.emp_id = tblemployees.emp_id";
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
                                            WHERE employee_leave.emp_id = :emp_id
                                            ORDER BY tblleavetype.id ASC";
                            
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

        </div>




    <!-- js -->
    <?php include('includes/scripts.php') ?>
</body>
</html>
