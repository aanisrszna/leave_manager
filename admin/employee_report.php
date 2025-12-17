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

// Example user info (replace dynamically if needed)
$fullname = 'John Doe';
$staff_position = 'Software Engineer';

// Create PDF
$pdf = new MYPDF($fullname, $staff_position);
$pdf->SetCreator('Your Company');
$pdf->SetTitle('Employee Leave Report');

$currentYear = date('Y');
$statusApproved = 1;

// Add first page
$pdf->AddPage();

// Title
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Employee Leave Information Report', 0, 1, 'L');

/* =========================
   SUMMARY OF LEAVE TAKEN
   ========================= */

$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', 13);
$pdf->Cell(0, 10, 'Summary of Employees Leave Taken (' . $currentYear . ')', 0, 1, 'L');
$pdf->Ln(2);

/* ---- Detect Annual vs Medical Leave Types ---- */
$typeSql = "SELECT id, LeaveType FROM tblleavetype WHERE IsDisplay = 'Yes'";
$typeStmt = $dbh->prepare($typeSql);
$typeStmt->execute();
$typeRows = $typeStmt->fetchAll(PDO::FETCH_ASSOC);

$annualTypeIds = [];
$medicalTypeIds = [];

foreach ($typeRows as $t) {
    if (stripos($t['LeaveType'], 'Annual') === 0) {
        $annualTypeIds[] = (int)$t['id'];
    } elseif (preg_match('/medical|hospital/i', $t['LeaveType'])) {
        $medicalTypeIds[] = (int)$t['id'];
    }
}

/* ---- Fetch Approved Leave Taken ---- */
$sql = "
SELECT 
    e.emp_id,
    e.FirstName,
    lt.id AS leave_type_id,
    COALESCE(SUM(l.RequestedDays),0) AS days_taken
FROM tblemployees e
LEFT JOIN tblleave l 
    ON l.empid = e.emp_id
    AND l.RegRemarks = :status
    AND YEAR(l.FromDate) = :year
LEFT JOIN tblleavetype lt 
    ON l.LeaveType = lt.LeaveType
WHERE e.Status NOT IN ('Inactive','Offline')
GROUP BY e.emp_id, lt.id
ORDER BY e.FirstName
";

$q = $dbh->prepare($sql);
$q->bindParam(':status', $statusApproved, PDO::PARAM_INT);
$q->bindParam(':year', $currentYear, PDO::PARAM_INT);
$q->execute();
$rows = $q->fetchAll(PDO::FETCH_ASSOC);

/* ---- Aggregate Leave Taken ---- */
$summary = [];

foreach ($rows as $r) {
    $name = $r['FirstName'];
    $type = (int)$r['leave_type_id'];
    $days = (float)$r['days_taken'];

    if (!isset($summary[$name])) {
        $summary[$name] = [
            'annual'  => 0,
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

/* ---- Fetch Carry Forward (available_day for Annual leave only) ---- */
$annualIdsCsv = implode(',', $annualTypeIds);

$cfSql = "
SELECT 
    e.FirstName,
    COALESCE(SUM(el.available_day),0) AS carry_forward
FROM tblemployees e
LEFT JOIN employee_leave el 
    ON el.emp_id = e.emp_id
LEFT JOIN tblleavetype lt 
    ON el.leave_type_id = lt.id
WHERE 
    e.Status NOT IN ('Inactive','Offline')
    AND lt.LeaveType LIKE 'Annual%'
GROUP BY e.emp_id
";

$cfStmt = $dbh->prepare($cfSql);
$cfStmt->execute();
$cfRows = $cfStmt->fetchAll(PDO::FETCH_ASSOC);

$carryForward = [];
foreach ($cfRows as $r) {
    $carryForward[$r['FirstName']] = (float)$r['carry_forward'];
}

/* ---- Summary Table Header ---- */
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(50, 8, 'Employee', 1, 0, 'C');
$pdf->Cell(30, 8, 'Annual', 1, 0, 'C');
$pdf->Cell(30, 8, 'Medical', 1, 0, 'C');
$pdf->Cell(25, 8, 'Total', 1, 0, 'C');
$pdf->Cell(30, 8, 'Carry Forward', 1, 1, 'C');

/* ---- Summary Table Body ---- */
$pdf->SetFont('helvetica', '', 10);

foreach ($summary as $name => $data) {

    $total = $data['annual'] + $data['medical'];
    $cf = $carryForward[$name] ?? 0;

    $pdf->Cell(50, 8, $name, 1, 0, 'L');
    $pdf->Cell(30, 8, number_format($data['annual'], 1), 1, 0, 'C');
    $pdf->Cell(30, 8, number_format($data['medical'], 1), 1, 0, 'C');
    $pdf->Cell(25, 8, number_format($total, 1), 1, 0, 'C');
    $pdf->Cell(30, 8, number_format($cf, 1), 1, 1, 'C');
}

/* ---- New Page for Detailed Leave Balances ---- */
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);

// Employee list
$sql = "
SELECT DISTINCT e.emp_id, e.FirstName
FROM employee_leave el
JOIN tblemployees e ON el.emp_id = e.emp_id
WHERE e.Status NOT IN ('Inactive','Offline')
ORDER BY e.FirstName
";
$query = $dbh->prepare($sql);
$query->execute();
$employees = $query->fetchAll(PDO::FETCH_OBJ);

/* ---- Per Employee Details ---- */
foreach ($employees as $employee) {

    $pdf->Ln(10);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Leave Information for ' . htmlentities($employee->FirstName), 0, 1, 'L');

    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(90, 7, 'Leave Type', 1, 0, 'C');
    $pdf->Cell(40, 7, 'Available Days', 1, 1, 'C');

    $sql_leaves = "
    SELECT lt.LeaveType, el.available_day
    FROM employee_leave el
    JOIN tblleavetype lt ON el.leave_type_id = lt.id
    WHERE el.emp_id = :emp_id
    ";

    $query_leaves = $dbh->prepare($sql_leaves);
    $query_leaves->bindParam(':emp_id', $employee->emp_id, PDO::PARAM_INT);
    $query_leaves->execute();
    $leave_details = $query_leaves->fetchAll(PDO::FETCH_OBJ);

    $pdf->SetFont('helvetica', '', 10);
    foreach ($leave_details as $leave) {
        $pdf->Cell(90, 7, htmlentities($leave->LeaveType), 1, 0, 'L');
        $pdf->Cell(40, 7, number_format($leave->available_day, 1), 1, 1, 'C');
    }

    if ($pdf->GetY() > 250) {
        $pdf->AddPage();
    }
}

// Output PDF
$pdf->Output('employee_leave_report.pdf', 'I');
?>
