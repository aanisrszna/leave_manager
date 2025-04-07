<?php


// Get selected month
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
    SELECT tblleave.FromDate, tblleave.ToDate, tblemployees.FirstName 
    FROM tblleave 
    INNER JOIN tblemployees ON tblleave.empid = tblemployees.emp_id
");
while ($row = mysqli_fetch_array($leaveQuery)) {
    $fromDate = new DateTime($row['FromDate']);
    $toDate = new DateTime($row['ToDate']);
    while ($fromDate <= $toDate) {
        $formattedDate = $fromDate->format('Y-m-d');
        $leaveDates[$formattedDate] = [$row['FirstName'] . "🌊", 'leave'];
        $fromDate->modify('+1 day');
    }
}

// Merge all events
$calendarEvents = array_merge_recursive($malaysiaHolidays, $birthdays, $leaveDates);

// Function to generate the calendar
function draw_calendar($month, $year, $events) {
    $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
    $numberOfDays = date('t', $firstDayOfMonth);
    $dayOfWeek = date('w', $firstDayOfMonth);

    $calendar = "<div class='calendar-container'>";
    $calendar .= "<div class='calendar-row'>";
    foreach ($daysOfWeek as $day) {
        $calendar .= "<div class='calendar-cell header'>$day</div>";
    }
    $calendar .= "</div>";

    if ($dayOfWeek > 0) {
        $calendar .= "<div class='calendar-row'>" . str_repeat("<div class='calendar-cell empty'></div>", $dayOfWeek);
    }

    for ($currentDay = 1; $currentDay <= $numberOfDays; $currentDay++, $dayOfWeek++) {
        if ($dayOfWeek == 7) {
            $calendar .= "</div><div class='calendar-row'>";
            $dayOfWeek = 0;
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

        $calendar .= "<div class='calendar-cell $eventClass' data-event='$eventName'>$currentDay</div>";
    }

    if ($dayOfWeek != 7) {
        $calendar .= str_repeat("<div class='calendar-cell empty'></div>", 7 - $dayOfWeek);
    }

    $calendar .= "</div></div>";
    return $calendar;
}
?>

<style>
    .calendar-container {
        text-align: center;
    }

    .calendar-row {
        display: flex;
        justify-content: center;
    }

    .calendar-cell {
        position: relative;
        padding: 10px;
        width: 50px;
        height: 50px;
        text-align: center;
        border: 1px solid #ddd;
        margin: 2px;
        cursor: pointer;
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

<!-- Month Selector -->
<form method="GET" class="d-flex justify-content-center align-items-center mb-3">
    <button type="submit" name="month" value="<?= $previousMonth ?>" class="btn btn-outline-primary mx-2">&lt;</button>
    <select name="month" class="form-control w-auto mx-2" onchange="this.form.submit()">
        <?php foreach ($malaysiaHolidays as $num => $name) {
            echo "<option value='$num' " . ($currentMonth == $num ? 'selected' : '') . ">$name</option>";
        } ?>
    </select>
    <button type="submit" name="month" value="<?= $nextMonth ?>" class="btn btn-outline-primary mx-2">&gt;</button>
</form>

<!-- Render the Calendar -->
<?= draw_calendar($currentMonth, 2025, $calendarEvents); ?>
