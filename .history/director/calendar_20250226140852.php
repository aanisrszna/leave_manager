<?php
// Database connection (Assuming $conn is your connection variable)

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
    '2025-06-08' => ['Hari Raya Aidiladha 🐪', 'holiday'],
    '2025-06-27' => ['Awal Muharram 🕋', 'holiday'],
    '2025-08-31' => ['Merdeka Day 🎆', 'holiday'],
    '2025-09-05' => ['Maulidur Rasul 🕌', 'holiday'],
    '2025-09-16' => ['Malaysia Day 🎆', 'holiday'],
    '2025-10-20' => ['Deepavali 🪔', 'holiday'],
    '2025-12-25' => ['Christmas Day 🎄', 'holiday'],
];

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
    $calendar .= "<div class='calendar-header'>";
    foreach ($daysOfWeek as $index => $day) {
        $weekendClass = ($index == 0 || $index == 6) ? "weekend" : "";
        $calendar .= "<div class='calendar-cell-header $weekendClass'>$day</div>";
    }
    $calendar .= "</div>";


    $calendar .= "<div class='calendar-row'>";
    if ($dayOfWeek > 0) {
        $calendar .= str_repeat("<div class='calendar-cell empty'></div>", $dayOfWeek);
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

        $calendar .= "<div class='calendar-cell $eventClass weekday-$dayOfWeek' data-event='$eventName'>";
        $calendar .= $currentDay;
        $calendar .= "</div>";

        $currentDay++;
        $dayOfWeek++;
    }

    if ($dayOfWeek != 7) {
        $calendar .= str_repeat("<div class='calendar-cell empty'></div>", 7 - $dayOfWeek);
    }

    $calendar .= "</div></div>";

    return $calendar;
}
?>

<!-- Month Selection Form -->
<div class="card-box height-100-p d-flex flex-column justify-content-center align-items-center">
    <div class="text-center">
        <form method="GET" class="d-flex align-items-center justify-content-center">
            <button type="submit" name="month" value="<?= $previousMonth ?>" class="btn btn-outline-primary mx-2">&lt;</button>
            <h5 class="mb-0"><?= date("F", mktime(0, 0, 0, $currentMonth, 1, $currentYear)) ?> <?= $currentYear ?></h5>
            <button type="submit" name="month" value="<?= $nextMonth ?>" class="btn btn-outline-primary mx-2">&gt;</button>
        </form>
    </div>    
    <?= draw_calendar($currentMonth, $currentYear, $calendarEvents); ?>
</div>

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

    
    /* Colors for days */
    .weekday-0 { background-color: #f8d7da; } /* Sunday */
    .weekday-1 { background-color: #d1ecf1; } /* Monday */
    .weekday-2 { background-color: #d4edda; } /* Tuesday */
    .weekday-3 { background-color: #fff3cd; } /* Wednesday */
    .weekday-4 { background-color: #cce5ff; } /* Thursday */
    .weekday-5 { background-color: #d6d8db; } /* Friday */
    .weekday-6 { background-color: #f0f0f0; } /* Saturday */

    .calendar-cell:hover::after {
        content: attr(data-event);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.75);
        color: white;
        padding: 5px;
        border-radius: 5px;
        font-size: 12px;
    }

    .calendar-header {
        display: flex;
        text-align: center;
        font-weight: bold;
        background-color: #b0b0b0; /* Grey background for all headers */
        color: white; /* White text for better contrast */
    }
    .calendar-cell-header {
        flex: 1;
        padding: 10px;
        border-bottom: 2px solid #ddd;
    }
    .weekend {
        background-color: #d0d0d0 !important; /* Lighter grey for weekends */
        color: white;
    }
    .calendar-container { display: flex; flex-direction: column; align-items: center; }
    .calendar-row { display: flex; }
    .calendar-cell { 
        padding: 15px; 
        width: 50px; 
        height: 50px; 
        text-align: center; 
        border: 1px solid #ddd; 
    }
</style>
