<?php
/**
 * Approved Leave Pie Chart
 * 
 * This script fetches the count of approved leave requests per employee 
 * and visualizes it using a Pie Chart with Chart.js.
 */

// Database connection assumed to be already established

// Define approved leave status
$statusApproved = 1;

// Fetch approved leave count per employee with first name
$sql = "SELECT e.FirstName, COUNT(l.id) AS leave_count 
        FROM tblleave l 
        JOIN tblemployees e ON l.empid = e.emp_id 
        WHERE l.RegRemarks = :status 
        GROUP BY e.FirstName";

$query = $dbh->prepare($sql);
$query->bindParam(':status', $statusApproved, PDO::PARAM_INT);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for chart
$empNames = [];
$leaveCounts = [];

foreach ($results as $row) {
    $empNames[] = $row['FirstName'];
    $leaveCounts[] = $row['leave_count'];
}

// Convert data to JSON for JavaScript usage
$empNamesJson = json_encode($empNames);
$leaveCountsJson = json_encode($leaveCounts);
$hasData = !empty($results); // Check if there is data
?>

<div class="card-box height-100-p d-flex flex-column justify-content-center align-items-center">
    <div class="pb-20">
        <?php if ($hasData): ?>
            <canvas id="approvedLeavePieChart" width="300" height="300"></canvas>
        <?php else: ?>
            <p class="no-data-message">No approved leave records found.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<scrip
        var ctx = document.getElementById('approvedLeavePieChart').getContext('2d');

        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: empNames,
                datasets: [{
                    data: leaveCounts,
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4CAF50', '#FF9800', 
                        '#9C27B0', '#3F51B5', '#009688', '#CDDC39', '#E91E63'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        align: 'center',
                        labels: {
                            boxWidth: 12,
                            padding: 20
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
