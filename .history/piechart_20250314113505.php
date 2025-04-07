<script>
    document.addEventListener("DOMContentLoaded", function () {
        var empNames = <?php echo $empNamesJson; ?>;
        var leaveCounts = <?php echo $leaveCountsJson; ?>;

        if (empNames.length === 0) {
            console.log("No data available for the chart.");
            return; // Stop execution if there's no data
        }

        var ctx = document.getElementById('approvedLeaveBarChart').getContext('2d');

        new Chart(ctx, {
            type: 'bar', // Change from 'pie' to 'bar'
            data: {
                labels: empNames,
                datasets: [{
                    label: 'Approved Leave Count',
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
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1 // Ensure whole numbers
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false // Hide legend for bar chart
                    }
                }
            }
        });
    });
</script>

<div class="card-box height-100-p d-flex flex-column justify-content-center align-items-center">
    <h4>Employees Leave <?php echo $currentYear; ?></h4>
    <div class="pb-20">
        <?php if ($hasData): ?>
            <canvas id="approvedLeaveBarChart" width="300" height="300"></canvas> <!-- Updated canvas ID -->
        <?php else: ?>
            <p class="no-data-message">No approved leave records found for <?php echo $currentYear; ?>.</p>
        <?php endif; ?>
    </div>
</div>
