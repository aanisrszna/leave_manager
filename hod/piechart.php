<?php
// Fetch Approved Leave count per Employee with First Name
$statusApproved = 1;
$sql = "SELECT e.FirstName, COUNT(l.id) AS leave_count 
        FROM tblleave l 
        JOIN tblemployees e ON l.empid = e.emp_id 
        WHERE l.RegRemarks = :status 
        GROUP BY e.FirstName";

$query = $dbh->prepare($sql);
$query->bindParam(':status', $statusApproved, PDO::PARAM_INT);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_ASSOC);

$empNames = [];
$leaveCounts = [];

foreach ($results as $row) {
    $empNames[] = $row['FirstName'];
    $leaveCounts[] = $row['leave_count'];
}
?>

<div class="card-box height-100-p d-flex flex-column justify-content-center align-items-center">
    <div class="pb-20">
        <canvas id="approvedLeavePieChart" width="300" height="300"></canvas> <!-- Increased size -->
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var ctx = document.getElementById('approvedLeavePieChart').getContext('2d');

        var approvedLeaveChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($empNames); ?>,
                datasets: [{
                    data: <?php echo json_encode($leaveCounts); ?>,
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
        height: 280px;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        text-align: left;
        padding-left: 20px;
        margin-bottom: 30px;
    }

    .approved-leave-chart .card-box {
        width: 100% !important;
        height: 100%  !important;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        padding-left: 20px;
    }

    .approved-leave-chart canvas {
        width: 100% !important;
        max-width:300 px; /* Increased max width */
        height: auto;
        max-height: 300px; /* Increased max height */
        padding: 10px;
    }

    .approved-leave-chart {
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
