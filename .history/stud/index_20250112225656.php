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
            // Fetch the latest applied leave for notification
            $latestLeaveQuery = mysqli_query($conn, "
                SELECT RegRemarks 
                FROM tblleave 
                WHERE empid = '$session_id' 
                ORDER BY id DESC 
                LIMIT 1;
            ") or die(mysqli_error($conn));

            // Display a notification based on the latest leave application status
            if (mysqli_num_rows($latestLeaveQuery) > 0) {
                $latestLeave = mysqli_fetch_array($latestLeaveQuery);
                if ($latestLeave['RegRemarks'] == 1) {
                    echo "<div class='alert alert-success'>Your Leave Has Been Approved!</div>";
                } elseif ($latestLeave['RegRemarks'] == 2) {
                    echo "<div class='alert alert-danger'>Sorry, Your Leave Has Been Rejected.</div>";
                }
            }
            ?>

            <div class="row pb-10">
                <?php
                // Fetch distinct leave types with calculated available days
                $query = mysqli_query($conn, "
                    SELECT 
                        el.leave_type_id,
                        lt.LeaveType,
                        el.available_day,
                        COALESCE(SUM(CASE WHEN tl.RegRemarks = 1 THEN tl.DaysOutstand ELSE 0 END), 0) AS OutstandingDays
                    FROM 
                        employee_leave el
                    INNER JOIN 
                        tblleavetype lt 
                        ON el.leave_type_id = lt.id
                    LEFT JOIN 
                        tblleave tl 
                        ON el.emp_id = tl.empid 
                        AND el.leave_type_id = tl.leave_type_id
                    WHERE 
                        el.emp_id = '$session_id'
                    GROUP BY 
                        el.leave_type_id, lt.LeaveType, el.available_day;
                ") or die(mysqli_error($conn));

                while ($row = mysqli_fetch_array($query)) {
                    // Calculate the available days
                    $availableDays = max($row['OutstandingDays'], $row['available_day']);
                ?>
                <div class="col-xl-6 col-lg-6 col-md-12 mb-20">
                    <div class="card-box height-100-p widget-style3">
                        <div class="d-flex flex-wrap">
                            <div class="widget-data">
                                <div class="weight-700 font-24 text-dark"><?php echo $row['LeaveType']; ?></div>
                                <div class="font-18 text-secondary weight-500">
                                    Available Days: <?php echo $availableDays; ?>
                                </div>
                            </div>
                            <div class="widget-icon">
                                <div class="icon" data-color="#17a2b8"><i class="icon-copy fa fa-calendar-check-o"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>

            <?php include('includes/footer.php'); ?>
        </div>
    </div>

    <!-- js -->
    <?php include('includes/scripts.php') ?>
</body>
</html>
