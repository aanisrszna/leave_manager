<style>
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
<form method="GET" class="mb-4 d-flex align-items-center justify-content-center">
    <?php
    $currentMonth = isset($_GET['month']) ? intval($_GET['month']) : date('n');
    $previousMonth = $currentMonth - 1 < 1 ? 12 : $currentMonth - 1;
    $nextMonth = $currentMonth + 1 > 12 ? 1 : $currentMonth + 1;
    ?>

    <!-- Left Arrow -->
    <button type="submit" name="month" value="<?= $previousMonth ?>" class="btn btn-outline-primary mx-2">&lt;</button>

    <!-- Month Selector -->
    <select name="month" id="month" class="form-control mx-2" style="width: auto; display: inline-block;" onchange="this.form.submit()">
        <?php
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];
        foreach ($months as $num => $name) {
            $selected = ($currentMonth == $num) ? 'selected' : '';
            echo "<option value='$num' $selected>$name</option>";
        }
        ?>
    </select>

    <h3 class="mb-0 mx-2">2025</h3>

    <!-- Right Arrow -->
    <button type="submit" name="month" value="<?= $nextMonth ?>" class="btn btn-outline-primary mx-2">&gt;</button>
</form>

<!-- Calendar Display -->
<div class="calendar-container">
    <?php
    function draw_calendar($month, $year, $events) {
        $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
        $numberOfDays = date('t', $firstDayOfMonth);
        $dateComponents = getdate($firstDayOfMonth);
        $dayOfWeek = $dateComponents['wday'];

        // Display weekdays header
        $calendar = "<div class='calendar-row'>";
        foreach ($daysOfWeek as $day) {
            $calendar .= "<div class='calendar-cell fw-bold'>$day</div>";
        }
        $calendar .= "</div><div class='calendar-row'>";

        // Offset empty days before the 1st of the month
        for ($i = 0; $i < $dayOfWeek; $i++) {
            $calendar .= "<div class='calendar-cell'></div>";
        }

        // Generate calendar days
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

            $calendar .= "<div class='calendar-cell $eventClass' data-event='$eventName'>$currentDay</div>";

            $currentDay++;
            $dayOfWeek++;
        }

        // Close remaining empty spaces in the last row
        while ($dayOfWeek < 7) {
            $calendar .= "<div class='calendar-cell'></div>";
            $dayOfWeek++;
        }

        return $calendar . "</div>";
    }

    echo draw_calendar($currentMonth, 2025, []);
    ?>
</div>
