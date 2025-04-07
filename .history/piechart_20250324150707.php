<?php

// Approved status
$statusApproved = 1;
$currentYear = date('Y');

// Fetch approved leave data (Annual Leave: 1,2,3 | Medical Leave: 4)
$sql = "SELECT e.FirstName, lt.id AS leave_type, SUM(l.RequestedDays) AS leave_count
        FROM tblleave l 
        JOIN tblemployees e ON l.empid = e.emp_id 
        JOIN tblleavetype lt ON l.LeaveType = lt.LeaveType
        WHERE l.RegRemarks = :status 
        AND YEAR(STR_TO_DATE(l.FromDate, '%Y-%m-%d')) = :currentYear
        GROUP BY e.FirstName, lt.id";

$query = $dbh->prepare($sql);
$query->bindParam(':status', $statusApproved, PDO::PARAM_INT);
$query->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_ASSOC);

// Initialize arrays
$employees = [];
$leaveCountsAnnual = [];
$leaveCountsMedical = [];

// Process results into structured data
foreach ($results as $row) {
    $name = $row['FirstName'];
    $leaveType = $row['leave_type'];
    $leaveDays = $row['leave_count'];

    // Add employee name to list
    $employees[$name] = $name;

    // Assign leave counts based on type
    if (in_array($leaveType, [1, 2, 3])) {
        $leaveCountsAnnual[$name] = ($leaveCountsAnnual[$name] ?? 0) + $leaveDays;
    } elseif ($leaveType == 4) {
        $leaveCountsMedical[$name] = ($leaveCountsMedical[$name] ?? 0) + $leaveDays;
    }
}

// Ensure all employees have values for both leave types
foreach ($employees as $name) {
    $leaveCountsAnnual[$name] = $leaveCountsAnnual[$name] ?? 0;
    $leaveCountsMedical[$name] = $leaveCountsMedical[$name] ?? 0;
}

// Convert arrays to JSON for JavaScript usage
$employeeNamesJson = json_encode(array_values($employees));
$leaveCountsAnnualJson = json_encode(array_values($leaveCountsAnnual));
$leaveCountsMedicalJson = json_encode(array_values($leaveCountsMedical));
?>

<div class="card-box height-100-p d-flex flex-column justify-content-center align-items-center">
    <h4>Employees Leave (<?php echo $currentYear; ?>)</h4>
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
        margin: auto;
    }
    .no-data-message {
        font-size: 14px;
        color: #ff0000;
        text-align: center;
        font-weight: bold;
    }
</style>