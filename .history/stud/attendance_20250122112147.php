<?php
// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "attendance_db";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Clock In
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clock_in'])) {
    $staff_id = intval($_POST['staff_id']);
    $date = date('Y-m-d');
    $time_in = date('H:i:s');
    $remark = $conn->real_escape_string($_POST['remark']);

    // Insert or update clock-in data
    $sql = "INSERT INTO tblattendance (staff_id, date, time_in, remark)
            VALUES ('$staff_id', '$date', '$time_in', '$remark')
            ON DUPLICATE KEY UPDATE time_in = '$time_in', remark = '$remark'";
    $conn->query($sql);
}

// Handle Clock Out
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clock_out'])) {
    $staff_id = intval($_POST['staff_id']);
    $date = date('Y-m-d');
    $time_out = date('H:i:s');

    // Update clock-out data and calculate total hours
    $sql = "UPDATE tblattendance
            SET time_out = '$time_out',
                total_hours = TIMESTAMPDIFF(HOUR, time_in, '$time_out')
            WHERE staff_id = '$staff_id' AND date = '$date'";
    $conn->query($sql);
}

// Fetch attendance for today
$date = date('Y-m-d');
$result = $conn->query("SELECT * FROM tblattendance WHERE date = '$date'");
$attendance_data = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: space-between;
            margin: 20px;
        }
        .form-container, .table-container {
            width: 45%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Clock In/Clock Out</h2>
        <form method="POST">
            <label for="staff_id">Staff ID:</label><br>
            <input type="number" name="staff_id" id="staff_id" required><br><br>

            <label for="remark">Remark (optional):</label><br>
            <input type="text" name="remark" id="remark"><br><br>

            <button type="submit" name="clock_in">Clock In</button>
            <button type="submit" name="clock_out">Clock Out</button>
        </form>
    </div>

    <div class="table-container">
        <h2>Today's Attendance</h2>
        <table>
            <thead>
                <tr>
                    <th>Staff ID</th>
                    <th>Date</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Total Hours</th>
                    <th>Remark</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($attendance_data)): ?>
                    <?php foreach ($attendance_data as $row): ?>
                        <tr>
                            <td><?= $row['staff_id'] ?></td>
                            <td><?= $row['date'] ?></td>
                            <td><?= $row['time_in'] ?: '-' ?></td>
                            <td><?= $row['time_out'] ?: '-' ?></td>
                            <td><?= $row['total_hours'] ?: '-' ?></td>
                            <td><?= $row['remark'] ?: '-' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No attendance records found for today.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
