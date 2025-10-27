<?php

// Approved status
$statusApproved = 1;
$currentYear = date('Y');

/**
 * 1) Build dynamic buckets from tblleavetype where IsDisplay='Yes'
 *    - Anything matching "medical" or "hospital" -> Medical bucket
 *    - Everything else -> Annual bucket
 *    (Adjust the pattern rules below anytime you need finer control,
 *     but no more hard-coded ID arrays in the chart code.)
 */
$typeSql = "SELECT id, LeaveType FROM tblleavetype WHERE IsDisplay = 'Yes'";
$typeStmt = $dbh->prepare($typeSql);
$typeStmt->execute();
$typeRows = $typeStmt->fetchAll(PDO::FETCH_ASSOC);

// Dynamic ID sets
$annualTypeIds = [];
$medicalTypeIds = [];

foreach ($typeRows as $t) {
    $id   = (int)$t['id'];
    $name = strtolower(trim($t['LeaveType']));

    // Heuristic to classify displayed types
    if (preg_match('/medical|hospital/i', $name)) {
        $medicalTypeIds[] = $id;
    } else {
        $annualTypeIds[] = $id;
    }
}

// If nothing is set to display, keep arrays empty (chart will show 0s)
$annualTypeIds = array_unique($annualTypeIds);
$medicalTypeIds = array_unique($medicalTypeIds);

/**
 * 2) Fetch approved leave data (same query shape you had)
 */
$sql = "SELECT e.emp_id, e.LastName, lt.id AS leave_type, COALESCE(SUM(l.RequestedDays), 0) AS leave_count
        FROM tblemployees e
        LEFT JOIN tblleave l ON l.empid = e.emp_id AND l.RegRemarks = :status 
            AND YEAR(STR_TO_DATE(l.FromDate, '%Y-%m-%d')) = :currentYear
        LEFT JOIN tblleavetype lt ON l.LeaveType = lt.LeaveType
        WHERE e.Role NOT IN ('Director', 'Admin') AND e.Status != 'Inactive' -- Exclude Director, Admin, and Inactive employees
        GROUP BY e.emp_id, e.LastName, lt.id
        ORDER BY e.emp_id";

$query = $dbh->prepare($sql);
$query->bindParam(':status', $statusApproved, PDO::PARAM_INT);
$query->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_ASSOC);

// Process (same structure you had), but use the dynamic ID sets above
$employees = [];
$leaveCountsAnnual = [];
$leaveCountsMedical = [];

foreach ($results as $row) {
    $name = $row['LastName'];
    $leaveType = isset($row['leave_type']) ? intval($row['leave_type']) : 0;
    $leaveDays = floatval($row['leave_count']);

    if (!isset($employees[$name])) {
        $employees[$name] = $name;
        $leaveCountsAnnual[$name] = 0;
        $leaveCountsMedical[$name] = 0;
    }

    // Dynamic classification: no hard-coded IDs anymore
    if ($leaveType && in_array($leaveType, $annualTypeIds, true)) {
        $leaveCountsAnnual[$name] += $leaveDays;
    }

    if ($leaveType && in_array($leaveType, $medicalTypeIds, true)) {
        $leaveCountsMedical[$name] += $leaveDays;
    }
}

// Ensure all employees have values for both leave types
foreach ($employees as $name) {
    $leaveCountsAnnual[$name] = $leaveCountsAnnual[$name] ?? 0;
    $leaveCountsMedical[$name] = $leaveCountsMedical[$name] ?? 0;
}

// Encode for chart (unchanged)
$employeeNamesJson = json_encode(array_values($employees), JSON_UNESCAPED_UNICODE);
$leaveCountsAnnualJson = json_encode(array_values($leaveCountsAnnual));
$leaveCountsMedicalJson = json_encode(array_values($leaveCountsMedical));

?>

<div class="card-box height-100-p d-flex flex-column justify-content-center align-items-center">
    <h5>Summary of Employees Leave Taken <?php echo $currentYear; ?></h5>
    <div class="chart-container">
        <?php if (!empty($employees)): ?>
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
        var employeeNames = <?php echo $employeeNamesJson; ?>;
        var leaveCountsAnnual = <?php echo $leaveCountsAnnualJson; ?>;
        var leaveCountsMedical = <?php echo $leaveCountsMedicalJson; ?>;

        console.log("Annual Leave Data:", leaveCountsAnnual);
        console.log("Medical Leave Data:", leaveCountsMedical);

        if (employeeNames.length === 0) {
            return;
        }

        var ctx = document.getElementById('approvedLeaveBarChart').getContext('2d');

        // Destroy existing chart instance if it exists
        if (window.myBarChart) {
            window.myBarChart.destroy();
        }

        // Create new chart instance (same structure, labels unchanged)
        window.myBarChart = new Chart(ctx, {
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
                animation: false, // Disable all animations
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
    .no-data-message {
        font-size: 14px;
        color: #ff0000;
        text-align: center;
        font-weight: bold;
    }
</style>
