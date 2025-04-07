<?php
// Include config and session for database connection and authentication
include('../includes/config.php');
include('../includes/session.php');

// Handle clock-in and clock-out submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staff_id = $_SESSION['alogin'];
    $remark = isset($_POST['remark']) ? $_POST['remark'] : null;

    if (isset($_POST['clock_in'])) {
        $date = date('Y-m-d');
        $time_in = date('H:i:s');
        $query = "INSERT INTO tblattendance (staff_id, date, time_in, remark) VALUES ('$staff_id', '$date', '$time_in', '$remark')";
        mysqli_query($conn, $query);
    } elseif (isset($_POST['clock_out'])) {
        $date = date('Y-m-d');
        $time_out = date('H:i:s');
        $query = "UPDATE tblattendance SET time_out = '$time_out' WHERE staff_id = '$staff_id' AND date = '$date' AND time_out IS NULL";
        mysqli_query($conn, $query);
    }
}

// Fetch attendance records
$staff_id = $_SESSION['alogin'];
$query = "SELECT * FROM tblattendance WHERE staff_id = '$staff_id' ORDER BY date DESC";
$result = mysqli_query($conn, $query);

$attendance_records = [];
while ($row = mysqli_fetch_assoc($result)) {
    $attendance_records[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Attendance</title>
    <link rel="stylesheet" href="../vendors/styles/core.css">
    <link rel="stylesheet" href="../vendors/styles/style.css">
    <link rel="stylesheet" href="../src/plugins/datatables/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../src/plugins/datatables/css/responsive.bootstrap4.min.css">
    <style>
        .attendance-card { border: 1px solid #ddd; padding: 20px; border-radius: 8px; background-color: #f9f9f9; text-align: center; }
        .attendance-card h1 { margin: 0; font-size: 3rem; color: #333; }
        .attendance-card p { font-size: 1.2rem; color: #555; }
        .tabs { display: flex; justify-content: center; margin-bottom: 20px; }
        .tab-button { padding: 10px 20px; margin: 0 5px; border: none; border-bottom: 3px solid transparent; background: none; cursor: pointer; font-size: 1rem; color: #333; }
        .tab-button.active { border-bottom: 3px solid #007bff; color: #007bff; }
        .form-control { margin: 10px 0; }
        .btn { margin-top: 10px; }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">My Attendance</h2>
    <div class="row">
        <div class="col-md-6">
            <div class="attendance-card">
                <div id="current-time">
                    <h1 id="time">00:00:00</h1>
                    <p id="date"><?php echo date('l, F j, Y'); ?></p>
                </div>
                <div class="tabs">
                    <button class="tab-button active" onclick="switchTab('clock-in')">Clock In</button>
                    <button class="tab-button" onclick="switchTab('clock-out')">Clock Out</button>
                </div>
                <form method="POST" id="clock-in-form">
                    <input type="hidden" name="staff_id" value="<?php echo $_SESSION['alogin']; ?>">
                    <input type="text" name="remark" class="form-control" placeholder="Remark (Optional)">
                    <button type="submit" name="clock_in" class="btn btn-primary btn-block">Clock In</button>
                </form>
                <form method="POST" id="clock-out-form" style="display: none;">
                    <input type="hidden" name="staff_id" value="<?php echo $_SESSION['alogin']; ?>">
                    <button type="submit" name="clock_out" class="btn btn-success btn-block">Clock Out</button>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            <h4>Attendance Records</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Total Hours</th>
                        <th>Status (In/Out)</th>
                    </tr>
                </thead>
                <tbody id="attendance-records">
                    <?php if (count($attendance_records) > 0): ?>
                        <?php foreach ($attendance_records as $record): ?>
                            <?php
                            // Convert UTC time to Asia/Kuala_Lumpur timezone
                            $time_in = new DateTime($record['time_in'], new DateTimeZone('UTC'));
                            $time_in->setTimezone(new DateTimeZone('Asia/Kuala_Lumpur'));
                            $formatted_time_in = $time_in->format('H:i:s');
                            
                            $time_out = new DateTime($record['time_out'], new DateTimeZone('UTC'));
                            $time_out->setTimezone(new DateTimeZone('Asia/Kuala_Lumpur'));
                            $formatted_time_out = $time_out->format('H:i:s');
                            ?>
                            <tr>
                                <td><?php echo $record['date']; ?></td>
                                <td><?php echo $formatted_time_in ?: '-'; ?></td>
                                <td><?php echo $formatted_time_out ?: '-'; ?></td>
                                <td><?php echo $record['total_hours'] ?: '-'; ?></td>
                                <td><?php echo $record['time_out'] ? 'Out' : 'In'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No records found for today.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>






            </table>
        </div>
    </div>
</div>

<script>
    // Update current time
    function updateTime() {
        const now = new Date();
        document.getElementById('time').textContent = now.toLocaleTimeString();
    }
    setInterval(updateTime, 1000);

    // Switch between Clock In and Clock Out tabs
    function switchTab(tab) {
        document.getElementById('clock-in-form').style.display = tab === 'clock-in' ? 'block' : 'none';
        document.getElementById('clock-out-form').style.display = tab === 'clock-out' ? 'block' : 'none';
        document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
        document.querySelector(`.tab-button[onclick="switchTab('${tab}')"]`).classList.add('active');
    }
</script>
</body>
</html>
