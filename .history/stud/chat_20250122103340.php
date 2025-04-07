<!DOCTYPE html>
<html>
<head>
    <!-- Basic Page Info -->
    <meta charset="utf-8">
    <title>ACI Leave System - Attendance</title>

    <!-- Site favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="../vendors/images/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../vendors/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../vendors/images/favicon-16x16.png">

    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="../vendors/styles/core.css">
    <link rel="stylesheet" type="text/css" href="../vendors/styles/icon-font.min.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/jquery-steps/jquery.steps.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/datatables/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/datatables/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="../vendors/styles/style.css">

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-119386393-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', 'UA-119386393-1');
    </script>
</head>
<body>
    <?php
    include('../includes/config.php');
    include('../includes/session.php');

    // Handle Clock In/Clock Out
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $staff_id = intval($_POST['staff_id']);
        $date = date('Y-m-d');
        $time = date('H:i:s');

        if (isset($_POST['clock_in'])) {
            $remark = isset($_POST['remark']) ? $conn->real_escape_string($_POST['remark']) : null;
            $sql = "INSERT INTO tblattendance (staff_id, date, time_in, remark)
                    VALUES ('$staff_id', '$date', '$time', '$remark')
                    ON DUPLICATE KEY UPDATE time_in = '$time', remark = '$remark'";
            $conn->query($sql);
        }

        if (isset($_POST['clock_out'])) {
            $sql = "UPDATE tblattendance
                    SET time_out = '$time',
                        total_hours = TIMESTAMPDIFF(HOUR, time_in, '$time')
                    WHERE staff_id = '$staff_id' AND date = '$date'";
            $conn->query($sql);
        }
    }

    // Fetch today's attendance
    $date = date('Y-m-d');
    $result = $conn->query("SELECT * FROM tblattendance WHERE date = '$date'");
    $attendance_data = $result->fetch_all(MYSQLI_ASSOC);
    ?>

    <div class="container">
        <div class="row">
            <!-- Form Section -->
            <div class="col-md-6">
                <h4>Clock In/Clock Out</h4>
                <form method="POST">
                    <div class="form-group">
                        <label for="staff_id">Staff ID</label>
                        <input type="number" name="staff_id" id="staff_id" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="remark">Remark (optional)</label>
                        <input type="text" name="remark" id="remark" class="form-control">
                    </div>
                    <button type="submit" name="clock_in" class="btn btn-primary">Clock In</button>
                    <button type="submit" name="clock_out" class="btn btn-success">Clock Out</button>
                </form>
            </div>

            <!-- Attendance Table -->
            <div class="col-md-6">
                <h4>Today's Attendance</h4>
                <table class="table table-striped">
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
        </div>
    </div>

    <!-- Scripts -->
    <script src="../vendors/scripts/core.js"></script>
    <script src="../vendors/scripts/script.min.js"></script>
    <script src="../vendors/scripts/process.js"></script>
    <script src="../vendors/scripts/layout-settings.js"></script>
</body>
</html>
