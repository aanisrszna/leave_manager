<div class="card-box height-100-p d-flex flex-column justify-content-center align-items-center">
    <h4>Employees Leave <?php echo $currentYear; ?></h4>
    <div class="pb-20">
        <?php if ($hasData): ?>
            <canvas id="approvedLeaveBarChart" width="400" height="300"></canvas>
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

        // Combine unique employee names
        var allEmployees = [...new Set([...empNamesAnnual, ...empNamesMedical])];
        
        // Create data mapping for annual and medical leave
        var leaveDataAnnual = allEmployees.map(name => {
            let index = empNamesAnnual.indexOf(name);
            return index !== -1 ? leaveCountsAnnual[index] : 0;
        });
        
        var leaveDataMedical = allEmployees.map(name => {
            let index = empNamesMedical.indexOf(name);
            return index !== -1 ? leaveCountsMedical[index] : 0;
        });

        var ctx = document.getElementById('approvedLeaveBarChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: allEmployees,
                datasets: [
                    {
                        label: 'Annual Leave',
                        data: leaveDataAnnual,
                        backgroundColor: '#36A2EB',
                        borderColor: '#36A2EB',
                        borderWidth: 1
                    },
                    {
                        label: 'Medical Leave',
                        data: leaveDataMedical,
                        backgroundColor: '#FF6384',
                        borderColor: '#FF6384',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Leave Days'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Employee Name'
                        }
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
    .approved-leave-chart {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 280px;
        text-align: left;
        padding-left: 20px;
        margin-bottom: 30px;
    }

    .approved-leave-chart canvas {
        width: 100% !important;
        max-width: 500px;
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
