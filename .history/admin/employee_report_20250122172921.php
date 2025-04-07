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
$pdf->Cell(0, 10, 'Employee Leave Information Report', 0, 1, 'C');

// Set body font
$pdf->SetFont('helvetica', '', 10);

// Query to get employee data (FirstName)
$sql = "SELECT DISTINCT tblemployees.emp_id, tblemployees.FirstName
        FROM employee_leave
        JOIN tblemployees ON employee_leave.emp_id = tblemployees.emp_id";
$query = $dbh->prepare($sql);
$query->execute();
$employees = $query->fetchAll(PDO::FETCH_OBJ);

// Loop through each employee and display the leave details
foreach ($employees as $employee) {
    // Add a page break if necessary
    if ($pdf->GetY() > 250) {
        $pdf->AddPage();
    }
    
    // Add employee name as the section header
    $pdf->Ln(10); // Line break
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Leave Information for ' . htmlentities($employee->FirstName), 0, 1, 'L');
    
    // Add table header
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(90, 7, 'Leave Type', 1, 0, 'C');
    $pdf->Cell(40, 7, 'Available Days', 1, 1, 'C');

    // Fetch leave details for the employee
    $sql_leaves = "SELECT tblleavetype.LeaveType, employee_leave.available_day
                   FROM employee_leave
                   JOIN tblleavetype ON employee_leave.leave_type_id = tblleavetype.id
                   WHERE employee_leave.emp_id = :emp_id";
    $query_leaves = $dbh->prepare($sql_leaves);
    $query_leaves->bindParam(':emp_id', $employee->emp_id, PDO::PARAM_INT);
    $query_leaves->execute();
    $leave_details = $query_leaves->fetchAll(PDO::FETCH_OBJ);

    // Display leave details in the table
    $pdf->SetFont('helvetica', '', 10);
    foreach ($leave_details as $leave) {
        // Check space before adding a new row
        if ($pdf->GetY() > 260) { // Adjust height limit based on layout
            $pdf->AddPage();
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(90, 7, 'Leave Type', 1, 0, 'C');
            $pdf->Cell(40, 7, 'Available Days', 1, 1, 'C');
            $pdf->SetFont('helvetica', '', 10);
        }

        $pdf->Cell(90, 7, htmlentities($leave->LeaveType), 1, 0, 'L');
        $pdf->Cell(40, 7, htmlentities($leave->available_day), 1, 1, 'C');
    }
}

// Output the PDF to the browser (force download)
$pdf->Output('employee_leave_report.pdf', 'I');
?>
