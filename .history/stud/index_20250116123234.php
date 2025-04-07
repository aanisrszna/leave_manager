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
                        el.available_day
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
                ?>
                <div class="col-xl-6 col-lg-6 col-md-12 mb-20">
                    <div class="card-box height-100-p widget-style3">
                        <div class="d-flex flex-wrap">
                            <div class="widget-data">
                                <div class="weight-700 font-24 text-dark"><?php echo htmlspecialchars($row['LeaveType']); ?></div>
                                <div class="font-18 text-secondary weight-500">
                                    Available Days: <?php echo htmlspecialchars($row['available_day']); ?>
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
                $birthdayQuery = mysqli_query($conn, "
                    SELECT FirstName, Dob 
                    FROM tblemployees;
                ") or die(mysqli_error($conn));

                // Create an array to store birthdays
                $birthdays = [];
                while ($row = mysqli_fetch_array($birthdayQuery)) {
                    $dob = $row['Dob'];
                    $firstname = $row['FirstName'];

                    // Adjust the date to the current year (2025)
                    $dobDate = new DateTime($dob);
                    $dobDate->setDate(2025, $dobDate->format('m'), $dobDate->format('d'));

                    $formattedDate = $dobDate->format('Y-m-d');
                    $birthdays[$formattedDate] = [$firstname . "'s Birthday 🎂", 'birthday'];
                }

                // Fetch leave data and map to calendar events
                $leaveQuery = mysqli_query($conn, "
                    SELECT tblleave.FromDate, tblleave.ToDate, tblemployees.FirstName, tblleave.RegRemarks 
                    FROM tblleave 
                    INNER JOIN tblemployees ON tblleave.empid = tblemployees.emp_id
                ") or die(mysqli_error($conn));

                // Array to store leave dates with multiple employees' names for the same date
                $leaveDates = [];
                while ($row = mysqli_fetch_array($leaveQuery)) {
                    // Check if RegRemarks equals 1
                    if ($row['RegRemarks'] == 1) {
                        $fromDate = new DateTime($row['FromDate']);
                        $toDate = new DateTime($row['ToDate']);
                        $firstname = $row['FirstName'];
                        
                        // Generate all dates between FromDate and ToDate
                        while ($fromDate <= $toDate) {
                            $formattedDate = $fromDate->format('Y-m-d');
                            
                            // Add employee name to the leaveDates array for the same date
                            if (!isset($leaveDates[$formattedDate])) {
                                $leaveDates[$formattedDate] = [
                                    'name' => ["$firstname's Leave 🌊"],
                                    'type' => 'leave'
                                ];
                            } else {
                                // If date already exists, append the name
                                $leaveDates[$formattedDate]['name'][] = "$firstname's Leave 🌊";
                            }
                            
                            $fromDate->modify('+1 day');
                        }
                    }
                }

                // Merge all events (holidays, birthdays, leaves)
                $calendarEvents = array_merge($malaysiaHolidays, $birthdays, $leaveDates);

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
                        $eventData = $events[$currentDate] ?? null;

                        $eventNames = $eventData['name'] ?? [];
                        $eventType = $eventData['type'] ?? null;

                        $eventClass = '';
                        if ($eventType === 'holiday') {
                            $eventClass = 'bg-danger text-white';
                        } elseif ($eventType === 'birthday') {
                            $eventClass = 'bg-warning text-dark';
                        } elseif ($eventType === 'leave') {
                            $eventClass = 'bg-primary text-white';
                        }

                        $calendar .= "<td class='text-center $eventClass'>";
                        $calendar .= $currentDay;

                        // Display multiple names for leave events
                        if ($eventNames) {
                            foreach ($eventNames as $name) {
                                $calendar .= "<br><small>$name</small>";
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
    <?php include('includes/scripts.php') ?>
</body>
</html>
