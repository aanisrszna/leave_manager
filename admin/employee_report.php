<?php
include('../includes/session.php');
include('../includes/config.php');
require_once('../TCPDF-main/tcpdf.php');

/* =========================
   TCPDF CLASS
   ========================= */
class MYPDF extends TCPDF {

    public function Header() {
        $this->Image('../vendors/images/riverraven.png', 160, 10, 30);
        $this->Ln(8);
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(
            0,
            10,
            'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(),
            0,
            0,
            'C'
        );
    }
}

/* =========================
   PDF INIT
   ========================= */
$pdf = new MYPDF();
$pdf->SetCreator('River Raven');
$pdf->SetTitle('Employee Leave Report');

$currentYear = date('Y');
$statusApproved = 1;

$pdf->AddPage();

/* =========================
   TITLE
   ========================= */
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Employee Leave Information Report', 0, 1);

$pdf->SetFont('helvetica', 'B', 13);
$pdf->Cell(0, 8, "Summary of Employees Leave Taken ($currentYear)", 0, 1);

/* =========================
   LEAVE TYPE GROUPING
   ========================= */
$typeSql = "SELECT id, LeaveType FROM tblleavetype WHERE IsDisplay = 'Yes'";
$typeStmt = $dbh->prepare($typeSql);
$typeStmt->execute();
$typeRows = $typeStmt->fetchAll(PDO::FETCH_ASSOC);

$annualTypeIds = [];
$medicalTypeIds = [];

foreach ($typeRows as $t) {
    if (preg_match('/medical|hospital/i', strtolower($t['LeaveType']))) {
        $medicalTypeIds[] = (int)$t['id'];
    } else {
        $annualTypeIds[] = (int)$t['id'];
    }
}

/* =========================
   FETCH APPROVED LEAVE
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
   BUILD SUMMARY
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
   CARRY FORWARD (ANNUAL)
   ========================= */
$cfSql = "
SELECT 
    e.FirstName,
    COALESCE(SUM(el.available_day),0) AS carry_forward
FROM tblemployees e
LEFT JOIN employee_leave el ON el.emp_id = e.emp_id
LEFT JOIN tblleavetype lt ON el.leave_type_id = lt.id
WHERE e.Status NOT IN ('Inactive','Offline')
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
   SUMMARY TABLE (WITH CF)
   ========================= */
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(60, 8, 'Employee', 1);
$pdf->Cell(35, 8, 'Annual Taken', 1, 0, 'C');
$pdf->Cell(30, 8, 'Medical Taken', 1, 0, 'C');
$pdf->Cell(20, 8, 'Total', 1, 0, 'C');
$pdf->Cell(25, 8, 'Carry Forward', 1, 1, 'C');

$pdf->SetFont('helvetica', '', 10);

foreach ($summary as $name => $data) {

    $cf = $carryForward[$name] ?? 0;
    $annualTotal = $data['annual'] + $cf;

    $pdf->Cell(60, 8, $name, 1);
    $pdf->Cell(
        35,
        8,
        number_format($data['annual'],1).' / '.number_format($annualTotal,1),
        1,
        0,
        'C'
    );
    $pdf->Cell(
        30,
        8,
        number_format($data['medical'],1).' / 14.0',
        1,
        0,
        'C'
    );
    $pdf->Cell(
        20,
        8,
        number_format($data['annual'] + $data['medical'],1),
        1,
        0,
        'C'
    );
    $pdf->Cell(
        25,
        8,
        number_format($cf,1),
        1,
        1,
        'C'
    );
}

/* =========================
   PREPARE CHART DATA
   ========================= */
$employees = array_keys($summary);
$annualData = array_column($summary, 'annual');
$medicalData = array_column($summary, 'medical');

/* =========================
   BAR CHART (UNCHANGED)
   ========================= */
$pdf->Ln(12);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 8, 'Employees Leave Summary Chart', 0, 1);

$chartHeight = 60;
$startX = 30;
$startY = $pdf->GetY();

$maxValue = max(max($annualData ?: [0]), max($medicalData ?: [0]), 21);
$barWidth = 5;
$gap = 10;
$x = $startX;

// Axes
$pdf->Line($startX - 5, $startY, $startX - 5, $startY + $chartHeight);
$pdf->Line($startX - 5, $startY + $chartHeight, 190, $startY + $chartHeight);

// Y-scale
$pdf->SetFont('helvetica', '', 7);
for ($i = 0; $i <= 5; $i++) {
    $val = round(($maxValue / 5) * $i);
    $y = $startY + $chartHeight - ($chartHeight / 5 * $i);
    $pdf->Line($startX - 7, $y, $startX - 5, $y);
    $pdf->SetXY($startX - 25, $y - 3);
    $pdf->Cell(15, 6, $val, 0, 0, 'R');
}

// Legend (top-right, adjusted down)
$legendX = 150;
$legendY = $startY + 4;

$pdf->SetFont('helvetica', '', 9);
$pdf->SetFillColor(54,162,235);
$pdf->Rect($legendX, $legendY, 4, 4, 'F');
$pdf->Text($legendX + 6, $legendY, 'Annual');

$pdf->SetFillColor(255,99,132);
$pdf->Rect($legendX, $legendY + 6, 4, 4, 'F');
$pdf->Text($legendX + 6, $legendY + 6, 'Medical');

// Bars
$pdf->SetFont('helvetica', '', 8);

foreach ($employees as $idx => $name) {

    $annual = $annualData[$idx];
    $medical = $medicalData[$idx];

    $hA = ($annual / $maxValue) * $chartHeight;
    $hM = ($medical / $maxValue) * $chartHeight;

    $pdf->SetFillColor(54,162,235);
    $pdf->Rect($x, $startY + $chartHeight - $hA, $barWidth, $hA, 'F');

    $pdf->SetFillColor(255,99,132);
    $pdf->Rect($x + $barWidth, $startY + $chartHeight - $hM, $barWidth, $hM, 'F');

    // Rotated label (short name)
    $short = explode(' ', $name)[0];
    $pdf->StartTransform();
    $pdf->Rotate(45, $x + 3, $startY + $chartHeight + 5);
    $pdf->Text($x, $startY + $chartHeight + 5, $short);
    $pdf->StopTransform();

    $x += ($barWidth * 2) + $gap;
}

/* =========================
   FOOTNOTE
   ========================= */
$pdf->Ln(20);
$pdf->SetFont('helvetica', 'I', 9);
$pdf->MultiCell(
    0,
    5,
    "* Annual and Medical leave reflect approved leave taken during the $currentYear calendar year only.",
    0,
    'L'
);

/* =========================
   OUTPUT
   ========================= */
$pdf->Output('employee_leave_report.pdf', 'I');
exit;
