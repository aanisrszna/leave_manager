<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>My Attendance</title>

    <!-- Favicon and CSS -->
    <link rel="apple-touch-icon" sizes="180x180" href="../vendors/images/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../vendors/images/favicon-32x32.png">
    <link rel="stylesheet" type="text/css" href="../vendors/styles/core.css">
    <link rel="stylesheet" type="text/css" href="../vendors/styles/style.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/datatables/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/datatables/css/responsive.bootstrap4.min.css">

    <!-- Custom Styles -->
    <style>
        .attendance-card {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 8px;
            background-color: #f9f9f9;
            text-align: center;
        }
        .attendance-card h1 {
            margin: 0;
            font-size: 3rem;
            color: #333;
        }
        .attendance-card p {
            font-size: 1.2rem;
            color: #555;
        }
        .tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .tab-button {
            padding: 10px 20px;
            margin: 0 5px;
            border: none;
            border-bottom: 3px solid transparent;
            background: none;
            cursor: pointer;
            font-size: 1rem;
            color: #333;
        }
        .tab-button.active {
            border-bottom: 3px solid #007bff;
            color: #007bff;
        }
        .form-control {
            margin: 10px 0;
        }
        .btn {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    	<?php include('includes/navbar.php')?>

	<?php include('includes/right_sidebar.php')?>

	<?php include('includes/left_sidebar.php')?>

	<div class="mobile-menu-overlay"></div>
    <?php
    include('../includes/config.php');
    include('../includes/session.php');

    // Handle Clock In/Clock Out
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $staff_id = $_POST['staff_id'];
        $date = date('Y-m-d');
        $time = date('H:i:s');
        $remark = $_POST['remark'] ?? '';

        if (isset($_POST['clock_in'])) {
            $conn->query("INSERT INTO tblattendance (staff_id, date, time_in, remark) 
                          VALUES ('$staff_id', '$date', '$time', '$remark')
                          ON DUPLICATE KEY UPDATE time_in = '$time', remark = '$remark'");
        }

        if (isset($_POST['clock_out'])) {
            $conn->query("UPDATE tblattendance 
                          SET time_out = '$time', total_hours = TIMESTAMPDIFF(SECOND, time_in, '$time') 
                          WHERE staff_id = '$staff_id' AND date = '$date'");
        }
    }

    // Fetch Attendance Data
    $date = date('Y-m-d');
    $result = $conn->query("SELECT * FROM tblattendance WHERE date = '$date'");
    $attendance_data = $result->fetch_all(MYSQLI_ASSOC);
    ?>

    <div class="container mt-5">
        <h2 class="text-center mb-4">My Attendance</h2>
        <div class="row">
            <!-- Clock In/Out Section -->
            <div class="col-md-6">
                <div class="attendance-card">
                    <div id="current-time">
                        <h1 id="time">00:00:00</h1>
                        <p id="date"><?= date('l, F j, Y'); ?></p>
                    </div>
                    <div class="tabs">
                        <button class="tab-button active" onclick="switchTab('clock-in')">Clock In</button>
                        <button class="tab-button" onclick="switchTab('clock-out')">Clock Out</button>
                    </div>
                    <form method="POST" id="clock-in-form">
                        <input type="hidden" name="staff_id" value="<?= $_SESSION['user_id']; ?>">
                        <input type="text" name="remark" class="form-control" placeholder="Remark (Optional)">
                        <button type="submit" name="clock_in" class="btn btn-primary btn-block">Confirm</button>
                    </form>
                    <form method="POST" id="clock-out-form" style="display: none;">
                        <input type="hidden" name="staff_id" value="<?= $_SESSION['user_id']; ?>">
                        <button type="submit" name="clock_out" class="btn btn-success btn-block">Confirm</button>
                    </form>
                </div>
            </div>

            <!-- Attendance Records Section -->
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
                    <tbody>
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
                                <td>
                                    <?= $row['time_out'] ? 'Out' : 'In'; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($attendance_data)): ?>
                            <tr>
                                <td colspan="5" class="text-center">No records found for today.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Scripts -->
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
