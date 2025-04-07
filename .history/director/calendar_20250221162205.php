<?php
// List of Malaysian Public Holidays for 2025
$malaysiaHolidays = [
    '2025-01-01' => ['New Year\'s Day 🎆', 'holiday'],
    '2025-01-29' => ['Chinese New Year 🧧', 'holiday'],
    '2025-01-30' => ['Chinese New Year 🧧', 'holiday'],
    '2025-02-01' => ['Federal Territory Day 🌏', 'holiday'],
    '2025-05-01' => ['Labour Day 💼', 'holiday'],
    '2025-12-25' => ['Christmas Day 🎄', 'holiday'],
];

// Sample Birthdays
$birthdays = [
    '2025-01-05' => ['Alice\'s Birthday 🎂', 'birthday'],
    '2025-01-29' => ['Bob\'s Birthday 🎂', 'birthday'],
];

// Sample Leaves
$leaveDates = [
    '2025-01-10' => ['Charlie\'s Leave 🌊', 'leave'],
    '2025-01-29' => ['David\'s Leave 🌊', 'leave'],
];

// Merge all events into one array
$calendarEvents = array_merge_recursive($malaysiaHolidays, $birthdays, $leaveDates);

function draw_calendar($month, $year, $events) {
    $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
    $numberOfDays = date('t', $firstDayOfMonth);
    $dateComponents = getdate($firstDayOfMonth);
    $monthName = $dateComponents['month'];
    $dayOfWeek = $dateComponents['wday'];

    $calendar = "<table class='calendar-table'>";
    $calendar .= "<thead><tr><th colspan='7'>$monthName $year</th></tr></thead>";
    $calendar .= "<tr>";
    
    foreach ($daysOfWeek as $day) {
        $calendar .= "<th>$day</th>";
    }
    $calendar .= "</tr><tr>";

    if ($dayOfWeek > 0) {
        $calendar .= str_repeat("<td class='empty'></td>", $dayOfWeek);
    }

    $currentDay = 1;
    while ($currentDay <= $numberOfDays) {
        if ($dayOfWeek == 7) {
            $dayOfWeek = 0;
            $calendar .= "</tr><tr>";
        }

        $currentDate = sprintf('%04d-%02d-%02d', $year, $month, $currentDay);
        $eventData = $events[$currentDate] ?? [];
        $eventDetails = '';

        if (!empty($eventData)) {
            foreach ($eventData as $event) {
                $eventDetails .= "<div>$event</div>";
            }
        }

        $calendar .= "<td class='calendar-day tooltip'>";
        $calendar .= "<div class='date-number'>$currentDay</div>";
        if ($eventDetails) {
            $calendar .= "<div class='tooltiptext'>$eventDetails</div>";
        }
        $calendar .= "</td>";

        $currentDay++;
        $dayOfWeek++;
    }

    if ($dayOfWeek != 7) {
        $calendar .= str_repeat("<td class='empty'></td>", 7 - $dayOfWeek);
    }

    $calendar .= "</tr></table>";

    return $calendar;
}

// Get selected month or default to current month
$selectedMonth = isset($_GET['month']) ? intval($_GET['month']) : date('n');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Calendar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .calendar-table {
            width: 100%;
            border-collapse: collapse;
        }
        .calendar-table th, .calendar-table td {
            border: 1px solid #ddd;
            width: 14.28%;
            height: 80px;
            text-align: center;
            vertical-align: middle;
            position: relative;
        }
        .calendar-day {
            position: relative;
            cursor: pointer;
            background-color: #f9f9f9;
        }
        .calendar-day:hover {
            background-color: #ececec;
        }
        .date-number {
            font-weight: bold;
        }
        .tooltip .tooltiptext {
            visibility: hidden;
            background-color: rgba(0, 0, 0, 0.8);
            color: #fff;
            text-align: center;
            padding: 8px;
            border-radius: 5px;
            position: absolute;
            z-index: 10;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
            min-width: 150px;
        }
        .tooltip:hover .tooltiptext {
            visibility: visible;
        }
        .empty {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <form method="GET" class="mb-4">
            <label for="month">Select Month:</label>
            <select name="month" id="month" class="form-control" style="width: auto; display: inline-block;">
                <?php
                $months = [
                    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                ];
                foreach ($months as $num => $name) {
                    $selected = ($selectedMonth == $num) ? 'selected' : '';
                    echo "<option value='$num' $selected>$name</option>";
                }
                ?>
            </select>
            <button type="submit" class="btn btn-primary">Show Calendar</button>
        </form>

        <div class="calendar-container">
            <?php echo draw_calendar($selectedMonth, 2025, $calendarEvents); ?>
        </div>
    </div>
</body>
</html>
