<?php
// Approved status
$statusApproved = 1;
$currentYear = date('Y');

/* =========================
   DETERMINE LEAVE GROUPING
   ========================= */
$typeSql = "SELECT id, LeaveType FROM tblleavetype WHERE IsDisplay = 'Yes'";
$typeStmt = $dbh->prepare($typeSql);
$typeStmt->execute();
$typeRows = $typeStmt->fetchAll(PDO::FETCH_ASSOC);

$annualTypeIds = [];
$medicalTypeIds = [];

foreach ($typeRows as $t) {
    $id   = (int)$t['id'];
    $name = strtolower(trim($t['LeaveType']));

    if (preg_match('/medical|hospital/i', $name)) {
        $medicalTypeIds[] = $id;
    } else {
        $annualTypeIds[] = $id;
    }
}

$annualTypeIds  = array_values(array_unique($annualTypeIds));
$medicalTypeIds = array_values(array_unique($medicalTypeIds));

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
   AGGREGATE FOR CHART
   ========================= */
$employees = [];
$leaveCountsAnnual = [];
$leaveCountsMedical = [];

foreach ($rows as $r) {

    $name = $r['FirstName'];
    $type = (int)$r['leave_type_id'];
    $days = (float)$r['days_taken'];

    if (!isset($employees[$name])) {
        $employees[$name] = $name;
        $leaveCountsAnnual[$name] = 0;
        $leaveCountsMedical[$name] = 0;
    }

    if (in_array($type, $annualTypeIds, true)) {
        $leaveCountsAnnual[$name] += $days;
    }

    if (in_array($type, $medicalTypeIds, true)) {
        $leaveCountsMedical[$name] += $days;
    }
}
