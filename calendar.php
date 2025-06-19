<?php
// Database connection (make sure $conn is defined before this)

// Get the selected month (default to current month)
$currentMonth = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$currentYear = 2025;
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
    '2025-06-27' => ['Awal Muharram 🕋', 'holiday'],
    '2025-08-31' => ['Merdeka Day 🎆', 'holiday'],
    '2025-09-05' => ['Maulidur Rasul 🕌', 'holiday'],
    '2025-09-16' => ['Malaysia Day 🎆', 'holiday'],
    '2025-10-20' => ['Deepavali 🪔', 'holiday'],
    '2025-12-25' => ['Christmas Day 🎄', 'holiday'],
];

// Fetch Employee Birthdays
$birthdays = [];
$birthdayQuery = mysqli_query($conn, "SELECT FirstName,LastName, Dob FROM tblemployees;");
while ($row = mysqli_fetch_array($birthdayQuery)) {
    $dob = new DateTime($row['Dob']);
    $dob->setDate(2025, $dob->format('m'), $dob->format('d'));
    $formattedDate = $dob->format('Y-m-d');
    $birthdays[$formattedDate] = [$row['LastName'] . " 🎂", 'birthday'];
}

// Fetch Employee Leaves
$leaveDates = [];
$leaveQuery = mysqli_query($conn, "
    SELECT tblleave.FromDate, tblleave.ToDate, tblemployees.FirstName, tblemployees.LastName, tblleave.RegRemarks, tblleavetype.LeaveType 
    FROM tblleave 
    INNER JOIN tblemployees ON tblleave.empid = tblemployees.emp_id
    INNER JOIN tblleavetype ON tblleave.LeaveType = tblleavetype.LeaveType
    WHERE tblleavetype.id NOT IN (5, 6, 7, 8)
");

while ($row = mysqli_fetch_array($leaveQuery)) {
    if ($row['RegRemarks'] == 1) {
        $fromDate = new DateTime($row['FromDate']);
        $toDate = new DateTime($row['ToDate']);
        while ($fromDate <= $toDate) {
            $formattedDate = $fromDate->format('Y-m-d');
            if (isset($leaveDates[$formattedDate])) {
                $leaveDates[$formattedDate][0] .= ", " . $row['LastName'] . "🏖️";
            } else {
                $leaveDates[$formattedDate] = [$row['LastName'] . "🏖️", 'leave'];
            }
            $fromDate->modify('+1 day');
        }
    }
}

// Merge All Events
$calendarEvents = $malaysiaHolidays;

foreach ($birthdays as $date => $event) {
    if (isset($calendarEvents[$date])) {
        $calendarEvents[$date][0] .= "," . $event[0];
    } else {
        $calendarEvents[$date] = $event;
    }
}

foreach ($leaveDates as $date => $event) {
    if (isset($calendarEvents[$date])) {
        $calendarEvents[$date][0] .= "," . $event[0];
    } else {
        $calendarEvents[$date] = $event;
    }
}

// Function to Draw the Calendar
function draw_calendar($month, $year, $events) {
    $daysOfWeek = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];
    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
    $numberOfDays = date('t', $firstDayOfMonth);
    $dateComponents = getdate($firstDayOfMonth);
    $dayOfWeek = $dateComponents['wday'];

    $calendarDays = array_fill(0, 35, null);
    $startIndex = $dayOfWeek;

    for ($i = 0; $i < $numberOfDays; $i++) {
        $calendarDays[$startIndex + $i] = $i + 1;
    }

    $currentDateToday = date('Y-m-d');
    $calendar = "<div class='calendar-container'>";
    $calendar .= "<div class='calendar-row'>";

    foreach ($daysOfWeek as $day) {
        $calendar .= "<div class='calendar-cell fw-bold'>$day</div>";
    }

    $calendar .= "</div><div class='calendar-row'>";

    foreach ($calendarDays as $index => $day) {
        if ($index % 7 == 0 && $index != 0) {
            $calendar .= "</div><div class='calendar-row'>";
        }

        $currentDate = $day ? sprintf('%04d-%02d-%02d', $year, $month, $day) : null;
        $eventData = $events[$currentDate] ?? null;
        $eventLabels = "";

        if ($eventData) {
            $eventLabels .= "<div class='label label-{$eventData[1]}'>" . $eventData[0] . "</div>";
        }

        if ($currentDate === $currentDateToday) {
            $eventLabels .= "<div class='label label-today'>Today 📍</div>";
        }

        $calendar .= "<div class='calendar-cell'>";
        $calendar .= $day ? "<div class='day-number'>$day</div>$eventLabels" : "";
        $calendar .= "</div>";
    }

    $calendar .= "</div></div>";
    return $calendar;
}
?>

<!-- Calendar Display -->
<div class="card-box height-100-p d-flex flex-column justify-content-center align-items-center">
    <br>      
    <div class="text-center">
        <form method="GET" class="d-flex align-items-center justify-content-center">
            <button type="submit" name="month" value="<?= $previousMonth ?>" class="btn btn-outline-primary mx-2">&lt;</button>
            <h5 class="mb-0"><?= date("F", mktime(0, 0, 0, $currentMonth, 1, $currentYear)) ?> <?= $currentYear ?></h5>
            <button type="submit" name="month" value="<?= $nextMonth ?>" class="btn btn-outline-primary mx-2">&gt;</button>
        </form>
    </div>

    <div class="legend-container mt-3">
        <div class="legend-item"><span class="legend-box bg-danger"></span> Holiday</div>
        <div class="legend-item"><span class="legend-box bg-warning"></span> Birthday</div>
        <div class="legend-item"><span class="legend-box bg-success"></span> Leave</div>
        <div class="legend-item"><span class="legend-box bg-secondary"></span> Today</div>
    </div>
    <br> 
    <?= draw_calendar($currentMonth, $currentYear, $calendarEvents); ?>
</div>

<!-- CSS Styling -->
<style>
    h5 {
        font-size: 1rem;
        font-weight: bold;
        text-align: center;
        margin-bottom: 10px;
    }
    .calendar-container {
        width: 100%;
    }
    .calendar-row {
        display: flex;
        width: 100%;
    }
    .calendar-cell {
        position: relative;
        flex: 1;
        padding: 13px;
        min-height: 80px;
        border: 1px solid #ddd;
        text-align: center;
        vertical-align: top;
        margin: 2px;
        font-size: 14px;
    }
    .calendar-cell .day-number {
        font-weight: bold;
        font-size: 16px;
        margin-bottom: 5px;
    }
    .label {
        margin-top: 2px;
        padding: 2px 6px;
        font-size: 12px;
        display: inline-block;
        border-radius: 4px;
        color: white;
    }
    .label-holiday {
        background-color: #dc3545;
    }
    .label-birthday {
        background-color: #ffc107;
        color: black;
    }
    .label-leave {
        background-color: #28a745;
    }
    .label-today {
        background-color: #6c757d;
    }

    /* Legend Styling */
    .legend-container {
        text-align: center;
        margin-top: 10px;
    }
    .legend-item {
        display: inline-flex;
        align-items: center;
        margin: 5px;
        font-size: 14px;
    }
    .legend-box {
        width: 15px;
        height: 15px;
        display: inline-block;
        margin-right: 5px;
        border-radius: 3px;
    }
    .bg-danger { background-color: #dc3545; }
    .bg-warning { background-color: #ffc107; }
    .bg-success { background-color: #28a745; }
    .bg-secondary { background-color: #6c757d; }
</style>
