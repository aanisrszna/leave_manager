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
                // Fetch Leave Types and available days directly from employee_leave
                $query = mysqli_query($conn, "
                    SELECT 
                        el.leave_type_id,
                        lt.LeaveType,
                        el.available_day
                    FROM 
                        employee_leave el
                    INNER JOIN 
                        tblleavetype lt 
                        ON el.leave_type_id = lt.id
                    WHERE 
                        el.emp_id = '$session_id';
                ") or die(mysqli_error($conn));

                while ($row = mysqli_fetch_array($query)) {
                ?>
                <div class="col-xl-6 col-lg-6 col-md-12 mb-20">
                    <div class="card-box height-100-p widget-style3">
                        <div class="d-flex flex-wrap">
                            <div class="widget-data">
                                <div class="weight-700 font-24 text-dark"><?php echo $row['LeaveType']; ?></div>
                                <div class="font-18 text-secondary weight-500">
                                    Available Days: <?php echo $row['available_day']; ?>
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

            <!-- Calendar Section -->
            <div class="calendar-container">
                <h2 class="h4 mb-20">Calendar 2025</h2>
                <div class="calendar">
                    <?php
                    function draw_calendar($month, $year) {
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
                            $calendar .= "<td class='text-center'>$currentDay</td>";
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

                    for ($month = 1; $month <= 12; $month++) {
                        echo draw_calendar($month, 2025);
                    }
                    ?>
                </div>
            </div>

            <?php include('includes/footer.php'); ?>
        </div>
    </div>

    <!-- js -->
    <?php include('includes/scripts.php') ?>
</body>
</html>
