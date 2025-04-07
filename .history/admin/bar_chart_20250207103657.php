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

// Query to get employee data (FirstName)
$sql = "SELECT DISTINCT tblemployees.emp_id, tblemployees.FirstName
        FROM tblleave
        JOIN tblemployees ON tblleave.empid = tblemployees.emp_id";
$query = $dbh->prepare($sql);
$query->execute();
$employees = $query->fetchAll(PDO::FETCH_OBJ);

// Initial position for the bar chart
$chartX = 20;
$chartY = 40;
$barWidth = 50;
$barSpacing = 10;
$maxBarHeight = 100;

// Loop through each employee and fetch requested leave details
foreach ($employees as $employee) {
    $pdf->Ln(10); // Line break
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Leave Information for ' . htmlentities($employee->FirstName), 0, 1, 'L');

    // Fetch and sum requested leave days for the employee
    $sql_leaves = "SELECT tblleavetype.LeaveType, SUM(tblleave.RequestedDays) AS total_requested_days
                   FROM tblleave
                   JOIN tblleavetype ON tblleave.LeaveType = tblleavetype.LeaveType
                   WHERE tblleave.empid = :emp_id
                   GROUP BY tblleavetype.LeaveType";
    $query_leaves = $dbh->prepare($sql_leaves);
    $query_leaves->bindParam(':emp_id', $employee->emp_id, PDO::PARAM_INT);
    $query_leaves->execute();
    $leave_details = $query_leaves->fetchAll(PDO::FETCH_OBJ);

    // Calculate chart positions and scaling
    $pdf->SetFont('helvetica', '', 10);
    $currentX = $chartX;
    $maxDays = 0;

    // Find the maximum requested days to scale bars properly
    foreach ($leave_details as $leave) {
        if ($leave->total_requested_days > $maxDays) {
            $maxDays = $leave->total_requested_days;
        }
    }

    // Draw bars for each leave type
    foreach ($leave_details as $leave) {
        $barHeight = ($maxDays > 0) ? ($leave->total_requested_days / $maxDays) * $maxBarHeight : 0;
        $barHeight = max($barHeight, 5); // Ensure minimum visibility
        
        // Draw the bar
        $pdf->SetFillColor(100, 150, 255); // Bar color
        $pdf->Rect($currentX, $chartY + ($maxBarHeight - $barHeight), $barWidth, $barHeight, 'F');

        // Display leave type below the bar
        $pdf->SetXY($currentX, $chartY + $maxBarHeight + 5);
        $pdf->MultiCell($barWidth, 5, htmlentities($leave->LeaveType), 0, 'C');

        // Display requested days above the bar
        $pdf->SetXY($currentX, $chartY + ($maxBarHeight - $barHeight) - 5);
        $pdf->Cell($barWidth, 5, htmlentities($leave->total_requested_days), 0, 0, 'C');

        // Move to the next bar position
        $currentX += $barWidth + $barSpacing;
    }

    // Add a page break after each employee's details if the content doesn't fit on the current page
    if ($pdf->GetY() > 250) {
        $pdf->AddPage();
    }
}

// Output the PDF to the browser (force download)
$pdf->Output('employee_leave_report_bar_chart.pdf', 'I');
?>