<?php


$currentMonth = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$currentYear = 2025;

$malaysiaHolidays = [
    '2025-01-01' => ['New Year\'s Day 🎆', 'holiday'],
    '2025-05-01' => ['Labour Day 💼', 'holiday'],
    '2025-08-31' => ['Merdeka Day 🎆', 'holiday'],
    '2025-12-25' => ['Christmas Day 🎄', 'holiday'],
];

$birthdays = [];
$birthdayQuery = mysqli_query($conn, "SELECT FirstName, Dob FROM tblemployees;");
while ($row = mysqli_fetch_array($birthdayQuery)) {
    $dob = new DateTime($row['Dob']);
    $dob->setDate(2025, $dob->format('m'), $dob->format('d'));
    $birthdays[$dob->format('Y-m-d')] = [$row['FirstName'] . "🎂", 'birthday'];
}

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
        $leaveDates[$fromDate->format('Y-m-d')] = [$row['FirstName'] . "🌊", 'leave'];
        $fromDate->modify('+1 day');
    }
}

$calendarEvents = array_merge_recursive($malaysiaHolidays, $birthdays, $leaveDates);

echo draw_calendar($currentMonth, $currentYear, $calendarEvents);

function draw_calendar($month, $year, $events) {
    $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
    $numberOfDays = date('t', $firstDayOfMonth);
    $dayOfWeek = date('w', $firstDayOfMonth);

    $calendar = "<div class='calendar-container'><div class='calendar-row'>";
    foreach ($daysOfWeek as $day) {
        $calendar .= "<div class='calendar-cell fw-bold'>$day</div>";
    }
    $calendar .= "</div><div class='calendar-row'>" . str_repeat("<div class='calendar-cell'></div>", $dayOfWeek);

    for ($currentDay = 1; $currentDay <= $numberOfDays; $currentDay++, $dayOfWeek++) {
        $date = sprintf('%04d-%02d-%02d', $year, $month, $currentDay);
        $eventName = $events[$date][0] ?? "";
        $calendar .= "<div class='calendar-cell' data-event='$eventName'>$currentDay</div>";
    }

    return $calendar . "</div></div>";
}
?>
