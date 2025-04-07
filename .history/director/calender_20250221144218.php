<?php
// Database connection
include('../includes/db_connection.php');

// List of Malaysian Public Holidays for 2025
$malaysiaHolidays = [
    '2025-01-01' => ['New Year\'s Day 🎆', 'holiday'],
    '2025-01-29' => ['Chinese New Year 🧧', 'holiday'],
    '2025-01-30' => ['Chinese New Year 🧧', 'holiday'],
    '2025-02-01' => ['Federal Territory Day 🌏', 'holiday'],
    '2025-02-11' => ['Thaipusm', 'holiday'],
    '2025-03-18' => ['Nuzul Al-Quran 🌙', 'holiday'],
    '2025-03-31' => ['Hari Raya Aidilfitri ✨', 'holiday'],
    '2025-04-01' => ['Hari Raya Aidilfitri ✨', 'holiday'],
    '2025-05-01' => ['Labour Day 💼', 'holiday'],
    '2025-05-12' => ['Wesak Day', 'holiday'],
    '2025-06-02' => ['Agong\'s Birthday 🥳', 'holiday'],
    '2025-06-07' => ['Hari Raya Aidiladha 🐪', 'holiday'],
    '2025-06-08' => ['Hari Raya Aidiladha 🐪', 'holiday'],
    '2025-06-27' => ['Awal Muharram', 'holiday'],
    '2025-08-31' => ['Merdeka Day', 'holiday'],
    '2025-09-05' => ['Maulidur Rasul', 'holiday'],
    '2025-09-16' => ['Malaysia Day 🎆', 'holiday'],
    '2025-10-20' => ['Deepavali', 'holiday'],
    '2025-12-25' => ['Christmas Day 🎄', 'holiday'],
];

// Fetch employee birthdays
$birthdayQuery = mysqli_query($conn, "SELECT FirstName, Dob FROM tblemployees;") or die(mysqli_error($conn));

$birthdays = [];
while ($row = mysqli_fetch_array($birthdayQuery)) {
    $dob = $row['Dob'];
    $firstname = $row['FirstName'];

    // Adjust the date to the current year (2025)
    $dobDate = new DateTime($dob);
    $dobDate->setDate(2025, $dobDate->format('m'), $dobDate->format('d'));

    $formattedDate = $dobDate->format('Y-m-d');
    $birthdays[$formattedDate] = [$firstname . "'s Birthday 🎂", 'birthday'];
}

// Fetch leave data and map to calendar events
$leaveQuery = mysqli_query($conn, "
    SELECT tblleave.FromDate, tblleave.ToDate, tblemployees.FirstName, tblleave.RegRemarks 
    FROM tblleave 
    INNER JOIN tblemployees ON tblleave.empid = tblemployees.emp_id
") or die(mysqli_error($conn));

// Leave mapping
$leaves = [];
while ($row = mysqli_fetch_array($leaveQuery)) {
    $fromDate = $row['FromDate'];
    $toDate = $row['ToDate'];
    $firstname = $row['FirstName'];
    $status = $row['RegRemarks'] == 1 ? "✅ Approved" : "❌ Pending";

    $leaves[$fromDate] = [$firstname . " on leave " . $status, 'leave'];
}

// Merge all events
$events = array_merge($malaysiaHolidays, $birthdays, $leaves);

// Get selected month
$selectedMonth = isset($_GET['month']) ? (int) $_GET['month'] : date('m');
$currentYear = 2025;

// Generate calendar for the selected month
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $selectedMonth, $currentYear);
$firstDayOfMonth = new DateTime("$currentYear-$selectedMonth-01");
?>

<!-- Month Selection Form -->
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

<!-- Calendar Table -->
<table class="table table-bordered text-center">
    <thead>
        <tr>
            <th>Sun</th>
            <th>Mon</th>
            <th>Tue</th>
            <th>Wed</th>
            <th>Thu</th>
            <th>Fri</th>
            <th>Sat</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $dayCounter = 1;
        $weekDay = (int) $firstDayOfMonth->format('w');
        
        echo "<tr>";
        for ($i = 0; $i < $weekDay; $i++) {
            echo "<td></td>";
        }

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = "$currentYear-$selectedMonth-".str_pad($day, 2, '0', STR_PAD_LEFT);
            $event = isset($events[$currentDate]) ? $events[$currentDate] : null;

            echo "<td>";
            echo "<strong>$day</strong>";
            if ($event) {
                echo "<br><span class='" . $event[1] . "'>" . $event[0] . "</span>";
            }
            echo "</td>";

            if (($weekDay + $day) % 7 == 0) {
                echo "</tr><tr>";
            }
        }

        while (($weekDay + $dayCounter) % 7 != 0) {
            echo "<td></td>";
            $dayCounter++;
        }
        echo "</tr>";
        ?>
    </tbody>
</table>
