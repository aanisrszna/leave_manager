<?php
// Connect to the database

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
$leaveQuery = mysqli_query($conn, "SELECT tblleave.FromDate, tblleave.ToDate, tblemployees.FirstName FROM tblleave INNER JOIN tblemployees ON tblleave.empid = tblemployees.emp_id");
while ($row = mysqli_fetch_array($leaveQuery)) {
    $fromDate = new DateTime($row['FromDate']);
    $toDate = new DateTime($row['ToDate']);
    while ($fromDate <= $toDate) {
        $formattedDate = $fromDate->format('Y-m-d');
        $leaveDates[$formattedDate] = [$row['FirstName'] . "'s Leave 🌊", 'leave'];
        $fromDate->modify('+1 day');
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

    $calendar = "<div class='calendar'><h3>$monthName $year</h3><table class='table table-bordered'>";
    $calendar .= "<thead><tr>";
    foreach ($daysOfWeek as $day) {
        $calendar .= "<th class='text-center'>$day</th>";
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
        $eventClass = '';
        $eventLabel = '';

        if ($eventData) {
            foreach ($eventData as $event) {
                list($eventName, $eventType) = $event;
                if ($eventType === 'holiday') $eventClass = 'bg-danger text-white';
                elseif ($eventType === 'birthday') $eventClass = 'bg-warning text-dark';
                elseif ($eventType === 'leave') $eventClass = 'bg-primary text-white';
                $eventLabel .= "<br><small>$eventName</small>";
            }
        }

        $calendar .= "<td class='text-center $eventClass'><strong>$currentDay</strong>$eventLabel</td>";
        $currentDay++;
        $dayOfWeek++;
    }

    if ($dayOfWeek != 7) {
        $calendar .= str_repeat("<td></td>", 7 - $dayOfWeek);
    }

    $calendar .= "</tr></tbody></table></div>";
    return $calendar;
}

$selectedMonth = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$selectedYear = 2025;
echo draw_calendar($selectedMonth, $selectedYear, $calendarEvents);
?>

<style>
    .calendar { max-width: 800px; margin: auto; padding: 20px; }
    .table { background-color: #fff; }
    .table th, .table td { text-align: center; vertical-align: middle; }
    .bg-danger { background-color: #dc3545 !important; }
    .bg-warning { background-color: #ffc107 !important; }
    .bg-primary { background-color: #007bff !important; }
</style>