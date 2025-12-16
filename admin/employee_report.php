<?php
// Include session and config files
include('../includes/session.php');
include('../includes/config.php');
require_once('../TCPDF-main/tcpdf.php');

class MYPDF extends TCPDF {
    private $fullname;
    private $staff_position;

    public function __construct($fullname, $staff_position) {
        parent::__construct();
        $this->fullname = $fullname;
        $this->staff_position = $staff_position;
    }

    public function Header() {
        $this->Image('../vendors/images/riverraven.png', 160, 10, 30);
   


        $this->Ln(5);

    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}

// Retrieve user details (this should be fetched from session or database)
$fullname = 'John Doe'; // Example, replace with dynamic data
$staff_position = 'Software Engineer'; // Example, replace with dynamic data

// Create a new PDF instance
$pdf = new MYPDF($fullname, $staff_position);

// Set PDF metadata
$pdf->SetCreator('Your Company');
$pdf->SetTitle('Employee Leave Report');

$currentYear = date('Y');
$statusApproved = 1;
// Add a page
$pdf->AddPage();

// Set header
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Employee Leave Information Report', 0, 1, 'L');
/* =========================
   SUMMARY OF LEAVE TAKEN
   ========================= */

$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', 13);
$pdf->Cell(0, 10, 'Summary of Employees Leave Taken (' . $currentYear . ')', 0, 1, 'L');
$pdf->Ln(2);

/* ---- Build dynamic leave buckets ---- */
$typeSql = "SELECT id, LeaveType FROM tblleavetype WHERE IsDisplay = 'Yes'";
$typeStmt = $dbh->prepare($typeSql);
$typeStmt->execute();
$typeRows = $typeStmt->fetchAll(PDO::FETCH_ASSOC);

$annualTypeIds = [];
$medicalTypeIds = [];

foreach ($typeRows as $t) {
    $name = strtolower($t['LeaveType']);
    if (preg_match('/medical|hospital/i', $name)) {
        $medicalTypeIds[] = (int)$t['id'];
    } else {
        $annualTypeIds[] = (int)$t['id'];
    }
}

/* ---- Fetch approved leave taken ---- */
$sql = "
SELECT 
    e.emp_id,
    e.FirstName,
    lt.id AS leave_type_id,
    COALESCE(SUM(l.RequestedDays), 0) AS days_taken
FROM tblemployees e
LEFT JOIN tblleave l 
    ON l.empid = e.emp_id
    AND l.RegRemarks = :status
    AND YEAR(l.FromDate) = :year
LEFT JOIN tblleavetype lt 
    ON l.LeaveType = lt.LeaveType
WHERE e.Status NOT IN ('Inactive', 'Offline')
GROUP BY e.emp_id, lt.id
ORDER BY e.FirstName
";

$q = $dbh->prepare($sql);
$q->bindParam(':status', $statusApproved, PDO::PARAM_INT);
$q->bindParam(':year', $currentYear, PDO::PARAM_INT);
$q->execute();
$rows = $q->fetchAll(PDO::FETCH_ASSOC);

/* ---- Aggregate per employee ---- */
$summary = [];

foreach ($rows as $r) {
    $name = $r['FirstName'];
    $type = (int)$r['leave_type_id'];
    $days = (float)$r['days_taken'];

    if (!isset($summary[$name])) {
        $summary[$name] = [
            'annual' => 0,
            'medical' => 0
        ];
    }

    if (in_array($type, $annualTypeIds, true)) {
        $summary[$name]['annual'] += $days;
    }

    if (in_array($type, $medicalTypeIds, true)) {
        $summary[$name]['medical'] += $days;
    }
}

/* ---- Table Header ---- */
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(60, 8, 'Employee', 1, 0, 'C');
$pdf->Cell(40, 8, 'Annual Leave (Days)', 1, 0, 'C');
$pdf->Cell(40, 8, 'Medical Leave (Days)', 1, 0, 'C');
$pdf->Cell(30, 8, 'Total', 1, 1, 'C');

/* ---- Table Body ---- */
$pdf->SetFont('helvetica', '', 10);

foreach ($summary as $name => $data) {
    $total = $data['annual'] + $data['medical'];

    $pdf->Cell(60, 8, $name, 1, 0, 'L');
    $pdf->Cell(40, 8, number_format($data['annual'], 1), 1, 0, 'C');
    $pdf->Cell(40, 8, number_format($data['medical'], 1), 1, 0, 'C');
    $pdf->Cell(30, 8, number_format($total, 1), 1, 1, 'C');
}

/* ---- Page break after summary ---- */
$pdf->AddPage();

// Set body font
$pdf->SetFont('helvetica', '', 10);

// Query to get employee data
$sql = "SELECT DISTINCT tblemployees.emp_id, tblemployees.FirstName FROM employee_leave JOIN tblemployees ON employee_leave.emp_id = tblemployees.emp_id";
$query = $dbh->prepare($sql);
$query->execute();
$employees = $query->fetchAll(PDO::FETCH_OBJ);

// Loop through each employee and display the leave details
foreach ($employees as $employee) {
    $pdf->Ln(10);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Leave Information for ' . htmlentities($employee->FirstName), 0, 1, 'L');
    
    // Add table header
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(90, 7, 'Leave Type', 1, 0, 'C');
    $pdf->Cell(40, 7, 'Available Days', 1, 1, 'C');

    // Fetch leave details for the employee
    $sql_leaves = "SELECT tblleavetype.LeaveType, employee_leave.available_day FROM employee_leave JOIN tblleavetype ON employee_leave.leave_type_id = tblleavetype.id WHERE employee_leave.emp_id = :emp_id";
    $query_leaves = $dbh->prepare($sql_leaves);
    $query_leaves->bindParam(':emp_id', $employee->emp_id, PDO::PARAM_INT);
    $query_leaves->execute();
    $leave_details = $query_leaves->fetchAll(PDO::FETCH_OBJ);

    // Display leave details in the table
    $pdf->SetFont('helvetica', '', 10);
    foreach ($leave_details as $leave) {
        $pdf->Cell(90, 7, htmlentities($leave->LeaveType), 1, 0, 'L');
        $pdf->Cell(40, 7, htmlentities($leave->available_day), 1, 1, 'C');
    }

    // Add a page break if needed
    if ($pdf->GetY() > 250) {
        $pdf->AddPage();
    }
}

// Output the PDF to the browser
$pdf->Output('employee_leave_report.pdf', 'I');
?>
