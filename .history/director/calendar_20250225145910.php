<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<style>
    td {
        position: relative;
        padding: 15px;
        cursor: pointer;
    }

    td:hover::after {
        content: attr(data-event);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.75);
        color: white;
        padding: 5px 10px;
        border-radius: 5px;
        white-space: nowrap;
        display: block;
        font-size: 12px;
    }
</style>

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
<form method="POST" action="generate_report.php">
    <button type="submit" name="generate_report" class="btn btn-success">Generate Calendar Report (PDF)</button>
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
                $calendarEvents = array_merge_recursive($malaysiaHolidays, $birthdays, $leaveDates);

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

                        $eventName = $eventData[0] ?? null;
                        $eventType = $eventData[1] ?? null;

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
                        if ($eventName) {
                            $calendar .= "<br><small>$eventName</small>";
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