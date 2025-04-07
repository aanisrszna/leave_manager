<?php
include('../includes/config.php');
include('../includes/session.php');

// Ensure the user is logged in and has an emp_id
if (!isset($_SESSION['emp_id'])) {
    header('Location: ../attendance.php');
    exit();
}

$emp_id = $_SESSION['emp_id'];

// Handle Clock In/Clock Out
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = date('Y-m-d');
    $time = date('H:i:s');
    $remark = $_POST['remark'] ?? '';

    if (isset($_POST['clock_in'])) {
        $conn->query("INSERT INTO tblattendance (staff_id, date, time_in, remark) 
                      VALUES ('$emp_id', '$date', '$time', '$remark')
                      ON DUPLICATE KEY UPDATE time_in = '$time', remark = '$remark'");
    }

    if (isset($_POST['clock_out'])) {
        $conn->query("UPDATE tblattendance 
                      SET time_out = '$time', total_hours = TIMESTAMPDIFF(SECOND, time_in, '$time') 
                      WHERE staff_id = '$emp_id' AND date = '$date'");
    }
}

// Fetch today's attendance for the logged-in user
$date = date('Y-m-d');
$result = $conn->query("SELECT * FROM tblattendance WHERE staff_id = '$emp_id' AND date = '$date'");
$attendance_data = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>My Attendance</title>
    <!-- Include your CSS/JS as required -->
</head>
<body>
    <div class="container">
        <h2>My Attendance</h2>
        <div class="row">
            <div class="col-md-6">
                <!-- Clock In/Clock Out Section -->
                <form method="POST">
                    <div id="current-time">
                        <h1 id="time">00:00:00</h1>
                        <p><?= date('l, F j, Y'); ?></p>
                    </div>
                    <input type="text" name="remark" class="form-control" placeholder="Remark (Optional)">
                    <button type="submit" name="clock_in" class="btn btn-primary">Clock In</button>
                    <button type="submit" name="clock_out" class="btn btn-success">Clock Out</button>
                </form>
            </div>
            <div class="col-md-6">
                <!-- Attendance Records -->
                <h4>Attendance Records</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Total Hours</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($attendance_data)): ?>
                            <?php foreach ($attendance_data as $row): ?>
                                <tr>
                                    <td><?= date('M d, Y', strtotime($row['date'])); ?></td>
                                    <td><?= $row['time_in'] ?: '-'; ?></td>
                                    <td><?= $row['time_out'] ?: '-'; ?></td>
                                    <td>
                                        <?php
                                        if ($row['total_hours']) {
                                            $hours = floor($row['total_hours'] / 3600);
                                            $minutes = floor(($row['total_hours'] % 3600) / 60);
                                            $seconds = $row['total_hours'] % 60;
                                            echo sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                    <td><?= $row['time_out'] ? 'Out' : 'In'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No records found for today.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        // Update the clock in real-time
        function updateTime() {
            const now = new Date();
            document.getElementById('time').textContent = now.toLocaleTimeString();
        }
        setInterval(updateTime, 1000);
    </script>
</body>
</html>
