<?php
/**
 * Approved Leave Bar Chart
 * 
 * This script fetches approved leave requests and visualizes them using 
 * a bar chart (Annual Leave vs. Medical Leave per Employee).
 */

// Database connection assumed to be already established

// Define approved leave status
$statusApproved = 1;
$currentYear = date('Y'); // Define current year

// Fetch approved leave count per employee for Annual Leave (Types 1, 2, 3)
$sqlAnnual = "SELECT e.FirstName, COUNT(l.id) AS leave_count 
        FROM tblleave l 
        JOIN tblemployees e ON l.empid = e.emp_id 
        JOIN tblleavetype lt ON l.LeaveType = lt.LeaveType
        WHERE l.RegRemarks = :status 
        AND YEAR(STR_TO_DATE(l.FromDate, '%Y-%m-%d')) = :currentYear
        AND lt.id IN (1, 2, 3) 
        GROUP BY e.FirstName";

$queryAnnual = $dbh->prepare($sqlAnnual);
$queryAnnual->bindParam(':status', $statusApproved, PDO::PARAM_INT);
$queryAnnual->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$queryAnnual->execute();
$resultsAnnual = $queryAnnual->fetchAll(PDO::FETCH_ASSOC);

// Fetch approved leave count per employee for Medical Leave (Type 4)
$sqlMedical = "SELECT e.FirstName, COUNT(l.id) AS leave_count 
        FROM tblleave l 
        JOIN tblemployees e ON l.empid = e.emp_id 
        JOIN tblleavetype lt ON l.LeaveType = lt.LeaveType
        WHERE l.RegRemarks = :status 
        AND YEAR(STR_TO_DATE(l.FromDate, '%Y-%m-%d')) = :currentYear
        AND lt.id = 4
        GROUP BY e.FirstName";

$queryMedical = $dbh->prepare($sqlMedical);
$queryMedical->bindParam(':status', $statusApproved, PDO::PARAM_INT);
$queryMedical->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$queryMedical->execute();
$resultsMedical = $queryMedical->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for charts
$employeeNames = [];
$leaveCountsAnnual = [];
$leaveCountsMedical = [];

// Combine employee names from both lists to ensure unique names
foreach ($resultsAnnual as $row) {
    $employeeNames[$row['FirstName']] = $row['FirstName'];
    $leaveCountsAnnual[$row['FirstName']] = $row['leave_count'];
}
foreach ($resultsMedical as $row) {
    $employeeNames[$row['FirstName']] = $row['FirstName'];
    $leaveCountsMedical[$row['FirstName']] = $row['leave_count'];
}

// Ensure all employees have values (default 0 if missing in either dataset)
foreach ($employeeNames as $name) {
    $leaveCountsAnnual[$name] = $leaveCountsAnnual[$name] ?? 0;
    $leaveCountsMedical[$name] = $leaveCountsMedical[$name] ?? 0;
}

// Convert arrays to JSON for JavaScript usage
$employeeNamesJson = json_encode(array_values($employeeNames));
$leaveCountsAnnualJson = json_encode(array_values($leaveCountsAnnual));
$leaveCountsMedicalJson = json_encode(array_values($leaveCountsMedical));

// Check if there is data available
$hasData = !empty($resultsAnnual) || !empty($resultsMedical);
?>

<div class="card-box height-100-p d-flex flex-column justify-content-center align-items-center">
    <h4>Employees Leave (<?php echo $currentYear; ?>)</h4>
    <div class="pb-20">
        <?php if ($hasData): ?>
            <canvas id="approvedLeaveBarChart" width="600" height="400"></canvas>
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
            console.log("No data available for the chart.");
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
                        backgroundColor: '#36A2EB', // Blue for Annual Leave
                        borderWidth: 1
                    },
                    {
                        label: 'Medical Leave',
                        data: leaveCountsMedical,
                        backgroundColor: '#FF6384', // Red for Medical Leave
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
                        stepSize: 1
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
    .approved-leave-chart {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 400px;
        text-align: left;
        padding-left: 20px;
        margin-bottom: 30px;
    }

    .approved-leave-chart .card-box {
        width: 100% !important;
        height: 100% !important;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        padding-left: 20px;
    }

    .approved-leave-chart canvas {
        width: 100% !important;
        max-width: 600px;
        height: auto;
        max-height: 400px;
        padding: 10px;
    }

    .no-data-message {
        font-size: 16px;
        color: #ff0000;
        text-align: center;
        font-weight: bold;
    }
</style>
<?php
/**
 * Approved Leave Bar Chart
 * 
 * This script fetches approved leave requests and visualizes them using 
 * a bar chart (Annual Leave vs. Medical Leave per Employee).
 */

// Database connection assumed to be already established

// Define approved leave status
$statusApproved = 1;
$currentYear = date('Y'); // Define current year

// Fetch approved leave count per employee for Annual Leave (Types 1, 2, 3)
// Fetch approved leave count per employee for Annual Leave (Types 1, 2, 3)
$sqlAnnual = "SELECT e.FirstName, SUM(l.RequestedDays) AS leave_count 
        FROM tblleave l 
        JOIN tblemployees e ON l.empid = e.emp_id 
        JOIN tblleavetype lt ON l.LeaveType = lt.LeaveType
        WHERE l.RegRemarks = :status 
        AND YEAR(STR_TO_DATE(l.FromDate, '%Y-%m-%d')) = :currentYear
        AND lt.id IN (1, 2, 3) 
        GROUP BY e.FirstName";

$queryAnnual = $dbh->prepare($sqlAnnual);
$queryAnnual->bindParam(':status', $statusApproved, PDO::PARAM_INT);
$queryAnnual->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$queryAnnual->execute();
$resultsAnnual = $queryAnnual->fetchAll(PDO::FETCH_ASSOC);

// Fetch approved leave count per employee for Medical Leave (Type 4)
$sqlMedical = "SELECT e.FirstName, SUM(l.RequestedDays) AS leave_count 
        FROM tblleave l 
        JOIN tblemployees e ON l.empid = e.emp_id 
        JOIN tblleavetype lt ON l.LeaveType = lt.LeaveType
        WHERE l.RegRemarks = :status 
        AND YEAR(STR_TO_DATE(l.FromDate, '%Y-%m-%d')) = :currentYear
        AND lt.id = 4
        GROUP BY e.FirstName";

$queryMedical = $dbh->prepare($sqlMedical);
$queryMedical->bindParam(':status', $statusApproved, PDO::PARAM_INT);
$queryMedical->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$queryMedical->execute();
$resultsMedical = $queryMedical->fetchAll(PDO::FETCH_ASSOC);


// Prepare data for charts
$employeeNames = [];
$leaveCountsAnnual = [];
$leaveCountsMedical = [];



// Combine employee names from both lists to ensure unique names
foreach ($resultsAnnual as $row) {
    $employeeNames[$row['FirstName']] = $row['FirstName'];
    $leaveCountsAnnual[$row['FirstName']] = $row['leave_count'];
}
foreach ($resultsMedical as $row) {
    $employeeNames[$row['FirstName']] = $row['FirstName'];
    $leaveCountsMedical[$row['FirstName']] = $row['leave_count'];
}

// Ensure all employees have values (default 0 if missing in either dataset)
foreach ($employeeNames as $name) {
    $leaveCountsAnnual[$name] = $leaveCountsAnnual[$name] ?? 0;
    $leaveCountsMedical[$name] = $leaveCountsMedical[$name] ?? 0;
}

// Convert arrays to JSON for JavaScript usage
$employeeNamesJson = json_encode(array_values($employeeNames));
$leaveCountsAnnualJson = json_encode(array_values($leaveCountsAnnual));
$leaveCountsMedicalJson = json_encode(array_values($leaveCountsMedical));

// Check if there is data available
$hasData = !empty($resultsAnnual) || !empty($resultsMedical);
?>




<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var employeeNames = <?php echo $employeeNamesJson; ?>;
        var leaveCountsAnnual = <?php echo $leaveCountsAnnualJson; ?>;
        var leaveCountsMedical = <?php echo $leaveCountsMedicalJson; ?>;

        if (employeeNames.length === 0) {
            console.log("No data available for the chart.");
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
                        backgroundColor: '#36A2EB', // Blue for Annual Leave
                        borderWidth: 1
                    },
                    {
                        label: 'Medical Leave',
                        data: leaveCountsMedical,
                        backgroundColor: '#FF6384', // Red for Medical Leave
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
                        stepSize: 1
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
                                let employeeIndex = tooltipItem.dataIndex;
                                let totalLeave = leaveCountsAnnual[employeeIndex] + leaveCountsMedical[employeeIndex];
                                let category = datasetIndex === 0 ? "Annual Leave" : "Medical Leave";
                                return `${tooltipItem.raw} days (${category}) | Total: ${totalLeave} days`;
                            }
                        }
                    }

                }
            }
        });
    });
</script>

<style>
    .approved-leave-chart {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 400px;
        text-align: left;
        padding-left: 20px;
        margin-bottom: 30px;
    }

    .approved-leave-chart .card-box {
        width: 100% !important;
        height: 100% !important;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        padding-left: 20px;
    }

    .approved-leave-chart canvas {
        width: 100% !important;
        max-width: 600px;
        height: auto;
        max-height: 400px;
        padding: 10px;
    }

    .no-data-message {
        font-size: 16px;
        color: #ff0000;
        text-align: center;
        font-weight: bold;
    }
</style>
