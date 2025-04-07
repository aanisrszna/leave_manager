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
						$excludedRole = 'manager'; // Assuming the role for manager is stored as 'manager' in the tblemployees table

						$sql = "SELECT l.id 
								FROM tblleave l
								INNER JOIN tblemployees e ON l.empid = e.emp_id 
								WHERE l.HodRemarks = :status 
								AND e.role != :excludedRole";
						$query = $dbh->prepare($sql);
						$query->bindParam(':status', $status, PDO::PARAM_STR);
						$query->bindParam(':excludedRole', $excludedRole, PDO::PARAM_STR);
						$query->execute();
						$results = $query->fetchAll(PDO::FETCH_OBJ);
						$leavecount = $query->rowCount();
						?>
    

						<div class="d-flex flex-wrap">
							<div class="widget-data">
								<div class="weight-700 font-24 text-dark"><?php echo($leavecount); ?></div>
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
			            <!-- Calendar Section -->
			<form method="GET" class="mb-4">
                <label for="month">Select Month:</label>
                <select name="month" id="month" class="form-control" style="width: auto; display: inline-block;">
                    <?php
                    // Months array
                    $months = [
                        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                    ];
                    // Generate month dropdown options
                    foreach ($months as $num => $name) {
                        $selected = (isset($_GET['month']) && $_GET['month'] == $num) ? 'selected' : '';
                        echo "<option value='$num' $selected>$name</option>";
                    }
                    ?>
                </select>
                <button type="submit" class="btn btn-primary">Show Calendar</button>
            </form>

            <!-- Calendar Display -->
            <div class="calendar-container">
                <?php
                // List of Malaysian Public Holidays for 2025
                $malaysiaHolidays = [
                    '2025-01-01' => ['New Year\'s Day 🎆', 'holiday'],
                    '2025-01-29' => ['Chinese New Year 🧧', 'holiday'],
                    '2025-01-30' => ['Chinese New Year 🧧', 'holiday'],
                    '2025-02-01' => ['Federal Territory Day 🌏', 'holiday'],
                    '2025-02-11' => ['Thaipusm', 'holiday'],
                    '2025-03-18' => ['Nuzul Al-Quran 🌙', 'holiday'],
                    '2025-03-31' => ['Hari Raya Aidilfitri ✨', 'holiday'],
                    '2025-04-01' => ['Hari Raya Aidilfitri ✨', 'holiday'],
                    '2025-05-01' => ['Labour Day 💼', 'holiday'],
                    '2025-05-12' => ['Wesak Day', 'holiday'],
                    '2025-06-02' => ['Agong\'s Birthday 🥳', 'holiday'],
                    '2025-06-07' => ['Hari Raya Aidiladha 🐪', 'holiday'],
                    '2025-06-08' => ['Hari Raya Aidiladha 🐪', 'holiday'],
                    '2025-06-27' => ['Awal Muharram', 'holiday'],
                    '2025-08-31' => ['Merdeka Day', 'holiday'],
                    '2025-09-05' => ['Maulidur Rasul', 'holiday'],
                    '2025-09-16' => ['Malaysia Day 🎆', 'holiday'],
                    '2025-10-20' => ['Deepavali', 'holiday'],
                    '2025-12-25' => ['Christmas Day 🎄', 'holiday'],
                ];

                // Fetch employee birthdays
				// Fetch employee birthdays
				$birthdayQuery = mysqli_query($conn, "
					SELECT FirstName, Dob 
					FROM tblemployees;
				") or die(mysqli_error($conn));

				$birthdays = [];
				while ($row = mysqli_fetch_array($birthdayQuery)) {
					$dob = $row['Dob'];
					$firstname = $row['FirstName'];

					// Adjust the date to the current year (2025)
					$dobDate = new DateTime($dob);
					$dobDate->setDate(2025, $dobDate->format('m'), $dobDate->format('d'));

					$formattedDate = $dobDate->format('Y-m-d');
					// Store only the event name (no type)
					$birthdays[$formattedDate] = ["$firstname's Birthday 🎂",'birthday'];
				}


				// Merge all events (holidays, birthdays, leaves)
                // Fetch leave data and map to calendar events
                $leaveQuery = mysqli_query($conn, "
                    SELECT tblleave.FromDate, tblleave.ToDate, tblemployees.FirstName, tblleave.RegRemarks 
                    FROM tblleave 
                    INNER JOIN tblemployees ON tblleave.empid = tblemployees.emp_id
                ") or die(mysqli_error($conn));

                // Array to store leave dates
                $leaveDates = [];
                while ($row = mysqli_fetch_array($leaveQuery)) {
                    if ($row['RegRemarks'] == 1) {
                        $fromDate = new DateTime($row['FromDate']);
                        $toDate = new DateTime($row['ToDate']);
                        $firstname = $row['FirstName'];

                        // Add all dates between FromDate and ToDate to the leaveDates array
                        while ($fromDate <= $toDate) {
                            $formattedDate = $fromDate->format('Y-m-d');

                            if (isset($leaveDates[$formattedDate])) {
                                // Concatenate names for overlapping leaves
                                $leaveDates[$formattedDate][0] .= '<br> ' . $firstname . "'s Leave 🌊";
                            } else {
                                $leaveDates[$formattedDate] = [$firstname . "'s Leave 🌊", 'leave'];
                            }

                            $fromDate->modify('+1 day');
                        }
                    }
                }

                // Merge all events (holidays, birthdays, leaves)
				// Merge all events (holidays, birthdays, leaves)
				$calendarEvents = [];

				foreach ([$malaysiaHolidays, $birthdays, $leaveDates] as $eventArray) {
					foreach ($eventArray as $date => $event) {
						if (!isset($calendarEvents[$date])) {
							$calendarEvents[$date] = [];
						}
						// Merge events into an array for the same date
						if (is_array($event[0])) {
							$calendarEvents[$date] = array_merge($calendarEvents[$date], $event);
						} else {
							$calendarEvents[$date][] = $event;
						}
					}
				}


                // Function to draw the calendar
				// Function to draw the calendar
				function draw_calendar($month, $year, $events) {
					$daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
					$firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
					$numberOfDays = date('t', $firstDayOfMonth);
					$dateComponents = getdate($firstDayOfMonth);
					$monthName = $dateComponents['month'];
					$dayOfWeek = $dateComponents['wday'];
				
					$calendar = "<table class='table table-bordered'>";
					$calendar .= "<thead><tr><th colspan='7'>$monthName $year</th></tr></thead>";
					$calendar .= "<tr>";
				
					foreach ($daysOfWeek as $day) {
						$calendar .= "<th class='text-center'>$day</th>";
					}
				
					$calendar .= "</tr><tr>";
				
					if ($dayOfWeek > 0) {
						$calendar .= str_repeat("<td></td>", $dayOfWeek);
					}
				
					$currentDay = 1;
				
					while ($currentDay <= $numberOfDays) {
						if ($dayOfWeek == 7) {
							$dayOfWeek = 0;
							$calendar .= "</tr><tr>";
						}
				
						$currentDate = sprintf('%04d-%02d-%02d', $year, $month, $currentDay);
						$eventData = $events[$currentDate] ?? [];
				
						$calendar .= "<td class='text-center'>";
						$calendar .= $currentDay;
				
						if ($eventData) {
							foreach ($eventData as $event) {
								$eventName = $event[0] ?? ''; // Event name
								$eventType = $event[1] ?? ''; // Event type
				
								$eventClass = '';
								if ($eventType === 'holiday') {
									$eventClass = 'bg-danger text-white';
								} elseif ($eventType === 'birthday') {
									$eventClass = 'bg-warning text-dark';
								} elseif ($eventType === 'leave') {
									$eventClass = 'bg-primary text-white';
								}
				
								$calendar .= "<br><small class='$eventClass'>$eventName</small>";
							}
						}
				
						$calendar .= "</td>";
				
						$currentDay++;
						$dayOfWeek++;
					}
				
					if ($dayOfWeek != 7) {
						$remainingDays = 7 - $dayOfWeek;
						$calendar .= str_repeat("<td></td>", $remainingDays);
					}
				
					$calendar .= "</tr></table>";
				
					return $calendar;
				}
				
				

                // Get the selected month or default to the current month
                $selectedMonth = isset($_GET['month']) ? intval($_GET['month']) : date('n');

                // Display the updated calendar
                echo draw_calendar($selectedMonth, 2025, $calendarEvents);
                ?>
            </div>


			<?php include('includes/footer.php'); ?>
		</div>
		
	</div>
	<!-- js -->

	<?php include('includes/scripts.php')?>
</body>
</html>