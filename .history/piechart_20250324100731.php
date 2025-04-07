<?php
/**
 * Approved Leave Bar Chart
 * 
 * This script fetches approved leave requests and visualizes them using 
 * a bar chart (Annual Leave & Medical Leave per employee).
 */

// Database connection assumed to be already established

// Define approved leave status
$statusApproved = 1;
$currentYear = date('Y');

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
$leaveData = [];

foreach ($resultsAnnual as $row) {
    $leaveData[$row['FirstName']]['Annual Leave'] = $row['leave_count'];
}

foreach ($resultsMedical as $row) {
    $leaveData[$row['FirstName']]['Medical Leave'] = $row['leave_count'];
}

// Generate datasets
$empNames = array_keys($leaveData);
$leaveCountsAnnual = [];
$leaveCountsMedical = [];

foreach ($leaveData as $employee => $leaveTypes) {
    $leaveCountsAnnual[] = $leaveTypes['Annual Leave'] ?? 0;
    $leaveCountsMedical[] = $leaveTypes['Medical Leave'] ?? 0;
}

// Convert data to JSON for JavaScript usage
$empNamesJson = json_encode($empNames);
$leaveCountsAnnualJson = json_encode($leaveCountsAnnual);
$leaveCountsMedicalJson = json_encode($leaveCountsMedical);
$hasData = !empty($resultsAnnual) || !empty($resultsMedical);
?>

<div class="card-box height-100-p d-flex flex-column justify-content-center align-items-center">
    <h4>Employees Leave (<?php echo $currentYear; ?>)</h4>
    <div class="pb-20">
        <?php if ($hasData): ?>
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
        var empNames = <?php echo $empNamesJson; ?>;
        var leaveCountsAnnual = <?php echo $leaveCountsAnnualJson; ?>;
        var leaveCountsMedical = <?php echo $leaveCountsMedicalJson; ?>;

        if (empNames.length === 0) {
            console.log("No data available for the chart.");
            return;
        }

        var ctx = document.getElementById('approvedLeaveBarChart').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: empNames,
                datasets: [
                    {
                        label: 'Annual Leave',
                        data: leaveCountsAnnual,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Medical Leave',
                        data: leaveCountsMedical,
                        backgroundColor: 'rgba(255, 99, 132, 0.7)',
                        borderColor: 'rgba(255, 99, 132, 1)',
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
                        },
                        ticks: {
                            autoSkip: false,
                            maxRotation: 45,
                            minRotation: 45
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
                    }
                }
            }
        });
    });
</script>

<style>
    .card-box {
        width: 100%;
        max-width: 700px;
        overflow: hidden;
        padding: 20px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    }

    #approvedLeaveBarChart {
        width: 100% !important;
        height: 300px !important;
    }

    .no-data-message {
        font-size: 16px;
        color: #ff0000;
        text-align: center;
        font-weight: bold;
    }
</style>
