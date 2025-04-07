<div class="container mt-5">
    <h2 class="text-center">Event Calendar - 2025</h2>

    <!-- Month Selection Form -->
    <form method="GET" class="mb-4 text-center">
        <label for="month">Select Month:</label>
        <select name="month" id="month" class="form-control d-inline-block w-auto">
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

    <div class="calendar-container">
        <?php
        $conn = mysqli_connect("localhost", "root", "", "your_database"); // Replace with your DB details

        // Fetch holidays, birthdays, and leave data
        $malaysiaHolidays = [
            '2025-01-01' => ['New Year\'s Day 🎆', 'holiday'],
            '2025-05-01' => ['Labour Day 💼', 'holiday'],
            '2025-08-31' => ['Merdeka Day 🎆', 'holiday'],
            '2025-12-25' => ['Christmas Day 🎄', 'holiday']
        ];

        $birthdays = [];
        $birthdayQuery = mysqli_query($conn, "SELECT FirstName, Dob FROM tblemployees;");
        while ($row = mysqli_fetch_array($birthdayQuery)) {
            $dob = new DateTime($row['Dob']);
            $dob->setDate(2025, $dob->format('m'), $dob->format('d'));
            $birthdays[$dob->format('Y-m-d')] = [$row['FirstName'] . "'s Birthday 🎂", 'birthday'];
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
                $leaveDates[$fromDate->format('Y-m-d')] = [$row['FirstName'] . "'s Leave 🌊", 'leave'];
                $fromDate->modify('+1 day');
            }
        }

        $calendarEvents = array_merge_recursive($malaysiaHolidays, $birthdays, $leaveDates);

        function draw_calendar($month, $year, $events) {
            $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            $firstDay = mktime(0, 0, 0, $month, 1, $year);
            $numDays = date('t', $firstDay);
            $dayOfWeek = date('w', $firstDay);

            $calendar = "<table class='table table-bordered'><thead><tr><th colspan='7'>$month $year</th></tr></thead>";
            $calendar .= "<tr>";
            foreach ($daysOfWeek as $day) {
                $calendar .= "<th class='text-center'>$day</th>";
            }
            $calendar .= "</tr><tr>";

            if ($dayOfWeek > 0) {
                $calendar .= str_repeat("<td></td>", $dayOfWeek);
            }

            $currentDay = 1;
            while ($currentDay <= $numDays) {
                if ($dayOfWeek == 7) {
                    $calendar .= "</tr><tr>";
                    $dayOfWeek = 0;
                }

                $dateKey = sprintf('%04d-%02d-%02d', $year, $month, $currentDay);
                $eventData = $events[$dateKey] ?? null;
                $eventClass = '';
                $tooltip = '';

                if ($eventData) {
                    foreach ($eventData as $event) {
                        $tooltip .= $event[0] . " | ";
                        if ($event[1] === 'holiday') $eventClass = 'bg-danger text-white';
                        elseif ($event[1] === 'leave') $eventClass = 'bg-primary text-white';
                        elseif ($event[1] === 'birthday') $eventClass = 'bg-warning text-dark';
                    }
                    $tooltip = rtrim($tooltip, " | ");
                }

                $calendar .= "<td class='text-center $eventClass' data-bs-toggle='tooltip' title='$tooltip'>$currentDay</td>";

                $currentDay++;
                $dayOfWeek++;
            }

            if ($dayOfWeek != 7) {
                $calendar .= str_repeat("<td></td>", 7 - $dayOfWeek);
            }

            $calendar .= "</tr></table>";
            return $calendar;
        }

        $selectedMonth = isset($_GET['month']) ? intval($_GET['month']) : date('n');
        echo draw_calendar($selectedMonth, 2025, $calendarEvents);
        ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltips.map(function (el) {
        return new bootstrap.Tooltip(el);
    });
});
</script>
