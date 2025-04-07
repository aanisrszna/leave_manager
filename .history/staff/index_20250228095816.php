<?php include('includes/header.php') ?>
<?php include('../includes/session.php') ?>
<body>

    <?php include('includes/navbar.php') ?>
    <?php include('includes/right_sidebar.php') ?>
    <?php include('includes/left_sidebar.php') ?>

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
                    el.emp_id = '$session_id'
                    AND lt.LeaveType NOT IN ('Emergency Leave', 'Unpaid Leave');
            ") or die(mysqli_error($conn));
            

                // Iterate through the fetched results
                while ($row = mysqli_fetch_array($query)) { 
                    // Avoid division by zero
                    $assigned_day = max($row['assigned_day'], 1); 
                    $progress_percentage = ($row['available_day'] / $assigned_day) * 100;
                ?>
                    <div class="col-xl-3 col-lg-6 col-md-12 mb-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-body text-center">
                                <i class="fas fa-calendar-check fa-2x text-primary mb-3"></i>
                                <h5 class="card-title font-weight-bold text-dark" style="font-size: 1rem;">
                                    <?php echo htmlspecialchars($row['LeaveType']); ?>
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


					<!-- Include Calendar Section -->
			<!-- Include Calendar Section -->
			<!-- Row for Calendar and Pie Chart -->
			<div class="row">
				<!-- Calendar Section (Half Width) -->
				<div class="col-md-6">
					<?php include('../calendar.php'); ?>
				</div>

				<!-- Pie Chart Section (Half Width) -->
				<div class="col-md-6">
					<?php include('../piechart.php'); ?>
				</div>

			</div> <!-- Close the previous row div -->

			<!-- Add a spacing div before the footer -->
			<div class="my-5"></div> 

            <?php include('includes/footer.php'); ?>
        </div>
    </div>

    <!-- js -->
    <?php include('includes/scripts.php') ?>
</body>
</html>
