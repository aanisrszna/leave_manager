<?php

// Malaysian Public Holidays 2025
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

// Fetch Employee Birthdays
$birthdays = [];
$birthdayQuery = mysqli_query($conn, "SELECT FirstName, Dob FROM tblemployees;");
while ($row = mysqli_fetch_array($birthdayQuery)) {
    $dob = new DateTime($row['Dob']);
    $dob->setDate(2025, $dob->format('m'), $dob->format('d'));
    $formattedDate = $dob->format('Y-m-d');
    $birthdays[$formattedDate] = [$row['FirstName'] . "'s Birthday 🎂", 'birthday'];
}

// Fetch Leave Data
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

// Merge All Events
$calendarEvents = array_merge_recursive($malaysiaHolidays, $birthdays, $leaveDates);

// Function to Generate Calendar
function draw_calendar($month, $year, $events) {
    $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
    $numberOfDays = date('t', $firstDayOfMonth);
    $dateComponents = getdate($firstDayOfMonth);
    $monthName = $dateComponents['month'];
    $dayOfWeek = $dateComponents['wday'];

    $calendar = "<div class='text-center mb-3'><h4 class='fw-bold'>$monthName $year</h4></div>";
    $calendar .= "<table class='table table-bordered text-center'>";
    $calendar .= "<thead class='bg-dark text-white'><tr>";

    foreach ($daysOfWeek as $day) {
        $calendar .= "<th>$day</th>";
    }

    $calendar .= "</tr></thead><tbody><tr>";

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

        $eventBadge = '';
        if ($eventType === 'holiday') $eventBadge = "<span class='badge bg-danger'>$eventName</span>";
        elseif ($eventType === 'birthday') $eventBadge = "<span class='badge bg-warning text-dark'>$eventName</span>";
        elseif ($eventType === 'leave') $eventBadge = "<span class='badge bg-primary'>$eventName</span>";

        $calendar .= "<td class='py-3'>";
        $calendar .= "<strong>$currentDay</strong><br>$eventBadge";
        $calendar .= "</td>";

        $currentDay++;
        $dayOfWeek++;
    }

    if ($dayOfWeek != 7) {
        $calendar .= str_repeat("<td></td>", 7 - $dayOfWeek);
    }

    $calendar .= "</tr></tbody></table>";
    return $calendar;
}

// Get Selected Month or Default to Current Month
$selectedMonth = isset($_GET['month']) ? intval($_GET['month']) : date('n');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employee Calendar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
    <!-- Calendar Section -->
    <div class="card shadow-sm p-3">
        <form method="GET" class="d-flex align-items-center gap-2">
            <label for="month" class="fw-bold">Select Month:</label>
            <select name="month" id="month" class="form-select w-auto">
                <?php
                foreach ($months as $num => $name) {
                    $selected = ($selectedMonth == $num) ? 'selected' : '';
                    echo "<option value='$num' $selected>$name</option>";
                }
                ?>
            </select>
            <button type="submit" class="btn btn-primary">Show Calendar</button>
        </form>
    </div>

    <!-- Calendar Display -->
    <div class="card mt-4 shadow-sm">
        <div class="card-body">
            <?php echo draw_calendar($selectedMonth, 2025, $calendarEvents); ?>
        </div>
    </div>
</div>

</body>
</html>
