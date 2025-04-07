<?php
include('../includes/session.php');
include('../includes/config.php');
require_once('../TCPDF-main/tcpdf.php');

// Define the TCPDF class
class MYPDF extends TCPDF {
    // Page header
    public function Header() {
        $this->SetFont('helvetica', 'B', 16);
        $this->Cell(0, 10, 'Calendar Report', 0, 1, 'C');
    }

    // Page footer
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}

// Fetch the data again from your existing query
$malaysiaHolidays = [
    '2025-01-01' => ['New Year\'s Day 🎆', 'holiday'],
    '2025-01-29' => ['Chinese New Year 🧧', 'holiday'],
    '2025-01-30' => ['Chinese New Year 🧧', 'holiday'],
    '2025-02-01' => ['Federal Territory Day 🌏', 'holiday'],
    '2025-02-11' => ['Thaipusm', 'holiday'],
    // Add all other holidays
];

$birthdays = [];
$birthdayQuery = mysqli_query($conn, "
    SELECT FirstName, Dob 
    FROM tblemployees;
") or die(mysqli_error($conn));

while ($row = mysqli_fetch_array($birthdayQuery)) {
    $dob = $row['Dob'];
    $firstname = $row['FirstName'];
    $dobDate = new DateTime($dob);
    $dobDate->setDate(2025, $dobDate->format('m'), $dobDate->format('d'));
    $formattedDate = $dobDate->format('Y-m-d');
    $birthdays[$formattedDate] = [$firstname . "'s Birthday 🎂", 'birthday'];
}

$leaveDates = [];
$leaveQuery = mysqli_query($conn, "
    SELECT tblleave.FromDate, tblleave.ToDate, tblemployees.FirstName, tblleave.RegRemarks 
    FROM tblleave 
    INNER JOIN tblemployees ON tblleave.empid = tblemployees.emp_id
") or die(mysqli_error($conn));

while ($row = mysqli_fetch_array($leaveQuery)) {
    if ($row['RegRemarks'] == 1) {
        $fromDate = new DateTime($row['FromDate']);
        $toDate = new DateTime($row['ToDate']);
        $firstname = $row['FirstName'];

        while ($fromDate <= $toDate) {
            $formattedDate = $fromDate->format('Y-m-d');
            if (isset($leaveDates[$formattedDate])) {
                $leaveDates[$formattedDate][0] .= '<br> ' . $firstname . "'s Leave 🌊";
            } else {
                $leaveDates[$formattedDate] = [$firstname . "'s Leave 🌊", 'leave'];
            }
            $fromDate->modify('+1 day');
        }
    }
}

// Merge all events (holidays, birthdays, leaves)
$calendarEvents = array_merge_recursive($malaysiaHolidays, $birthdays, $leaveDates);

// Initialize TCPDF
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Calendar Report');
$pdf->SetMargins(15, 27, 15);
$pdf->SetAutoPageBreak(TRUE, 25);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);

// Get selected month (you can adjust this based on your form input)
$selectedMonth = isset($_GET['month']) ? intval($_GET['month']) : date('n');

// Call the function to generate the calendar in HTML
$html = draw_calendar($selectedMonth, 2025, $calendarEvents);

// Write the calendar HTML to the PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Output the PDF
$pdf->Output('calendar_report.pdf', 'I');
?>
