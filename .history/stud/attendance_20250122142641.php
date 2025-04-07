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
    <?php
    include('../includes/config.php');
    include('../includes/session.php');
    ?>

    <div class="container mt-5">
        <h2 class="text-center mb-4">My Attendance</h2>
        <div class="row">
            <!-- Clock In/Out Section -->
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
                    <tbody id="attendance-records">
                        <tr>
                            <td colspan="5" class="text-center">Loading...</td>
                        </tr>
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

        // Fetch attendance records dynamically
        function fetchAttendance() {
            fetch('fetch_attendance.php')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('attendance-records');
                    tbody.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(record => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${record.date}</td>
                                <td>${record.time_in || '-'}</td>
                                <td>${record.time_out || '-'}</td>
                                <td>${record.total_hours || '-'}</td>
                                <td>${record.time_out ? 'Out' : 'In'}</td>
                            `;
                            tbody.appendChild(row);
                        });
                    } else {
                        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No records found for today.</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching attendance:', error);
                });
        }

        // Submit clock in/out forms via AJAX
        document.getElementById('clock-in-form').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            fetch('attendance_handler.php', {
                method: 'POST',
                body: formData
            }).then(() => fetchAttendance());
        });

        document.getElementById('clock-out-form').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            fetch('attendance_handler.php', {
                method: 'POST',
                body: formData
            }).then(() => fetchAttendance());
        });

        // Initial fetch of attendance records
        fetchAttendance();
    </script>
</body>
</html>
