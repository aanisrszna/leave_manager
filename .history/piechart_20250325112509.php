<?php
// Database Connection

$status = 'Approved'; // Modify as needed
$currentYear = date('Y'); 

$sql = "SELECT e.LastName AS employee_name, 
               lt.LeaveType AS leave_type, 
               SUM(l.RequestedDays) AS leave_count
        FROM tblleave l 
        JOIN tblemployees e ON l.empid = e.emp_id 
        JOIN tblleavetype lt ON lt.id = l.LeaveType 
        WHERE l.RegRemarks = :status 
        AND YEAR(STR_TO_DATE(l.FromDate, '%Y-%m-%d')) = :currentYear
        GROUP BY e.LastName, lt.LeaveType";

$query = $dbh->prepare($sql);
$query->bindParam(':status', $status, PDO::PARAM_STR);
$query->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$query->execute();
$leaveResults = $query->fetchAll(PDO::FETCH_ASSOC);

// Debugging Output
echo "<pre>"; print_r($leaveResults); echo "</pre>";

// Format Data for Chart.js
$employees = [];
$annualLeaveData = [];
$medicalLeaveData = [];

foreach ($leaveResults as $row) {
    $name = $row['employee_name'];
    if (!in_array($name, $employees)) {
        $employees[] = $name;
        $annualLeaveData[$name] = 0;
        $medicalLeaveData[$name] = 0;
    }
    
    if ($row['leave_type'] == 'Annual Leave - Staff') {
        $annualLeaveData[$name] = $row['leave_count'];
    } elseif ($row['leave_type'] == 'Medical/Sick Leave') {
        $medicalLeaveData[$name] = $row['leave_count'];
    }
}

// Convert Data for Chart.js
$labels = json_encode(array_values($employees));
$annualLeaveValues = json_encode(array_values($annualLeaveData));
$medicalLeaveValues = json_encode(array_values($medicalLeaveData));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Leave Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <canvas id="leaveChart"></canvas>
    <script>
        var ctx = document.getElementById('leaveChart').getContext('2d');
        var leaveChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo $labels; ?>,
                datasets: [
                    {
                        label: 'Annual Leave',
                        backgroundColor: 'blue',
                        data: <?php echo $annualLeaveValues; ?>
                    },
                    {
                        label: 'Medical Leave',
                        backgroundColor: 'red',
                        data: <?php echo $medicalLeaveValues; ?>
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</body>
</html>
