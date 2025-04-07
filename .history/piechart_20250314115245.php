<?php
/**
 * Approved Leave Pie Charts
 * 
 * This script fetches the count of approved leave requests per employee 
 * and visualizes them using two Pie Charts with Chart.js.
 */

// Database connection assumed to be already established

// Define approved leave status
$statusApproved = 1;
$currentYear = date('Y');

// Fetch approved leave count per employee for leave types 1, 2, 3
$sql1 = "SELECT e.FirstName, COUNT(l.id) AS leave_count 
        FROM tblleave l 
        JOIN tblemployees e ON l.empid = e.emp_id 
        JOIN tblleavetype lt ON l.LeaveType = lt.LeaveType
        WHERE l.RegRemarks = :status 
        AND YEAR(STR_TO_DATE(l.FromDate, '%Y-%m-%d')) = :currentYear
        AND lt.id IN (1, 2, 3) 
        GROUP BY e.FirstName";

$query1 = $dbh->prepare($sql1);
$query1->bindParam(':status', $statusApproved, PDO::PARAM_INT);
$query1->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$query1->execute();
$results1 = $query1->fetchAll(PDO::FETCH_ASSOC);

// Fetch approved leave count per employee for leave type 4
$sql2 = "SELECT e.FirstName, COUNT(l.id) AS leave_count 
        FROM tblleave l 
        JOIN tblemployees e ON l.empid = e.emp_id 
        JOIN tblleavetype lt ON l.LeaveType = lt.LeaveType
        WHERE l.RegRemarks = :status 
        AND YEAR(STR_TO_DATE(l.FromDate, '%Y-%m-%d')) = :currentYear
        AND lt.id = 4
        GROUP BY e.FirstName";

$query2 = $dbh->prepare($sql2);
$query2->bindParam(':status', $statusApproved, PDO::PARAM_INT);
$query2->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$query2->execute();
$results2 = $query2->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for charts
$empNames1 = [];
$leaveCounts1 = [];

foreach ($results1 as $row) {
    $empNames1[] = $row['FirstName'];
    $leaveCounts1[] = $row['leave_count'];
}

$empNames2 = [];
$leaveCounts2 = [];

foreach ($results2 as $row) {
    $empNames2[] = $row['FirstName'];
    $leaveCounts2[] = $row['leave_count'];
}

// Convert data to JSON for JavaScript usage
$empNamesJson1 = json_encode($empNames1);
$leaveCountsJson1 = json_encode($leaveCounts1);
$empNamesJson2 = json_encode($empNames2);
$leaveCountsJson2 = json_encode($leaveCounts2);

$hasData1 = !empty($results1);
$hasData2 = !empty($results2);
?>

<div class="card-box height-100-p d-flex flex-column justify-content-center align-items-center">
    <h4>Employees Leave <?php echo $currentYear; ?></h4>
    
    <div class="pb-20">
        <h5>Leave Types 1, 2, 3</h5>
        <?php if ($hasData1): ?>
            <canvas id="approvedLeavePieChart1" width="300" height="300"></canvas>
        <?php else: ?>
            <p class="no-data-message">No approved leave records found for leave types 1, 2, 3 in <?php echo $currentYear; ?>.</p>
        <?php endif; ?>
    </div>

    <div class="pb-20">
        <h5>Leave Type 4</h5>
        <?php if ($hasData2): ?>
            <canvas id="approvedLeavePieChart2" width="300" height="300"></canvas>
        <?php else: ?>
            <p class="no-data-message">No approved leave records found for leave type 4 in <?php echo $currentYear; ?>.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var empNames1 = <?php echo $empNamesJson1; ?>;
        var leaveCounts1 = <?php echo $leaveCountsJson1; ?>;
        var empNames2 = <?php echo $empNamesJson2; ?>;
        var leaveCounts2 = <?php echo $leaveCountsJson2; ?>;

        if (empNames1.length > 0) {
            var ctx1 = document.getElementById('approvedLeavePieChart1').getContext('2d');
            new Chart(ctx1, {
                type: 'pie',
                data: {
                    labels: empNames1,
                    datasets: [{
                        data: leaveCounts1,
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4CAF50', '#FF9800'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: { boxWidth: 12, padding: 20 }
                        }
                    }
                }
            });
        }

        if (empNames2.length > 0) {
            var ctx2 = document.getElementById('approvedLeavePieChart2').getContext('2d');
            new Chart(ctx2, {
                type: 'pie',
                data: {
                    labels: empNames2,
                    datasets: [{
                        data: leaveCounts2,
                        backgroundColor: ['#9C27B0', '#3F51B5', '#009688', '#CDDC39', '#E91E63'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: { boxWidth: 12, padding: 20 }
                        }
                    }
                }
            });
        }
    });
</script>

<style>
    .approved-leave-chart {
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: left;
        padding-left: 20px;
        margin-bottom: 30px;
    }

    .approved-leave-chart .card-box {
        width: 100% !important;
        height: 100% !important;<?php
/**
 * Approved Leave Pie Charts (Single Canvas)
 * 
 * This script fetches the count of approved leave requests per employee 
 * and visualizes them using a single Pie Chart with multiple datasets.
 */

// Database connection assumed to be already established

// Define approved leave status
$statusApproved = 1;
$currentYear = date('Y');

// Fetch approved leave count per employee for leave types 1, 2, 3
$sql1 = "SELECT e.FirstName, COUNT(l.id) AS leave_count 
        FROM tblleave l 
        JOIN tblemployees e ON l.empid = e.emp_id 
        JOIN tblleavetype lt ON l.LeaveType = lt.LeaveType
        WHERE l.RegRemarks = :status 
        AND YEAR(STR_TO_DATE(l.FromDate, '%Y-%m-%d')) = :currentYear
        AND lt.id IN (1, 2, 3) 
        GROUP BY e.FirstName";

$query1 = $dbh->prepare($sql1);
$query1->bindParam(':status', $statusApproved, PDO::PARAM_INT);
$query1->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$query1->execute();
$results1 = $query1->fetchAll(PDO::FETCH_ASSOC);

// Fetch approved leave count per employee for leave type 4
$sql2 = "SELECT e.FirstName, COUNT(l.id) AS leave_count 
        FROM tblleave l 
        JOIN tblemployees e ON l.empid = e.emp_id 
        JOIN tblleavetype lt ON l.LeaveType = lt.LeaveType
        WHERE l.RegRemarks = :status 
        AND YEAR(STR_TO_DATE(l.FromDate, '%Y-%m-%d')) = :currentYear
        AND lt.id = 4
        GROUP BY e.FirstName";

$query2 = $dbh->prepare($sql2);
$query2->bindParam(':status', $statusApproved, PDO::PARAM_INT);
$query2->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$query2->execute();
$results2 = $query2->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for charts
$empNames = [];
$leaveCounts1 = [];
$leaveCounts2 = [];

foreach ($results1 as $row) {
    $empNames[] = $row['FirstName'];
    $leaveCounts1[] = $row['leave_count'];
}

foreach ($results2 as $row) {
    $index = array_search($row['FirstName'], $empNames);
    if ($index !== false) {
        $leaveCounts2[$index] = $row['leave_count'];
    } else {
        $empNames[] = $row['FirstName'];
        $leaveCounts1[] = 0;
        $leaveCounts2[] = $row['leave_count'];
    }
}

// Convert data to JSON for JavaScript usage
$empNamesJson = json_encode($empNames);
$leaveCountsJson1 = json_encode($leaveCounts1);
$leaveCountsJson2 = json_encode($leaveCounts2);
$hasData = !empty($results1) || !empty($results2);
?>

<div class="card-box height-100-p d-flex flex-column justify-content-center align-items-center">
    <h4>Employees Leave <?php echo $currentYear; ?></h4>
    <div class="pb-20">
        <?php if ($hasData): ?>
            <canvas id="approvedLeavePieChart" width="300" height="300"></canvas>
        <?php else: ?>
            <p class="no-data-message">No approved leave records found for <?php echo $currentYear; ?>.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var empNames = <?php echo $empNamesJson; ?>;
        var leaveCounts1 = <?php echo $leaveCountsJson1; ?>;
        var leaveCounts2 = <?php echo $leaveCountsJson2; ?>;

        if (empNames.length === 0) {
            console.log("No data available for the chart.");
            return;
        }

        var ctx = document.getElementById('approvedLeavePieChart').getContext('2d');

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: empNames,
                datasets: [
                    {
                        label: 'Leave Types 1, 2, 3',
                        data: leaveCounts1,
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4CAF50', '#FF9800'],
                        borderWidth: 1
                    },
                    {
                        label: 'Leave Type 4',
                        data: leaveCounts2,
                        backgroundColor: ['#9C27B0', '#3F51B5', '#009688', '#CDDC39', '#E91E63'],
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 12,
                            padding: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (tooltipItem) {
                                let datasetIndex = tooltipItem.datasetIndex;
                                let label = tooltipItem.label || '';
                                let value = tooltipItem.raw || 0;
                                let category = datasetIndex === 0 ? "Leave 1, 2, 3" : "Leave 4";
                                return `${label}: ${value} (${category})`;
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
        height: 280px;
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
        max-width: 300px;
        height: auto;
        max-height: 300px;
        padding: 10px;
    }

    .no-data-message {
        font-size: 16px;
        color: #ff0000;
        text-align: center;
        font-weight: bold;
    }
</style>

        display: flex;
        justify-content: flex-start;
        align-items: center;
        padding-left: 20px;
    }

    .approved-leave-chart canvas {
        width: 100% !important;
        max-width: 300px;
        height: auto;
        max-height: 300px;
        padding: 10px;
    }

    .no-data-message {
        font-size: 16px;
        color: #ff0000;
        text-align: center;
        font-weight: bold;
    }
</style>
