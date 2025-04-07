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
                    '2025-03-18' => 'Nuzul Al-Quran',
                    '2025-03-31' => 'Hari Raya Aidilfitri',
                    '2025-04-01' => 'Hari Raya Aidilfitri',
                    '2025-05-01' => 'Labour Day',
                    '2025-05-12' => 'Wesak Day',
                    '2025-06-02' => 'Agong\'s Birthday',
                    '2025-06-07' => 'Hari Raya Aidiladha',
                    '2025-06-08' => 'Hari Raya Aidiladha',
                    '2025-06-27' => 'Awal Muharram',
                    '2025-08-31' => 'Merdeka Day',
                    '2025-09-05' => 'Maulidur Rasul',
                    '2025-09-16' => 'Malaysia Day',
                    '2025-10-20' => 'Deepavali',
                    '2025-12-25' => 'Christmas Day',
                ];

                // Fetch Birthdays from the Employees table
                $employeeBirthdaysQuery = mysqli_query($conn, "
                    SELECT FirstName, DATE_FORMAT(DOB, '%m-%d') AS dob
                    FROM tblemployees
                ") or die(mysqli_error($conn));

                $employeeBirthdays = [];
                while ($employee = mysqli_fetch_array($employeeBirthdaysQuery)) {
                    $employeeBirthdays[$employee['dob']] = $employee['FirstName'] . "'s Birthday 🎉";
                }

                // Function to draw the calendar
                function draw_calendar($month, $year, $holidays, $birthdays) {
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
                        $eventData = $holidays[$currentDate] ?? null;

                        $eventName = $eventData[0] ?? null;
                        $eventType = $eventData[1] ?? null;

                        // Check for birthday
                        $birthdayKey = sprintf('%02d-%02d', $month, $currentDay);
                        $birthdayName = $birthdays[$birthdayKey] ?? null;

                        $eventClass = '';
                        if ($eventType === 'holiday') {
                            $eventClass = 'bg-danger';
                        } elseif ($eventType === 'birthday') {
                            $eventClass = 'bg-warning';
                        }

                        $calendar .= "<td class='$eventClass'>";
                        $calendar .= "<div>$currentDay</div>"; // Date number
                        if ($eventName) {
                            $calendar .= "<small>$eventName</small>";
                        }
                        if ($birthdayName) {
                            $calendar .= "<small>$birthdayName</small>";
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

                // Display the calendar for the selected month and year 2025
                echo draw_calendar($selectedMonth, 2025, $malaysiaHolidays, $employeeBirthdays);
                ?>
            </div>

            <?php include('includes/footer.php'); ?>
        </div>
    </div>

    <!-- js -->
    <?php include('includes/scripts.php') ?>
</body>
</html>

<style>
    .calendar-container table {
        width: 100%;
        border-collapse: collapse;
    }

    .calendar-container th, .calendar-container td {
        width: 14.28%; /* Ensure 7 columns fit evenly */
        height: 100px; /* Fixed height for consistent box size */
        vertical-align: top;
        text-align: center;
        border: 1px solid #ddd;
        position: relative;
    }

    .calendar-container td small {
        font-size: 12px; /* Smaller text for event descriptions */
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap; /* Prevent text from wrapping */
    }

    .calendar-container td.bg-danger {
        background-color: #dc3545; /* Bootstrap danger color */
        color: #fff;
    }

    .calendar-container td.bg-warning {
        background-color: #ffc107; /* Bootstrap warning color */
        color: #000;
    }
</style>
