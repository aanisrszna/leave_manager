<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar with Events</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .calendar-container {
            max-width: 800px;
            margin: 0 auto;
        }
        td {
            height: 80px;
            vertical-align: middle;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Event Calendar - 2025</h2>

    <!-- Month Selection Form -->
    <form method="GET" class="mb-4 text-center">
        <label for="month">Select Month:</label>
        <select name="month" id="month" class="form-control d-inline-block w-auto">
            <?php
            $months = [
                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
            ];
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
        $conn = mysqli_connect("localhost", "root", "", "your_database"); // Replace with your DB details

        // List of Malaysian Public Holidays for 2025
        $malaysiaHolidays = [
            '2025-01-01' => ['New Year\'s Day 🎆', 'holiday'],
            '2025-01-29' => ['Chinese New Year 🧧', 'holiday'],
            '2025-01-30' => ['Chinese New Year 🧧', 'holiday'],
            '2025-02-01' => ['Federal Territory Day 🌏', 'holiday'],
            '2025-02-11' => ['Thaipusam', 'holiday'],
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
        $birthdays = [];
        $birthdayQuery = mysqli_query($conn, "SELECT FirstName, Dob FROM tblemployees;");
        while ($row = mysqli_fetch_array($birthdayQuery)) {
            $dob = new DateTime($row['Dob']);
            $dob->setDate(2025, $dob->format('m'), $dob->format('d'));
            $formattedDate = $dob->format('Y-m-d');
            $birthdays[$formattedDate] = [$row['FirstName'] . "'s Birthday 🎂", 'birthday'];
        }

        // Fetch leave data
        $leaveDates = [];
        $leaveQuery = mysqli_query($conn, "
            SELECT tblleave.FromDate, tblleave.ToDate, tblemployees.FirstName, tblleave.RegRemarks 
            FROM tblleave 
            INNER JOIN tblemployees ON tblleave.empid = tblemployees.emp_id
        ");
        while ($row = mysqli_fetch_array($leaveQuery)) {
            if ($row['RegRemarks'] == 1) {
                $fromDate = new DateTime($row['FromDate']);
                $toDate = new DateTime($row['ToDate']);
                while ($fromDate <= $toDate) {
                    $formattedDate = $fromDate->format('Y-m-d');
                    $leaveDates[$formattedDate] = [$row['FirstName'] . "'s Leave 🌊", 'leave'];
                    $fromDate->modify('+1 day');
                }
            }
        }

        // Merge all events
        $calendarEvents = array_merge_recursive($malaysiaHolidays, $birthdays, $leaveDates);

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
                $eventNames = [];
                $eventColors = [];

                if ($eventData) {
                    foreach ($eventData as $event) {
                        $eventNames[] = $event[0];
                        if ($event[1] === 'holiday') $eventColors[] = 'bg-danger text-white';
                        elseif ($event[1] === 'leave') $eventColors[] = 'bg-primary text-white';
                        elseif ($event[1] === 'birthday') $eventColors[] = 'bg-warning text-dark';
                    }
                }

                $eventClass = in_array('bg-danger text-white', $eventColors) ? 'bg-danger text-white' :
                              (in_array('bg-primary text-white', $eventColors) ? 'bg-primary text-white' :
                              (in_array('bg-warning text-dark', $eventColors) ? 'bg-warning text-dark' : ''));

                $tooltip = $eventNames ? 'title="' . implode(' & ', $eventNames) . '"' : '';

                $calendar .= "<td class='text-center $eventClass' data-bs-toggle='tooltip' $tooltip>$currentDay</td>";

                $currentDay++;
                $dayOfWeek++;
            }

            if ($dayOfWeek != 7) {
                $calendar .= str_repeat("<td></td>", 7 - $dayOfWeek);
            }

            $calendar .= "</tr></table>";
            return $calendar;
        }

        $selectedMonth = isset($_GET['month']) ? intval($_GET['month']) : date('n');
        echo draw_calendar($selectedMonth, 2025, $calendarEvents);
        ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

</body>
</html>
