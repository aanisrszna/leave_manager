<?php
/**
 * Approved Leave Pie Charts (Nested Doughnut)
 * 
 * This script fetches approved leave requests and visualizes them using 
 * a nested doughnut chart (Annual Leave = Outer, Medical Leave = Inner).
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
$empNamesAnnual = [];
$leaveCountsAnnual = [];
$empNamesMedical = [];
$leaveCountsMedical = [];

foreach ($resultsAnnual as $row) {
    $empNamesAnnual[] = $row['FirstName'];
    $leaveCountsAnnual[] = $row['leave_count'];
}

foreach ($resultsMedical as $row) {
    $empNamesMedical[] = $row['FirstName'];
    $leaveCountsMedical[] = $row['leave_count'];
}

// Convert data to JSON for JavaScript usage
$empNamesAnnualJson = json_encode($empNamesAnnual);
$leaveCountsAnnualJson = json_encode($leaveCountsAnnual);
$empNamesMedicalJson = json_encode($empNamesMedical);
$leaveCountsMedicalJson = json_encode($leaveCountsMedical);
$hasData = !empty($resultsAnnual) || !empty($resultsMedical);
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
        var empNamesAnnual = <?php echo $empNamesAnnualJson; ?>;
        var leaveCountsAnnual = <?php echo $leaveCountsAnnualJson; ?>;
        var empNamesMedical = <?php echo $empNamesMedicalJson; ?>;
        var leaveCountsMedical = <?php echo $leaveCountsMedicalJson; ?>;

        if (empNamesAnnual.length === 0 && empNamesMedical.length === 0) {
            console.log("No data available for the chart.");
            return;
        }

        var ctx = document.getElementById('approvedLeavePieChart').getContext('2d');

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: [...empNamesAnnual, ...empNamesMedical],
                datasets: [
                    {
                        label: 'Annual Leave',
                        data: [...leaveCountsAnnual, ...leaveCountsMedical.map(() => 0)], // Outer ring
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4CAF50', '#FF9800'],
                        borderWidth: 1
                    },
                    {
                        label: 'Medical Leave',
                        data: [...leaveCountsAnnual.map(() => 0), ...leaveCountsMedical], // Inner ring
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4CAF50', '#FF9800'],
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutoutPercentage: 50, // Creates a nested effect
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
                                let category = datasetIndex === 0 ? "Annual Leave" : "Medical Leave";
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
