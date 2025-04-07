<?php
// Database connection

$currentMonth = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$previousMonth = $currentMonth - 1 < 1 ? 12 : $currentMonth - 1;
$nextMonth = $currentMonth + 1 > 12 ? 1 : $currentMonth + 1;

// List of Malaysian Public Holidays for 2025
$malaysiaHolidays = [
    '2025-01-01' => ['New Year\'s Day 🎆', 'holiday'],
    '2025-01-29' => ['Chinese New Year 🧧', 'holiday'],
    '2025-01-30' => ['Chinese New Year 🧧', 'holiday'],
    '2025-02-01' => ['Federal Territory Day 🌏', 'holiday'],
    '2025-02-11' => ['Thaipusam 🪔', 'holiday'],
    '2025-03-18' => ['Nuzul Al-Quran 🌙', 'holiday'],
    '2025-03-31' => ['Hari Raya Aidilfitri ✨', 'holiday'],
    '2025-04-01' => ['Hari Raya Aidilfitri ✨', 'holiday'],
    '2025-05-01' => ['Labour Day 💼', 'holiday'],
    '2025-05-12' => ['Wesak Day', 'holiday'],
    '2025-06-02' => ['Agong\'s Birthday 🥳', 'holiday'],
    '2025-06-07' => ['Hari Raya Aidiladha 🐪', 'holiday'],
    '2025-06-08' => ['Hari Raya Aidiladha 🐪', 'holiday'],
    '2025-06-27' => ['Awal Muharram🕋', 'holiday'],
    '2025-08-31' => ['Merdeka Day🎆', 'holiday'],
    '2025-09-05' => ['Maulidur Rasul🕌', 'holiday'],
    '2025-09-16' => ['Malaysia Day 🎆', 'holiday'],
    '2025-10-20' => ['Deepavali🪔', 'holiday'],
    '2025-12-25' => ['Christmas Day 🎄', 'holiday'],
];

// Fetch employee birthdays
$birthdays = [];
$birthdayQuery = mysqli_query($conn, "SELECT FirstName, Dob FROM tblemployees;");
while ($row = mysqli_fetch_array($birthdayQuery)) {
    $dob = new DateTime($row['Dob']);
    $dob->setDate(2025, $dob->format('m'), $dob->format('d'));
    $formattedDate = $dob->format('Y-m-d');
    $birthdays[$formattedDate] = [$row['FirstName'] . "🎂", 'birthday'];
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
            $leaveDates[$formattedDate] = [$row['FirstName'] . "🌊", 'leave'];
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
    $dayOfWeek = $dateComponents['wday'];

    $calendar = "<div class='calendar-row'>";
    foreach ($daysOfWeek as $day) {
        $calendar .= "<div class='calendar-cell fw-bold'>$day</div>";
    }
    $calendar .= "</div>";

    if ($dayOfWeek > 0) {
        $calendar .= "<div class='calendar-row'>" . str_repeat("<div class='calendar-cell'></div>", $dayOfWeek);
    }

    $currentDay = 1;
    while ($currentDay <= $numberOfDays) {
        if ($dayOfWeek == 7) {
            $dayOfWeek = 0;
            $calendar .= "</div><div class='calendar-row'>";
        }

        $currentDate = sprintf('%04d-%02d-%02d', $year, $month, $currentDay);
        $eventData = $events[$currentDate] ?? null;
        $eventName = $eventData[0] ?? "";
        $eventType = $eventData[1] ?? "";

        $eventClass = match ($eventType) {
            'holiday' => 'bg-danger text-white',
            'birthday' => 'bg-warning text-dark',
            'leave' => 'bg-primary text-white',
            default => ''
        };

        $calendar .= "<div class='calendar-cell $eventClass' data-event='$eventName'>";
        $calendar .= $currentDay;
        $calendar .= "</div>";

        $currentDay++;
        $dayOfWeek++;
    }

    if ($dayOfWeek != 7) {
        $calendar .= str_repeat("<div class='calendar-cell'></div>", 7 - $dayOfWeek);
    }

    $calendar .= "</div>";

    return $calendar;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Calendar</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include Bootstrap if needed -->
</head>
<body>

<form class="mb-4 d-flex align-items-center justify-content-center" onsubmit="return false;">
    <button type="button" onclick="changeMonth(<?= $previousMonth ?>)" class="btn btn-outline-primary mx-2">&lt;</button>

    <select id="month" class="form-control mx-2" style="width: auto; display: inline-block;" onchange="changeMonth(this.value)">
        <?php
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];
        foreach ($months as $num => $name) {
            $selected = ($currentMonth == $num) ? 'selected' : '';
            echo "<option value='$num' $selected>$name</option>";
        }
        ?>
    </select>

    <h3 class="mb-0 mx-2">2025</h3>

    <button type="button" onclick="changeMonth(<?= $nextMonth ?>)" class="btn btn-outline-primary mx-2">&gt;</button>
</form>

<div id="calendarContainer">
    <?= draw_calendar($currentMonth, 2025, $calendarEvents); ?>
</div>

<script>
function changeMonth(month) {
    let xhr = new XMLHttpRequest();
    xhr.open("GET", "calendar.php?month=" + month, true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            document.getElementById("calendarContainer").innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}
</script>

</body>
</html>
