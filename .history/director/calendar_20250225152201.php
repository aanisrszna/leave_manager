<style>
    .calendar-cell {
        position: relative;
        padding: 15px;
        cursor: pointer;
        display: inline-block;
        width: 100px;
        height: 60px;
        text-align: center;
        vertical-align: middle;
        border: 1px solid #ddd;
        margin: 2px;
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

    .calendar-row {
        display: flex;
        justify-content: center;
    }

    .calendar-container {
        text-align: center;
    }
</style>

<!-- Calendar Section -->
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
            $selected = (isset($_GET['month']) && $_GET['month'] == $num) ? 'selected' : '';
            echo "<option value='$num' $selected>$name</option>";
        }
        ?>
    </select>
    <button type="submit" class="btn btn-primary">Show Calendar</button>
</form>

<!-- Calendar Display -->
<div class="calendar-container">
    <?php

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
                $leaveDates[$formattedDate] = [$row['FirstName'] . "'s Leave 🌊", 'leave'];
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

        $calendar = "<div><h3>" . date('F Y', $firstDayOfMonth) . "</h3></div>";
        $calendar .= "<div class='calendar-row'>";
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

    $selectedMonth = isset($_GET['month']) ? intval($_GET['month']) : date('n');
    echo draw_calendar($selectedMonth, 2025, $calendarEvents);
    ?>
</div>
