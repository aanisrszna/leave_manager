<?php include('includes/header.php')?>
<?php include('../includes/session.php')?>
<body>

	<?php include('includes/navbar.php')?>

	<?php include('includes/right_sidebar.php')?>

	<?php include('includes/left_sidebar.php')?>

	<div class="mobile-menu-overlay"></div>

	<div class="main-container">
		<div class="pd-ltr-20">
		<div class="title pb-20">
                <h2 class="h3 mb-0">Employee Leave Types</h2>
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

					// Exclude "Emergency Leave" and "Unpaid Leave"
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
			<div class="row pb-10">
				<div class="col-xl-3 col-lg-3 col-md-6 mb-20">
					<div class="card-box height-100-p widget-style3">

						<?php
						$sql = "SELECT emp_id from tblemployees";
						$query = $dbh -> prepare($sql);
						$query->execute();
						$results=$query->fetchAll(PDO::FETCH_OBJ);
						$empcount=$query->rowCount();
						?>

						<div class="d-flex flex-wrap">
							<div class="widget-data">
								<div class="weight-700 font-24 text-dark"><?php echo($empcount);?></div>
								<div class="font-14 text-secondary weight-500">Total Staffs</div>
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
						$sql = "SELECT id from tblleave where HodRemarks=:status";
						$query = $dbh -> prepare($sql);
						$query->bindParam(':status',$status,PDO::PARAM_STR);
						$query->execute();
						$results=$query->fetchAll(PDO::FETCH_OBJ);
						$leavecount=$query->rowCount();
						?>        

						<div class="d-flex flex-wrap">
							<div class="widget-data">
								<div class="weight-700 font-24 text-dark"><?php echo htmlentities($leavecount); ?></div>
								<div class="font-14 text-secondary weight-500">Approved Leave</div>
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
						$status = 0;
						$excludedRoles = ['manager', 'admin']; // Exclude both Manager and Admin

						$sql = "SELECT l.id 
								FROM tblleave l
								INNER JOIN tblemployees e ON l.empid = e.emp_id 
								WHERE l.HodRemarks = :status 
								AND e.role NOT IN ('manager', 'admin')"; // Exclude both roles

						$query = $dbh->prepare($sql);
						$query->bindParam(':status', $status, PDO::PARAM_INT); // Use INT for numerical values
						$query->execute();
						$results = $query->fetchAll(PDO::FETCH_OBJ);
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
				<div class="col-xl-3 col-lg-3 col-md-6 mb-20">
					<div class="card-box height-100-p widget-style3">

						<?php
						$status=2;
						$sql = "SELECT id from tblleave where HodRemarks=:status";
						$query = $dbh -> prepare($sql);
						$query->bindParam(':status',$status,PDO::PARAM_STR);
						$query->execute();
						$results=$query->fetchAll(PDO::FETCH_OBJ);
						$leavecount=$query->rowCount();
						?>  

						<div class="d-flex flex-wrap">
							<div class="widget-data">
								<div class="weight-700 font-24 text-dark"><?php echo($leavecount); ?></div>
								<div class="font-14 text-secondary weight-500">Rejected Leave</div>
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
					<h2 class="text-blue h4">LATEST LEAVE APPLICATIONS</h2>
				</div>
				<div class="pb-20">
					<table class="data-table table stripe hover nowrap">
						<thead>
							<tr>
								<th class="table-plus datatable-nosort">STAFF NAME</th>
								<th>LEAVE TYPE</th>
								<th>APPLIED DATE</th>
								<th>MY REMARKS</th>
								<th>REG. REMARKS</th>
								<th class="datatable-nosort">ACTION</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<?php 
								// SQL query to fetch leave applications where HodRemarks is 0 (Pending)
								$sql = "SELECT tblleave.id as lid,tblemployees.FirstName,tblemployees.LastName,tblemployees.emp_id,tblemployees.Gender,tblemployees.Phonenumber,tblemployees.EmailId,tblemployees.Position_Staff,tblemployees.Staff_ID,tblleave.LeaveType,tblleave.ToDate,tblleave.FromDate,tblleave.PostingDate,tblleave.RequestedDays,tblleave.DaysOutstand,tblleave.Sign,tblleave.HodRemarks,tblleave.RegRemarks,tblleave.HodSign,tblleave.RegSign,tblleave.HodDate,tblleave.RegDate 
										FROM tblleave 
										JOIN tblemployees ON tblleave.empid = tblemployees.emp_id 
										WHERE tblemployees.role = 'Staff' 
										AND Department = '$session_depart' 
										AND tblleave.HodRemarks = 0  -- Only show Pending leaves
										ORDER BY lid DESC LIMIT 5";
								$query = $dbh->prepare($sql);
								$query->execute();
								$results = $query->fetchAll(PDO::FETCH_OBJ);
								$cnt = 1;
								if ($query->rowCount() > 0) {
									foreach ($results as $result) {         
								?>  

								<td class="table-plus">
									<div class="name-avatar d-flex align-items-center">
										<div class="txt mr-2 flex-shrink-0">
											<b><?php echo htmlentities($cnt);?></b>
										</div>
										<div class="txt">
											<div class="weight-600"><?php echo htmlentities($result->FirstName." ".$result->LastName);?></div>
										</div>
									</div>
								</td>
								<td><?php echo htmlentities($result->LeaveType);?></td>
								<td><?php echo htmlentities($result->PostingDate);?></td>
								<td>
									<?php 
									$stats = $result->HodRemarks;
									if ($stats == 1) {
										echo '<span style="color: green">Approved</span>';
									} elseif ($stats == 2) {
										echo '<span style="color: red">Rejected</span>';
									} elseif ($stats == 0) {
										echo '<span style="color: blue">Pending</span>';
									}
									?>
								</td>
								<td>
									<?php 
									$stats = $result->RegRemarks;
									if ($stats == 1) {
										echo '<span style="color: green">Approved</span>';
									} elseif ($stats == 2) {
										echo '<span style="color: red">Rejected</span>';
									} elseif ($stats == 0) {
										echo '<span style="color: blue">Pending</span>';
									}
									?>
								</td>
								<td>
									<div class="dropdown">
										<a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
											<i class="dw dw-more"></i>
										</a>
										<div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
											<a class="dropdown-item" href="leave_details.php?leaveid=<?php echo htmlentities($result->lid); ?>"><i class="dw dw-eye"></i> View</a>
										</div>
									</div>
								</td>
							</tr>
							<?php $cnt++; } } ?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<?php include('../calendar.php'); ?>
				</div>

				<div class="col-md-6">
					<?php include('../piechart.php'); ?>
				</div>

			</div> <!-- Close the previous row div -->

			<div class="my-5"></div> 

			<?php include('includes/footer.php'); ?>
		</div>
		
	</div>
	<!-- js -->

	<?php include('includes/scripts.php')?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let currentMonth = new Date().getMonth() + 1;
        let currentYear = 2025;

        function loadCalendar(month) {
            fetch(`../calendar.php?month=${month}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById("calendarContainer").innerHTML = data;
                    document.getElementById("currentMonthText").innerText = new Date(2025, month - 1, 1).toLocaleString('en-US', { month: 'long', year: 'numeric' });
                });
        }

        loadCalendar(currentMonth);

        document.getElementById("prevMonth").addEventListener("click", function() {
            currentMonth = (currentMonth - 1 < 1) ? 12 : currentMonth - 1;
            loadCalendar(currentMonth);
        });

        document.getElementById("nextMonth").addEventListener("click", function() {
            currentMonth = (currentMonth + 1 > 12) ? 1 : currentMonth + 1;
            loadCalendar(currentMonth);
        });
    });
</script>
</body>
</html>