<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "", "your_database");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Define public holidays
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

// Fetch birthdays
$birthdays = [];
$birthdayQuery = mysqli_query($conn, "SELECT FirstName, Dob FROM tblemployees;");
while ($row = mysqli_fetch_array($birthdayQuery)) {
    $dob = new DateTime($row['Dob']);
    $dob->setDate(2025, $dob->format('m'), $dob->format('d'));
    $birthdays[$dob->format('Y-m-d')] = [$row['FirstName'] . "'s Birthday 🎂", 'birthday'];
}

// Fetch leave records
$leaveDates = [];
$leaveQuery = mysqli_query($conn, "
    SELECT tblleave.FromDate, tblleave.ToDate, tblemployees.FirstName 
    FROM tblleave 
    INNER JOIN tblemployees ON tblleave.empid = tblemployees.emp_id
");
while ($row = mysqli_fetch_array($leaveQuery)) {
    $fromDate = new DateTime($row['FromDate']);
    $toDate = new DateTime($row['ToDate']);
    while ($fromDate <= $toDate) {
        $leaveDates[$fromDate->format('Y-m-d')] = [$row['FirstName'] . "'s Leave 🌊", 'leave'];
        $fromDate->modify('+1 day');
    }
}

// Merge all events
$calendarEvents = array_merge_recursive($malaysiaHolidays, $birthdays, $leaveDates);

function draw_calendar($month, $year, $events) {
    $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    $firstDay = mktime(0, 0, 0, $month, 1, $year);
    $numDays = date('t', $firstDay);
    $dayOfWeek = date('w', $firstDay);

    $calendar = "<table class='table table-bordered'><thead><tr><th colspan='7'>" . date('F', $firstDay) . " $year</th></tr></thead>";
    $calendar .= "<tr>";
    foreach ($daysOfWeek as $day) {
        $calendar .= "<th class='text-center'>$day</th>";
    }
    $calendar .= "</tr><tr>";

    if ($dayOfWeek > 0) {
        $calendar .= str_repeat("<td></td>", $dayOfWeek);
    }

    $currentDay = 1;
    while ($currentDay <= $numDays) {
        if ($dayOfWeek == 7) {
            $calendar .= "</tr><tr>";
            $dayOfWeek = 0;
        }

        $dateKey = sprintf('%04d-%02d-%02d', $year, $month, $currentDay);
        $eventData = $events[$dateKey] ?? [];
        $eventClass = '';
        $tooltip = '';

        if (!empty($eventData)) {
            $eventClasses = [];
            $tooltipArray = [];

            foreach ($eventData as $event) {
                $tooltipArray[] = $event[0];

                if ($event[1] === 'holiday') {
                    $eventClasses[] = 'bg-danger text-white';
                } elseif ($event[1] === 'leave') {
                    $eventClasses[] = 'bg-primary text-white';
                } elseif ($event[1] === 'birthday') {
                    $eventClasses[] = 'bg-warning text-dark';
                }
            }

            $eventClass = implode(' ', array_unique($eventClasses));
            $tooltip = implode(" | ", $tooltipArray);
        }

        $calendar .= "<td class='text-center $eventClass' data-bs-toggle='tooltip' title='$tooltip'>$currentDay</td>";

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
?>

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
            for ($i = 1; $i <= 12; $i++) {
                $selected = ($selectedMonth == $i) ? 'selected' : '';
                echo "<option value='$i' $selected>" . date('F', mktime(0, 0, 0, $i, 1)) . "</option>";
            }
            ?>
        </select>
        <button type="submit" class="btn btn-primary">Show Calendar</button>
    </form>

    <div class="calendar-container">
        <?php echo draw_calendar($selectedMonth, 2025, $calendarEvents); ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltips.map(function (el) {
        return new bootstrap.Tooltip(el);
    });
});
</script>
</body>
</html>
