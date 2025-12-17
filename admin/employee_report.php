<?php
include('../includes/session.php');
include('../includes/config.php');
require_once('../TCPDF-main/tcpdf.php');

class MYPDF extends TCPDF {

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

/* =========================
   PDF INIT
   ========================= */

$pdf = new MYPDF();
$pdf->SetCreator('Your Company');
$pdf->SetTitle('Employee Leave Report');

$currentYear = date('Y');
$statusApproved = 1;

$pdf->AddPage();
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Employee Leave Information Report', 0, 1, 'L');

/* =========================
   SUMMARY SECTION
   ========================= */

$pdf->Ln(4);
$pdf->SetFont('helvetica', 'B', 13);
$pdf->Cell(0, 10, "Summary of Employees Leave Taken ($currentYear)", 0, 1);

/* =========================
   DETERMINE LEAVE GROUPING
   ========================= */

$typeSql = "
SELECT id, LeaveType, IsDisplay
FROM tblleavetype
WHERE IsDisplay = 'Yes'
";
$typeStmt = $dbh->prepare($typeSql);
$typeStmt->execute();
$typeRows = $typeStmt->fetchAll(PDO::FETCH_ASSOC);

$annualTypeIds  = [];
$medicalTypeIds = [];

foreach ($typeRows as $t) {

    $name = strtolower($t['LeaveType']);

    // Medical bucket
    if (preg_match('/medical|hospital/i', $name)) {
        $medicalTypeIds[] = (int)$t['id'];
    }
    // Annual bucket = Annual + other IsDisplay Yes
    else {
        $annualTypeIds[] = (int)$t['id'];
    }
}

/* =========================
   FETCH APPROVED LEAVE TAKEN
   ========================= */

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

/* =========================
   AGGREGATE PER EMPLOYEE
   ========================= */

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

/* =========================
   CARRY FORWARD (ANNUAL ONLY)
   ========================= */

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

/* =========================
   SUMMARY TABLE
   ========================= */

$pdf->Ln(2);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(50, 8, 'Employee', 1);
$pdf->Cell(30, 8, 'Annual*', 1, 0, 'C');
$pdf->Cell(30, 8, 'Medical', 1, 0, 'C');
$pdf->Cell(25, 8, 'Total', 1, 0, 'C');
$pdf->Cell(30, 8, 'Carry Forward', 1, 1, 'C');

$pdf->SetFont('helvetica', '', 10);

foreach ($summary as $name => $data) {

    $total = $data['annual'] + $data['medical'];
    $cf    = $carryForward[$name] ?? 0;

    $pdf->Cell(50, 8, $name, 1);
    $pdf->Cell(30, 8, number_format($data['annual'], 1), 1, 0, 'C');
    $pdf->Cell(30, 8, number_format($data['medical'], 1), 1, 0, 'C');
    $pdf->Cell(25, 8, number_format($total, 1), 1, 0, 'C');
    $pdf->Cell(30, 8, number_format($cf, 1), 1, 1, 'C');
}

/* =========================
   FOOTNOTE
   ========================= */

$pdf->Ln(3);
$pdf->SetFont('helvetica', 'I', 9);
$pdf->MultiCell(0, 5, 
    "* Annual includes Annual Leave and other leave types marked as IsDisplay = Yes (excluding Medical/Hospital)."
);

/* =========================
   OUTPUT
   ========================= */

$pdf->Output('employee_leave_report.pdf', 'I');
?>
