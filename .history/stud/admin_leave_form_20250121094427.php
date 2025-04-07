<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "your_database_name");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'fetchStaffDetails') {
        $staff_id = $_POST['staff_id'];
        $query = "SELECT emp_id, name, department, position FROM tblstaff WHERE staff_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $staff_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo json_encode($result->fetch_assoc());
        } else {
            echo json_encode(["error" => "Staff not found."]);
        }
        exit;
    }

    if ($_POST['action'] === 'fetchLeaveTypes') {
        $emp_id = $_POST['emp_id'];
        $query = "SELECT leave_type_id, leave_name FROM tblleavetype WHERE emp_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $emp_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $leave_types = [];
        while ($row = $result->fetch_assoc()) {
            $leave_types[] = $row;
        }
        echo json_encode($leave_types);
        exit;
    }

    if ($_POST['action'] === 'addLeaveApplication') {
        $emp_id = $_POST['emp_id'];
        $leave_type_id = $_POST['leave_type_id'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $reason = $_POST['reason'];

        $query = "INSERT INTO tblleave (emp_id, leave_type_id, start_date, end_date, reason) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iisss", $emp_id, $leave_type_id, $start_date, $end_date, $reason);

        if ($stmt->execute()) {
            echo json_encode(["success" => "Leave application added successfully."]);
        } else {
            echo json_encode(["error" => "Failed to add leave application."]);
        }
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Leave Form</title>
    <script>
        async function fetchStaffDetails() {
            const staffId = document.getElementById('staff_id').value;

            const response = await fetch('admin_leave_form.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ action: 'fetchStaffDetails', staff_id: staffId }),
            });
            const data = await response.json();

            if (data.error) {
                alert(data.error);
                return;
            }

            document.getElementById('name').value = data.name;
            document.getElementById('department').value = data.department;
            document.getElementById('position').value = data.position;

            fetchLeaveTypes(data.emp_id);
        }

        async function fetchLeaveTypes(empId) {
            const response = await fetch('admin_leave_form.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ action: 'fetchLeaveTypes', emp_id: empId }),
            });
            const leaveTypes = await response.json();

            const leaveTypeDropdown = document.getElementById('leave_type');
            leaveTypeDropdown.innerHTML = leaveTypes
                .map(lt => `<option value="${lt.leave_type_id}">${lt.leave_name}</option>`)
                .join('');
        }

        async function submitForm(event) {
            event.preventDefault();

            const formData = new FormData(document.getElementById('leaveForm'));
            formData.append('action', 'addLeaveApplication');

            const response = await fetch('admin_leave_form.php', {
                method: 'POST',
                body: formData,
            });
            const result = await response.json();

            alert(result.success || result.error);
        }
    </script>
</head>
<body>
    <h1>Admin Leave Form</h1>
    <form id="leaveForm" onsubmit="submitForm(event)">
        <label for="staff_id">Staff ID:</label>
        <input type="text" id="staff_id" name="staff_id" required>
        <button type="button" onclick="fetchStaffDetails()">Fetch Staff Details</button>
        <br><br>

        <label for="name">Name:</label>
        <input type="text" id="name" name="name" readonly>
        <br><br>

        <label for="department">Department:</label>
        <input type="text" id="department" name="department" readonly>
        <br><br>

        <label for="position">Position:</label>
        <input type="text" id="position" name="position" readonly>
        <br><br>

        <label for="leave_type">Leave Type:</label>
        <select id="leave_type" name="leave_type" required></select>
        <br><br>

        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date" required>
        <br><br>

        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date" required>
        <br><br>

        <label for="reason">Reason:</label>
        <textarea id="reason" name="reason" required></textarea>
        <br><br>

        <button type="submit">Submit Leave</button>
    </form>
</body>
</html>
