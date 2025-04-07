<?php
// Include session and config files
include('../includes/session.php');
include('../includes/config.php');
require_once('../TCPDF-main/tcpdf.php');

// List of Malaysian Public Holidays for 2025
$malaysiaHolidays = [
    '2025-01-01' => 'New Year\'s Day',
    '2025-01-29' => 'Chinese New Year',
    '2025-01-30' => 'Chinese New Year',
    '2025-02-01' => 'Federal Territory Day',
    '2025-02-11' => 'Thaipusm',
    '2025-03-18' => 'Nuzul Al-Quran',
    '2025-03-31' => 'Hari Raya Aidilfitri',
    '2025-04-01' => 'Hari Raya Aidilfitri',
    '2025-05-01' => 'Labour Day',
    '2025-05-12' => 'Wesak Day',
    '2025-06-02' => 'Agong\'s Birthday',
    '2025-06-07' => 'Hari Raya Aidiladha',
    '2025-06-08' => 'Hari Raya Aidiladha',
    '2025-06-27' => 'Awal Muharram',
    '2025-08-31' => 'Merdeka Day',
    '2025-09-05' => 'Maulidur Rasul',
    '2025-09-16' => 'Malaysia Day',
    '2025-10-20' => 'Deepavali',
    '2025-12-25' => 'Christmas Day',
];

// Fetch employee birthdays
$birthdayQuery = mysqli_query($conn, "SELECT FirstName, Dob FROM tblemployees;") or die(mysqli_error($conn));
$birthdays = [];
while ($row = mysqli_fetch_array($birthdayQuery)) {
    $dob = $row['Dob'];
    $firstname = $row['FirstName'];
    $dobDate = new DateTime($dob);
    $dobDate->setDate(2025, $dobDate->format('m'), $dobDate->format('d'));
    $formattedDate = $dobDate->format('Y-m-d');
    $birthdays[$formattedDate] = $firstname . "'s Birthday";
}

// Fetch leave data
$leaveQuery = mysqli_query($conn, "SELECT tblleave.FromDate, tblleave.ToDate, tblemployees.FirstName, tblleave.RegRemarks FROM tblleave INNER JOIN tblemployees ON tblleave.empid = tblemployees.emp_id") or die(mysqli_error($conn));
$leaveDates = [];
while ($row = mysqli_fetch_array($leaveQuery)) {
    if ($row['RegRemarks'] == 1) {
        $fromDate = new DateTime($row['FromDate']);
        $toDate = new DateTime($row['ToDate']);
        $firstname = $row['FirstName'];

        while ($fromDate <= $toDate) {
            $formattedDate = $fromDate->format('Y-m-d');
            if (isset($leaveDates[$formattedDate])) {
                $leaveDates[$formattedDate] .= ', ' . $firstname . "'s Leave";
            } else {
                $leaveDates[$formattedDate] = $firstname . "'s Leave";
            }
            $fromDate->modify('+1 day');
        }
    }
}

// Merge all events (holidays, birthdays, leaves)
$calendarEvents = array_merge($malaysiaHolidays, $birthdays, $leaveDates);

// Generate PDF if "Generate Report" button is clicked
if (isset($_POST['generate_report'])) {
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false); // Set page size to A4
    $pdf->SetFont('helvetica', '', 10); // Set font size to 10
    $pdf->AddPage();

    // Set title
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'Calendar Report - By Date', 0, 1, 'C');
    $pdf->Ln(10);

    // Create table headers
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(40, 10, 'Date', 1, 0, 'C');
    $pdf->Cell(150, 10, 'Event', 1, 1, 'C');

    // Set regular font for event details
    $pdf->SetFont('helvetica', '', 10);

    // Sort events by date
    ksort($calendarEvents);

    // List all events by date
    foreach ($calendarEvents as $date => $event) {
        $pdf->Cell(40, 10, $date, 1, 0, 'C');
        $pdf->Cell(150, 10, $event, 1, 1, 'L');
    }

    // Output PDF
    $pdf->Output('calendar_report_by_date.pdf', 'I');
}
?>

<!-- Calendar Section -->
<form method="GET" class="mb-4">
    <label for="month">Select Month:</label>
    <select name="month" id="month" class="form-control" style="width: auto; display: inline-block;">
        <option value="1" <?= isset($_GET['month']) && $_GET['month'] == 1 ? 'selected' : ''; ?>>January</option>
        <option value="2" <?= isset($_GET['month']) && $_GET['month'] == 2 ? 'selected' : ''; ?>>February</option>
        <option value="3" <?= isset($_GET['month']) && $_GET['month'] == 3 ? 'selected' : ''; ?>>March</option>
        <option value="4" <?= isset($_GET['month']) && $_GET['month'] == 4 ? 'selected' : ''; ?>>April</option>
        <option value="5" <?= isset($_GET['month']) && $_GET['month'] == 5 ? 'selected' : ''; ?>>May</option>
        <option value="6" <?= isset($_GET['month']) && $_GET['month'] == 6 ? 'selected' : ''; ?>>June</option>
        <option value="7" <?= isset($_GET['month']) && $_GET['month'] == 7 ? 'selected' : ''; ?>>July</option>
        <option value="8" <?= isset($_GET['month']) && $_GET['month'] == 8 ? 'selected' : ''; ?>>August</option>
        <option value="9" <?= isset($_GET['month']) && $_GET['month'] == 9 ? 'selected' : ''; ?>>September</option>
        <option value="10" <?= isset($_GET['month']) && $_GET['month'] == 10 ? 'selected' : ''; ?>>October</option>
        <option value="11" <?= isset($_GET['month']) && $_GET['month'] == 11 ? 'selected' : ''; ?>>November</option>
        <option value="12" <?= isset($_GET['month']) && $_GET['month'] == 12 ? 'selected' : ''; ?>>December</option>
    </select>
    <button type="submit" class="btn btn-primary">Show Calendar</button>
    <button type="submit" name="generate_report" class="btn btn-success">Generate Calendar Report (PDF)</button>
</form>
