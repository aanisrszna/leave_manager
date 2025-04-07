<?php
$currentYear = date('Y');

// Static employee leave data
$employeeNames = ['Ali', 'Fatimah', 'John', 'Siti'];
$leaveCountsAnnual = [5, 8, 2, 10];
$leaveCountsMedical = [2, 1, 3, 4];

$employeeNamesJson = json_encode($employeeNames);
$leaveCountsAnnualJson = json_encode($leaveCountsAnnual);
$leaveCountsMedicalJson = json_encode($leaveCountsMedical);
?>

<div class="card-box height-100-p d-flex flex-column justify-content-center align-items-center">
    <h5>Employees Leave <?php echo $currentYear; ?></h5>
    <div class="chart-container">
        <canvas id="approvedLeaveBarChart"></canvas>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var employeeNames = <?php echo $employeeNamesJson; ?>;
        var leaveCountsAnnual = <?php echo $leaveCountsAnnualJson; ?>;
        var leaveCountsMedical = <?php echo $leaveCountsMedicalJson; ?>;

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
    }
</style>
