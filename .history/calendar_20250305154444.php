<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "", "your_database_name"); // Change as needed

// Get the selected month (default to current month)
$currentMonth = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$currentYear = 2025;
$previousMonth = $currentMonth - 1 < 1 ? 12 : $currentMonth - 1;
$nextMonth = $currentMonth + 1 > 12 ? 1 : $currentMonth + 1;

// List of Malaysian Public Holidays for 2025
$malaysiaHolidays = [
    '2025-01-01' => [['name' => 'New Year\'s Day 🎆', 'type' => 'holiday']],
    '2025-01-29' => [['name' => 'Chinese New Year 🧧', 'type' => 'holiday']],
    '2025-01-30' => [['name' => 'Chinese New Year 🧧', 'type' => 'holiday']],
    '2025-02-01' => [['name' => 'Federal Territory Day 🌏', 'type' => 'holiday']],
    '2025-02-11' => [['name' => 'Thaipusam 🪔', 'type' => 'holiday']],
    '2025-03-18' => [['name' => 'Nuzul Al-Quran 🌙', 'type' => 'holiday']],
    '2025-03-31' => [['name' => 'Hari Raya Aidilfitri ✨', 'type' => 'holiday']],
    '2025-04-01' => [['name' => 'Hari Raya Aidilfitri ✨', 'type' => 'holiday']],
    '2025-05-01' => [['name' => 'Labour Day 💼', 'type' => 'holiday']],
    '2025-05-12' => [['name' => 'Wesak Day', 'type' => 'holiday']],
    '2025-06-02' => [['name' => 'Agong\'s Birthday 🥳', 'type' => 'holiday']],
    '2025-06-07' => [['name' => 'Hari Raya Aidiladha 🐪', 'type' => 'holiday']],
    '2025-06-08' => [['name' => 'Hari Raya Aidiladha 🐪', 'type' => 'holiday']],
    '2025-06-27' => [['name' => 'Awal Muharram 🕋', 'type' => 'holiday']],
    '2025-08-31' => [['name' => 'Merdeka Day 🎆', 'type' => 'holiday']],
    '2025-09-05' => [['name' => 'Maulidur Rasul 🕌', 'type' => 'holiday']],
    '2025-09-16' => [['name' => 'Malaysia Day 🎆', 'type' => 'holiday']],
    '2025-10-20' => [['name' => 'Deepavali 🪔', 'type' => 'holiday']],
    '2025-12-25' => [['name' => 'Christmas Day 🎄', 'type' => 'holiday']],
];

// Fetch Employee Birthdays
$birthdays = [];
$birthdayQuery = mysqli_query($conn, "SELECT FirstName, Dob FROM tblemployees;");
while ($row = mysqli_fetch_array($birthdayQuery)) {
    $dob = new DateTime($row['Dob']);
    $dob->setDate(2025, $dob->format('m'), $dob->format('d'));
    $formattedDate = $dob->format('Y-m-d');
    $birthdays[$formattedDate][] = ['name' => $row['FirstName'] . " 🎂", 'type' => 'birthday'];
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
            $leaveDates[$formattedDate][] = ['name' => $row['FirstName'] . " 🌊", 'type' => 'leave'];
            $fromDate->modify('+1 day');
        }
    }
}

// Merge All Events
$calendarEvents = array_merge_recursive($malaysiaHolidays, $birthdays, $leaveDates);

// Function to Draw the Calendar
function draw_calendar($month, $year, $events) {
    $daysOfWeek = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];
    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
    $numberOfDays = date('t', $firstDayOfMonth);
    $dateComponents = getdate($firstDayOfMonth);
    $dayOfWeek = $dateComponents['wday']; // 0 (Sunday) to 6 (Saturday)

    $calendar = "<div class='calendar-container'>";
    $calendar .= "<div class='calendar-row'>";
    
    foreach ($daysOfWeek as $day) {
        $calendar .= "<div class='calendar-cell fw-bold'>$day</div>";
    }

    $calendar .= "</div><div class='calendar-row'>";

    for ($i = 0; $i < $dayOfWeek; $i++) {
        $calendar .= "<div class='calendar-cell'></div>";
    }

    for ($day = 1; $day <= $numberOfDays; $day++) {
        $currentDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
        $eventData = $events[$currentDate] ?? [];
        $eventText = "";
        $eventClass = "";

        foreach ($eventData as $event) {
            $eventText .= $event['name'] . "<br>";
            $eventClass = match ($event['type']) {
                'holiday' => 'bg-danger text-white',
                'birthday' => 'bg-warning text-dark',
                'leave' => 'bg-primary text-white',
                default => ''
            };
        }

        $calendar .= "<div class='calendar-cell $eventClass' data-event='$eventText'>";
        $calendar .= "$day <br><small>$eventText</small>";
        $calendar .= "</div>";

        if (($day + $dayOfWeek) % 7 == 0 && $day != $numberOfDays) {
            $calendar .= "</div><div class='calendar-row'>";
        }
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
    <br>    
    <?= draw_calendar($currentMonth, $currentYear, $calendarEvents); ?>
</div>

<!-- CSS Styling -->
<style>
    .calendar-container { width: 100%; }
    .calendar-row { display: flex; width: 100%; }
    .calendar-cell {
        flex: 1;
        padding: 15px;
        text-align: center;
        border: 1px solid #ddd;
        cursor: pointer;
    }
    .calendar-cell:hover::after {
        content: attr(data-event);
        position: absolute;
        background: black;
        color: white;
        padding: 5px;
        border-radius: 5px;
    }
</style>
