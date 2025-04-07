<?php
// Fetch data from database
$currentYear = date("Y");
$hasData = false; // Flag to check if there's any data
$employeeNames = [];
$leaveCountsAnnual = [];
$leaveCountsMedical = [];

// Example: Fetch data from the database (Replace with your actual query)
$sql = "SELECT emp.Name, 
        SUM(CASE WHEN leaveType = 'Annual Leave' THEN 1 ELSE 0 END) AS AnnualLeave,
        SUM(CASE WHEN leaveType = 'Medical Leave' THEN 1 ELSE 0 END) AS MedicalLeave
        FROM tblleave l
        JOIN tblemployees emp ON l.empid = emp.emp_id
        WHERE YEAR(l.leaveDate) = ?
        GROUP BY emp.Name";
$stmt->bind_param("s", $currentYear);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $hasData = true;
    $employeeNames[] = $row['Name'];
    $leaveCountsAnnual[] = (int)$row['AnnualLeave'];
    $leaveCountsMedical[] = (int)$row['MedicalLeave'];
}
$stmt->close();
$conn->close();

$employeeNamesJson = json_encode($employeeNames);
$leaveCountsAnnualJson = json_encode($leaveCountsAnnual);
$leaveCountsMedicalJson = json_encode($leaveCountsMedical);
?>

<div class="card-box height-100-p d-flex flex-column justify-content-center align-items-center">
    <h4>Employees Leave (<?php echo $currentYear; ?>)</h4>
    <div class="pb-20">
        <?php if ($hasData): ?>
            <canvas id="approvedLeaveBarChart" width="400" height="300"></canvas>
        <?php else: ?>
            <p class="no-data-message">No approved leave records found for <?php echo $currentYear; ?>.</p>
        <?php endif; ?>
    </div>
</div>

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
                        backgroundColor: '#36A2EB',
                        borderWidth: 1,
                        maxBarThickness: 20
                    },
                    {
                        label: 'Medical Leave',
                        data: leaveCountsMedical,
                        backgroundColor: '#FF6384',
                        borderWidth: 1,
                        maxBarThickness: 20
                    }
                ]
            },
            options: {
                responsive: false,
                maintainAspectRatio: true,
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
        height: 300px;
        text-align: left;
        padding-left: 10px;
        margin-bottom: 20px;
    }

    .approved-leave-chart canvas {
        width: 100% !important;
        max-width: 400px;
        height: auto;
        max-height: 300px;
        padding: 5px;
    }

    .no-data-message {
        font-size: 14px;
        color: #ff0000;
        text-align: center;
        font-weight: bold;
    }
</style>
