<?php
// Include session and config files
include('../includes/session.php');
include('../includes/config.php');
require_once('../TCPDF-main/tcpdf.php');

// Create a new PDF instance
$pdf = new TCPDF();

// Set PDF metadata
$pdf->SetCreator('Your Company');
$pdf->SetTitle('Employee Leave Report');

// Add a page
$pdf->AddPage();

// Set header
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Employee Leave Information Report (Bar Chart)', 0, 1, 'C');

// Set body font
$pdf->SetFont('helvetica', '', 10);

// Query to get employees who have applied for leave
$sql = "SELECT tblemployees.emp_id, tblemployees.FirstName, SUM(tblleave.RequestedDays) AS total_requested_days
        FROM tblleave
        JOIN tblemployees ON tblleave.empid = tblemployees.emp_id
        GROUP BY tblemployees.emp_id, tblemployees.FirstName";
$query = $dbh->prepare($sql);
$query->execute();
$employees = $query->fetchAll(PDO::FETCH_OBJ);

// Initial position for the bar chart
$chartX = 20;
$chartY = 40;
$barWidth = 50;
$barSpacing = 10;
$maxBarHeight = 100;

// Find the maximum requested days to scale bars properly
$maxDays = 0;
foreach ($employees as $employee) {
    if ($employee->total_requested_days > $maxDays) {
        $maxDays = $employee->total_requested_days;
    }
}

// Draw bars for each employee
$currentX = $chartX;
foreach ($employees as $employee) {
    $barHeight = ($maxDays > 0) ? ($employee->total_requested_days / $maxDays) * $maxBarHeight : 0;
    $barHeight = max($barHeight, 5); // Ensure minimum visibility

    // Draw the bar
    $pdf->SetFillColor(100, 150, 255); // Bar color
    $pdf->Rect($currentX, $chartY + ($maxBarHeight - $barHeight), $barWidth, $barHeight, 'F');

    // Display employee name below the bar
    $pdf->SetXY($currentX, $chartY + $maxBarHeight + 5);
    $pdf->MultiCell($barWidth, 5, htmlentities($employee->FirstName), 0, 'C');

    // Display total requested days above the bar
    $pdf->SetXY($currentX, $chartY + ($maxBarHeight - $barHeight) - 5);
    $pdf->Cell($barWidth, 5, htmlentities($employee->total_requested_days), 0, 0, 'C');

    // Move to the next bar position
    $currentX += $barWidth + $barSpacing;
}

// Output the PDF to the browser (force download)
$pdf->Output('employee_leave_report_bar_chart.pdf', 'I');
?>
