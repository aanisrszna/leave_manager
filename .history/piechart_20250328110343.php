<?php

// Approved status
$statusApproved = 1;
$currentYear = date('Y');

// Fetch approved leave data (Annual Leave: 1,2,3 | Medical Leave: 4)
$sql = "SELECT e.emp_id, e.LastName, lt.id AS leave_type, COALESCE(SUM(l.RequestedDays), 0) AS leave_count
        FROM tblemployees e
        LEFT JOIN tblleave l ON l.empid = e.emp_id AND l.RegRemarks = :status 
            AND YEAR(STR_TO_DATE(l.FromDate, '%Y-%m-%d')) = :currentYear
        LEFT JOIN tblleavetype lt ON l.LeaveType = lt.LeaveType
        WHERE e.Role != 'Director'  -- Exclude Director Role
        GROUP BY e.emp_id, e.LastName, lt.id
        ORDER BY e.emp_id";



$query = $dbh->prepare($sql);
$query->bindParam(':status', $statusApproved, PDO::PARAM_INT);
$query->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_ASSOC);

// Debugging: Check fetched data
// echo "<pre>"; print_r($results); echo "</pre>";

$employees = [];
$leaveCountsAnnual = [];
$leaveCountsMedical = [];

// Ensure default values
foreach ($results as $row) {
    $name = $row['LastName'];
    $leaveType = intval($row['leave_type']);
    $leaveDays = floatval($row['leave_count']);

    if (!isset($employees[$name])) {
        $employees[$name] = $name;
        $leaveCountsAnnual[$name] = 0;
        $leaveCountsMedical[$name] = 0;
    }

    if (in_array($leaveType, [1, 2, 3, 33, 34, 35])) {
        if (!isset($leaveCountsAnnual[$name])) {
            $leaveCountsAnnual[$name] = 0;
        }
        $leaveCountsAnnual[$name] += $leaveDays; // Accumulate leave days
    }
    
    if ($leaveType === 4) {
        if (!isset($leaveCountsMedical[$name])) {
            $leaveCountsMedical[$name] = 0;
        }
        $leaveCountsMedical[$name] += $leaveDays; // Accumulate medical leave days
    }
    
    
}

// Ensure all employees have values for both leave types
foreach ($employees as $name) {
    $leaveCountsAnnual[$name] = $leaveCountsAnnual[$name] ?? 0;
    $leaveCountsMedical[$name] = $leaveCountsMedical[$name] ?? 0;
}

// Debugging: Check processed leave data
// echo "<pre>"; print_r($leaveCountsAnnual); print_r($leaveCountsMedical); echo "</pre>";

$employeeNamesJson = json_encode(array_values($employees));
$leaveCountsAnnualJson = json_encode(array_values($leaveCountsAnnual));
$leaveCountsMedicalJson = json_encode(array_values($leaveCountsMedical));


?>

<div class="card-box height-100-p d-flex flex-column justify-content-center align-items-center">
    <h5>Employees Leave <?php echo $currentYear; ?></h5>
    <div class="chart-container">
        <?php if (!empty($employees)): ?>
            <canvas id="approvedLeaveBarChart"></canvas>
        <?php else: ?>
            <p class="no-data-message">No approved leave records found for <?php echo $currentYear; ?>.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var employeeNames = <?php echo $employeeNamesJson; ?>;
        var leaveCountsAnnual = <?php echo $leaveCountsAnnualJson; ?>;
        var leaveCountsMedical = <?php echo $leaveCountsMedicalJson; ?>;

        console.log("Annual Leave Data:", leaveCountsAnnual);
        console.log("Medical Leave Data:", leaveCountsMedical);

        if (employeeNames.length === 0) {
            return;
        }

        var ctx = document.getElementById('approvedLeaveBarChart').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: employeeNames,
                datasets: [
                    {
                        label: 'Annual Leave',
                        data: leaveCountsAnnual,
                        backgroundColor: '#36A2EB',
                        borderWidth: 1
                    },
                    {
                        label: 'Medical Leave',
                        data: leaveCountsMedical,
                        backgroundColor: '#FF6384',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false, // Disable all animations
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Employees'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Number of Leave Days'
                        },
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function (tooltipItem) {
                                let datasetIndex = tooltipItem.datasetIndex;
                                let value = tooltipItem.raw || 0;
                                let category = datasetIndex === 0 ? "Annual Leave" : "Medical Leave";
                                return `${value} days (${category})`;
                            }
                        }
                    }
                }
            }
        });
    });
</script>

<style>
    .chart-container {
        width: 100%;
        max-width: 800px;
        height: 400px;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .no-data-message {
        font-size: 14px;
        color: #ff0000;
        text-align: center;
        font-weight: bold;
    }
</style>
