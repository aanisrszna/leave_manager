<?php
include('../includes/config.php');
include('../includes/session.php');
date_default_timezone_set('Asia/Kuala_Lumpur');

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
    <title>Interactive Attendance Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .attendance-card { 
            border: 1px solid #ddd; 
            padding: 20px; 
            border-radius: 12px; 
            background-color: #f1f7ff;
            box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.1);
        }
        .attendance-card h1 { font-size: 3rem; color: #007bff; }
        .tab-button { 
            border: none; 
            padding: 12px 20px; 
            cursor: pointer; 
            background: transparent; 
            font-size: 1rem; 
        }
        .tab-button.active { 
            color: #007bff; 
            font-weight: bold;
            border-bottom: 3px solid #007bff;
        }
        .status-badge {
            font-size: 0.9rem;
            padding: 4px 8px;
            border-radius: 5px;
            color: white;
        }
        .badge-in { background-color: #28a745; }
        .badge-out { background-color: #dc3545; }
    </style>
</head>
<body>
    <?php include('includes/navbar.php') ?>


<div class="container mt-5">
    <h2 class="text-center mb-4">Interactive Attendance Tracker</h2>
    
    <div class="row">
        <div class="col-md-6">
            <div class="attendance-card text-center">
                <h1 id="current-time">00:00:00</h1>
                <p id="current-date"><?php echo date('l, F j, Y'); ?></p>

                <div class="d-flex justify-content-center mb-3">
                    <button class="tab-button active" onclick="switchTab('clock-in')">Clock In</button>
                    <button class="tab-button" onclick="switchTab('clock-out')">Clock Out</button>
                </div>

                <form method="POST" id="clock-in-form">
                    <input type="text" name="remark" class="form-control mb-3" placeholder="Remark (Optional)">
                    <button type="submit" name="clock_in" class="btn btn-primary w-100">Clock In</button>
                </form>

                <form method="POST" id="clock-out-form" style="display: none;">
                    <button type="submit" name="clock_out" class="btn btn-success w-100">Clock Out</button>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            <h4>Attendance Records</h4>
            <table class="table table-bordered table-hover">
                <thead class="table-primary">
                    <tr>
                        <th>Date</th>
                        <th>Time In</th>
                        <th>Status</th>
                        <th>Time Out</th>
                        <th>Status</th>
                        <th>Total Hours</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($attendance_records) > 0): ?>
                        <?php foreach ($attendance_records as $record): ?>
                            <tr>
                                <td><?php echo $record['date']; ?></td>
                                <td><?php echo $record['time_in'] ?: '-'; ?></td>
                                <td>
                                    <span class="status-badge badge-in">
                                        <?php echo $record['time_in'] ? 'Clocked In' : '-'; ?>
                                    </span>
                                </td>
                                <td><?php echo $record['time_out'] ?: '-'; ?></td>
                                <td>
                                    <span class="status-badge badge-out">
                                        <?php echo $record['time_out'] ? 'Clocked Out' : '-'; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    if ($record['time_in'] && $record['time_out']) {
                                        $time_in = new DateTime($record['time_in']);
                                        $time_out = new DateTime($record['time_out']);
                                        $interval = $time_in->diff($time_out);
                                        echo $interval->format('%h hours %i minutes');
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function updateTime() {
        const now = new Date();
        document.getElementById('current-time').textContent = now.toLocaleTimeString();
    }
    setInterval(updateTime, 1000);

    function switchTab(tab) {
        document.getElementById('clock-in-form').style.display = tab === 'clock-in' ? 'block' : 'none';
        document.getElementById('clock-out-form').style.display = tab === 'clock-out' ? 'block' : 'none';
        document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
        document.querySelector(`.tab-button[onclick="switchTab('${tab}')"]`).classList.add('active');
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
