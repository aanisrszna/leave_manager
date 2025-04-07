<?php
include "database_connection.php"; // Ensure database connection is included
include "calendar_functions.php";  // Put draw_calendar() function in a separate file if needed

$month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Recalculate events
$calendarEvents = $malaysiaHolidays;

// Merge Birthdays and Leaves Again
foreach ($birthdays as $date => $event) {
    if (isset($calendarEvents[$date])) {
        $calendarEvents[$date][0] .= "," . $event[0];
    } else {
        $calendarEvents[$date] = $event;
    }
}

foreach ($leaveDates as $date => $event) {
    if (isset($calendarEvents[$date])) {
        $calendarEvents[$date][0] .= "," . $event[0];
    } else {
        $calendarEvents[$date] = $event;
    }
}

echo draw_calendar($month, $year, $calendarEvents);
?>
