<?php
// -----------------------------
// Calendar (Birthdays/Leaves + PH)
// -----------------------------
// Assumes: $conn is a valid mysqli connection

// Get selected month/year (defaults to current)
$currentMonth = isset($_GET['month']) ? max(1, min(12, intval($_GET['month']))) : intval(date('n'));
$currentYear  = isset($_GET['year'])  ? intval($_GET['year']) : intval(date('Y'));

// Compute prev/next month + year safely
$previousMonth = ($currentMonth === 1) ? 12 : $currentMonth - 1;
$previousYear  = ($currentMonth === 1) ? $currentYear - 1 : $currentYear;

$nextMonth = ($currentMonth === 12) ? 1 : $currentMonth + 1;
$nextYear  = ($currentMonth === 12) ? $currentYear + 1 : $currentYear;

// --- Holidays per year ---
function getMalaysiaHolidays(int $year): array {
    if ($year === 2025) {
        // Your original 2025 list (unchanged)
        return [
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
            '2025-09-01' => ['Replacement Merdeka Day🎆', 'holiday'],
            '2025-09-05' => ['Maulidur Rasul 🕌', 'holiday'],
            '2025-09-15' => ['Additional PH Malaysia 🎆', 'holiday'],
            '2025-09-16' => ['Malaysia Day 🎆', 'holiday'],
            '2025-10-20' => ['Deepavali 🪔', 'holiday'],
            '2025-12-25' => ['Christmas Day 🎄', 'holiday'],
        ];
    }

    if ($year === 2026) {
        // ✅ Kuala Lumpur + National only (from your list)
        // NOTE: Where multiple holidays fall on the same date, we combine names into one tooltip.
        return [
            '2026-01-01' => ['New Year\'s Day 🎆', 'holiday'],

            // 1 Feb (KL has both Thaipusam + FT Day)
            '2026-02-01' => ['Thaipusam 🪔, Federal Territory Day 🌏', 'holiday'],
            // 2 Feb (KL has both FT Day Holiday + Thaipusam Holiday)
            '2026-02-02' => ['Federal Territory Day Holiday 🌏, Thaipusam Holiday 🪔', 'holiday'],

            '2026-02-17' => ['Chinese New Year 🧧', 'holiday'],
            '2026-02-18' => ['Chinese New Year Holiday 🧧', 'holiday'],

            '2026-03-07' => ['Nuzul Al-Quran 🌙', 'holiday'], // KL applies
            '2026-03-21' => ['Hari Raya Aidilfitri ✨', 'holiday'],
            '2026-03-22' => ['Hari Raya Aidilfitri Holiday ✨', 'holiday'],
            '2026-03-23' => ['Hari Raya Aidilfitri Holiday ✨', 'holiday'], // National except Kedah → KL applies

            '2026-05-01' => ['Labour Day 💼', 'holiday'],
            '2026-05-31' => ['Wesak Day 🪷', 'holiday'],

            // 1 Jun has Agong’s Birthday + Wesak Holiday (KL applies)
            '2026-06-01' => ['Agong\'s Birthday 👑, Wesak Day Holiday 🪷', 'holiday'],

            '2026-06-17' => ['Awal Muharram 🕋', 'holiday'],
            '2026-08-25' => ['Prophet Muhammad\'s Birthday 🕌', 'holiday'],
            '2026-08-31' => ['Merdeka Day 🎆', 'holiday'],
            '2026-09-16' => ['Malaysia Day 🎆', 'holiday'],

            '2026-11-08' => ['Deepavali 🪔', 'holiday'], // KL applies
            '2026-11-09' => ['Deepavali Holiday 🪔', 'holiday'], // KL applies

            '2026-12-25' => ['Christmas Day 🎄', 'holiday'],
        ];
    }

    // Default: none
    return [];
}

$malaysiaHolidays = getMalaysiaHolidays($currentYear);

// --- Birthdays (pin to selected $currentYear) ---
// --- Birthdays (pin to selected $currentYear) ---
// Exclude employees whose Status is 'Inactive' (case-insensitive)
$birthdays = [];
$birthdayQuery = mysqli_query(
    $conn,
    "SELECT FirstName, LastName, Dob
     FROM tblemployees
     WHERE COALESCE(LOWER(Status), '') <> 'inactive'"
);

if ($birthdayQuery) {
    while ($row = mysqli_fetch_array($birthdayQuery)) {
        $dob = new DateTime($row['Dob']);
        $dob->setDate($currentYear, (int)$dob->format('m'), (int)$dob->format('d'));
        $formattedDate = $dob->format('Y-m-d');

        if (isset($birthdays[$formattedDate])) {
            $birthdays[$formattedDate][0] .= ", " . $row['LastName'] . " 🎂";
        } else {
            $birthdays[$formattedDate] = [$row['LastName'] . " 🎂", 'birthday'];
        }
    }
}


// --- Leaves (approved only if RegRemarks==1) ---
$leaveDates = [];
$leaveQuery = mysqli_query($conn, "
    SELECT tblleave.FromDate, tblleave.ToDate, tblemployees.FirstName, tblemployees.LastName, tblleave.RegRemarks, tblleavetype.LeaveType 
    FROM tblleave 
    INNER JOIN tblemployees ON tblleave.empid = tblemployees.emp_id
    INNER JOIN tblleavetype ON tblleave.LeaveType = tblleavetype.LeaveType
    WHERE tblleavetype.id NOT IN (5, 6, 7, 8)
");
if ($leaveQuery) {
    while ($row = mysqli_fetch_array($leaveQuery)) {
        if ((int)$row['RegRemarks'] === 1) {
            $fromDate = new DateTime($row['FromDate']);
            $toDate   = new DateTime($row['ToDate']);
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
}

// --- Merge events ---
$calendarEvents = $malaysiaHolidays;
foreach ($birthdays as $date => $event) {
    if (isset($calendarEvents[$date])) {
        $calendarEvents[$date][0] .= ", " . $event[0];
    } else {
        $calendarEvents[$date] = $event;
    }
}
foreach ($leaveDates as $date => $event) {
    if (isset($calendarEvents[$date])) {
        $calendarEvents[$date][0] .= ", " . $event[0];
    } else {
        $calendarEvents[$date] = $event;
    }
}

// --- Draw calendar (6 rows grid + safe tooltip) ---
function draw_calendar($month, $year, $events) {
    $daysOfWeek = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];
    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
    $numberOfDays = (int)date('t', $firstDayOfMonth);
    $dayOfWeek = (int)date('w', $firstDayOfMonth);

    // 6x7 grid ensures consistent layout
    $totalCells = 42;
    $calendarDays = array_fill(0, $totalCells, null);
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
        $eventData   = $currentDate && isset($events[$currentDate]) ? $events[$currentDate] : null;

        // Tooltip text (escape quotes)
        $eventName = '';
        if (is_array($eventData) && isset($eventData[0])) {
            $eventName = htmlspecialchars($eventData[0], ENT_QUOTES, 'UTF-8');
        }

        $eventType = is_array($eventData) && isset($eventData[1]) ? $eventData[1] : '';
        $eventClass = match ($eventType) {
            'holiday'  => 'bg-danger text-white',
            'birthday' => 'bg-warning text-dark',
            'leave'    => 'bg-success text-white',
            default    => ''
        };

        $isToday = ($currentDate === $currentDateToday);
        if ($isToday) {
            $eventClass = 'bg-secondary text-white fw-bold';
            $eventName  = 'Today 📅';
        }

        $calendar .= "<div class='calendar-cell $eventClass' data-event='$eventName'>";
        $calendar .= $day ? $day : "";
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
        <!-- Left arrow (prev month/year) -->
        <form method="GET" class="d-inline-block">
            <input type="hidden" name="year" value="<?= htmlspecialchars($previousYear) ?>">
            <button type="submit" name="month" value="<?= $previousMonth ?>" class="btn btn-outline-primary mx-2">&lt;</button>
        </form>

        <h5 class="mb-0 d-inline-block">
            <?= date("F", mktime(0, 0, 0, $currentMonth, 1, $currentYear)) ?> <?= $currentYear ?>
        </h5>

        <!-- Right arrow (next month/year) -->
        <form method="GET" class="d-inline-block">
            <input type="hidden" name="year" value="<?= htmlspecialchars($nextYear) ?>">
            <button type="submit" name="month" value="<?= $nextMonth ?>" class="btn btn-outline-primary mx-2">&gt;</button>
        </form>

        <!-- Quick selectors -->
        <form method="GET" class="d-flex align-items-center justify-content-center mt-2">
            <select name="month" class="form-select w-auto mx-2" onchange="this.form.submit()">
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m ?>" <?= $m == $currentMonth ? 'selected' : '' ?>>
                        <?= date('F', mktime(0, 0, 0, $m, 1, $currentYear)) ?>
                    </option>
                <?php endfor; ?>
            </select>
            <select name="year" class="form-select w-auto mx-2" onchange="this.form.submit()">
                <?php for ($y = $currentYear - 2; $y <= $currentYear + 2; $y++): ?>
                    <option value="<?= $y ?>" <?= $y == $currentYear ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
            <noscript><button class="btn btn-primary">Go</button></noscript>
        </form>
    </div>

    <!-- Legend -->
    <div class="legend-container mt-3">
        <div class="legend-item"><span class="legend-box bg-danger"></span> Holiday</div>
        <div class="legend-item"><span class="legend-box bg-warning"></span> Birthday</div>
        <div class="legend-item"><span class="legend-box bg-success"></span> Leave</div>
        <div class="legend-item"><span class="legend-box bg-secondary"></span> Today</div>
    </div>
    <br>

    <!-- Render the calendar grid -->
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
        cursor: pointer;
        text-align: center;
        vertical-align: middle;
        border: 1px solid #ddd;
        margin: 2px;
        min-height: 52px;
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
        z-index: 5;
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
    /* Basic Bootstrap-like helpers (if not using Bootstrap) */
    .btn { padding: 6px 10px; border: 1px solid #0d6efd; background: transparent; border-radius: 4px; }
    .btn:hover { background: #0d6efd; color: #fff; }
    .btn-outline-primary { color: #0d6efd; }
    .form-select { padding: 6px 10px; }
    .d-inline-block { display: inline-block; }
    .d-flex { display: flex; }
    .align-items-center { align-items: center; }
    .justify-content-center { justify-content: center; }
    .mx-2 { margin-left: 0.5rem; margin-right: 0.5rem; }
    .mt-2 { margin-top: 0.5rem; }
    .mt-3 { margin-top: 1rem; }
    .mb-0 { margin-bottom: 0; }
    .bg-danger { background: #dc3545 !important; }
    .bg-warning { background: #ffc107 !important; }
    .bg-success { background: #198754 !important; }
    .bg-secondary { background: #6c757d !important; }
    .text-white { color: #fff !important; }
    .text-dark { color: #212529 !important; }
    .fw-bold { font-weight: 700 !important; }
</style>
