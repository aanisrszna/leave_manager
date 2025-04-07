<?php
// Database connection (modify accordingly)
$host = "your_host";
$user = "your_username";
$password = "your_password";
$database = "your_database";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch leave records
$query = "SELECT emp_id, leave_type, leave_count FROM tbl_leave";
$result = $conn->query($query);

$leaveCountsAnnual = [];
$leaveCountsMedical = [];
$employees = [];

while ($row = $result->fetch_assoc()) {
    $empId = intval($row['emp_id']); // Ensure it's an integer
    $leaveType = intval($row['leave_type']);  // Ensure it's an integer
    $leaveDays = floatval($row['leave_count']); // Convert to float for decimals

    // Store employee IDs
    $employees[$empId] = $empId;

    // Assign leave counts based on type
    if (in_array($leaveType, [1, 2, 3])) {
        $leaveCountsAnnual[$empId] = ($leaveCountsAnnual[$empId] ?? 0) + $leaveDays;
    } elseif ($leaveType === 4) {
        $leaveCountsMedical[$empId] = ($leaveCountsMedical[$empId] ?? 0) + $leaveDays;
    }
}

// Encode data to JSON for frontend use
$chartData = [
    "employees" => array_values($employees), // Ensure correct ordering
    "annualLeave" => $leaveCountsAnnual,
    "medicalLeave" => $leaveCountsMedical
];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<canvas id="leaveChart"></canvas>

<script>
    // Parse PHP data to JavaScript
    const chartData = <?php echo json_encode($chartData); ?>;

    // Extract data for the chart
    const employees = chartData.employees;
    const annualLeaveData = employees.map(empId => chartData.annualLeave[empId] || 0);
    const medicalLeaveData = employees.map(empId => chartData.medicalLeave[empId] || 0);

    // Debugging: Log data before rendering
    console.log("Employees:", employees);
    console.log("Annual Leave Data:", annualLeaveData);
    console.log("Medical Leave Data:", medicalLeaveData);

    // Create Bar Chart
    const ctx = document.getElementById('leaveChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: employees,
            datasets: [
                {
                    label: "Annual Leave",
                    backgroundColor: "blue",
                    data: annualLeaveData
                },
                {
                    label: "Medical Leave",
                    backgroundColor: "red",
                    data: medicalLeaveData
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

</body>
</html>
