<?php

// Get the selected month (default to current month)
$currentMonth = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$currentYear = 2025;
$previousMonth = $currentMonth - 1 < 1 ? 12 : $currentMonth - 1;
$nextMonth = $currentMonth + 1 > 12 ? 1 : $currentMonth + 1;

// List of Malaysian Public Holidays for 2025
$malaysiaHolidays = [...]; // Keep holiday array as is

// Fetch Employee Birthdays
$birthdays = [];
$birthdayQuery = mysqli_query($conn, "SELECT FirstName, Dob FROM tblemployees;");
while ($row = mysqli_fetch_array($birthdayQuery)) {
    $dob = new DateTime($row['Dob']);
    $dob->setDate(2025, $dob->format('m'), $dob->format('d'));
    $formattedDate = $dob->format('Y-m-d');
    $birthdays[$formattedDate] = [$row['FirstName'] . "🎂", 'birthday'];
}

// Fetch Employee Leaves
$leaveDates = [];
$leaveQuery = mysqli_query($conn, "...");
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

// Merge All Events
$calendarEvents = array_merge_recursive($malaysiaHolidays, $birthdays, $leaveDates);

// Function to Draw the Calendar
function draw_calendar($month, $year, $events) {
    $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
    $numberOfDays = date('t', $firstDayOfMonth);
    $dateComponents = getdate($firstDayOfMonth);
    $dayOfWeek = $dateComponents['wday'];

    $calendar = "<div class='calendar-container'>";
    $calendar .= "<div class='calendar-row'>";
    foreach ($daysOfWeek as $index => $day) {
        $dayClass = ($index == 0 || $index == 6) ? 'weekend' : 'weekday';
        $calendar .= "<div class='calendar-cell fw-bold $dayClass'>$day</div>";
    }
    $calendar .= "</div>";

    $calendar .= "<div class='calendar-row'>";
    if ($dayOfWeek > 0) {
        $calendar .= str_repeat("<div class='calendar-cell'></div>", $dayOfWeek);
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

        $dayClass = ($dayOfWeek == 0 || $dayOfWeek == 6) ? 'weekend' : 'weekday';
        $calendar .= "<div class='calendar-cell $eventClass $dayClass' data-event='$eventName'>";
        $calendar .= $currentDay;
        $calendar .= "</div>";

        $currentDay++;
        $dayOfWeek++;
    }

    if ($dayOfWeek != 7) {
        $calendar .= str_repeat("<div class='calendar-cell'></div>", 7 - $dayOfWeek);
    }

    $calendar .= "</div></div>";

    return $calendar;
}
?>

<!-- CSS Styling -->
<style>
    h5 {
        font-size: 1.2rem;
        font-weight: bold;
        text-align: center;
        margin-bottom: 10px;
    }
    .calendar-container {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .calendar-row {
        display: flex;
    }
    .calendar-cell {
        position: relative;
        padding: 15px;
        cursor: pointer;
        display: inline-block;
        width: 50px;
        height: 50px;
        text-align: center;
        vertical-align: middle;
        border: 1px solid #ddd;
        margin: 2px;
    }
    .calendar-cell.weekday {
        background-color: #e3f2fd; /* Light blue */
    }
    .calendar-cell.weekend {
        background-color: #f2f2f2; /* Light grey */
    }
    .calendar-cell:hover::after {
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
