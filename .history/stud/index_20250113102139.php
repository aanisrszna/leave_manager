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
                    '2025-01-01' => 'New Year\'s Day',
                    '2025-01-29' => 'Chinese New Year',
                    '2025-01-30' => 'Chinese New Year',
                    '2025-02-01' => 'Federal Territory Day',
                    '2025-02-11' => 'Thaipusm',
                    '2025-02-20' => 'Chinese New Year (Day 2)',
                    '2025-05-01' => 'Labour Day',
                    '2025-05-17' => 'Hari Raya Aidilfitri',
                    '2025-05-18' => 'Hari Raya Aidilfitri (Day 2)',
                    '2025-06-07' => 'Agong\'s Birthday',
                    '2025-08-31' => 'Merdeka Day',
                    '2025-09-16' => 'Malaysia Day',
                    '2025-10-28' => 'Deepavali',
                    '2025-12-25' => 'Christmas Day',
                ];

                // Function to draw the calendar
                function draw_calendar($month, $year, $holidays) {
                    // Days of the week
                    $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

                    // First day of the month
                    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);

                    // Total number of days in the month
                    $numberOfDays = date('t', $firstDayOfMonth);

                    // Get information about the first day of the month
                    $dateComponents = getdate($firstDayOfMonth);
                    $monthName = $dateComponents['month'];
                    $dayOfWeek = $dateComponents['wday'];

                    // Start building the calendar HTML
                    $calendar = "<table class='table table-bordered'>";
                    $calendar .= "<thead><tr><th colspan='7'>$monthName $year</th></tr></thead>";
                    $calendar .= "<tr>";

                    // Create the header row with days of the week
                    foreach ($daysOfWeek as $day) {
                        $calendar .= "<th class='text-center'>$day</th>";
                    }

                    $calendar .= "</tr><tr>";

                    // Fill the first row with blank cells if the month doesn't start on Sunday
                    if ($dayOfWeek > 0) {
                        $calendar .= str_repeat("<td></td>", $dayOfWeek);
                    }

                    // Initialize the current day
                    $currentDay = 1;

                    // Fill the calendar with the days of the month
                    while ($currentDay <= $numberOfDays) {
                        // If Sunday, start a new row
                        if ($dayOfWeek == 7) {
                            $dayOfWeek = 0;
                            $calendar .= "</tr><tr>";
                        }

                        // Generate the current date
                        $currentDate = sprintf('%04d-%02d-%02d', $year, $month, $currentDay);

                        // Highlight holidays
                        $holidayName = isset($holidays[$currentDate]) ? $holidays[$currentDate] : null;
                        $holidayClass = $holidayName ? 'table-danger' : '';

                        // Add the day to the calendar
                        $calendar .= "<td class='text-center $holidayClass'>";
                        $calendar .= $currentDay;
                        if ($holidayName) {
                            $calendar .= "<br><small class='text-muted'>$holidayName</small>";
                        }
                        $calendar .= "</td>";

                        // Increment the day and the day of the week
                        $currentDay++;
                        $dayOfWeek++;
                    }

                    // Fill the remaining cells with blank cells if the month doesn't end on Saturday
                    if ($dayOfWeek != 7) {
                        $remainingDays = 7 - $dayOfWeek;
                        $calendar .= str_repeat("<td></td>", $remainingDays);
                    }

                    $calendar .= "</tr></table>";

                    return $calendar;
                }

                // Get the selected month or default to the current month
                $selectedMonth = isset($_GET['month']) ? intval($_GET['month']) : date('n');

                // Display the calendar for the selected month and year 2025
                echo draw_calendar($selectedMonth, 2025, $malaysiaHolidays);
                ?>
            </div>

            <?php include('includes/footer.php'); ?>
        </div>
    </div>

    <!-- js -->
    <?php include('includes/scripts.php') ?>
</body>
</html>
