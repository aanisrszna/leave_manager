<?php
include('session.php');
include('connect.php');

if (!isset($_GET['emp_id']) || empty($_GET['emp_id'])) {
    die("Error: Employee ID is missing.");
}

$emp_id = $_GET['emp_id'];
$query = mysqli_query($conn, "SELECT * FROM tblemployees WHERE emp_id = '$emp_id'");

if (mysqli_num_rows($query) == 0) {
    die("Error: Employee not found.");
}

$row = mysqli_fetch_assoc($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Profile</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Staff Profile</h2>
    <table>
        <tr><td><strong>Name:</strong></td><td><?php echo htmlspecialchars($row['FirstName']); ?></td></tr>
        <tr><td><strong>Email:</strong></td><td><?php echo htmlspecialchars($row['EmailId']); ?></td></tr>
        <tr><td><strong>Gender:</strong></td><td><?php echo htmlspecialchars($row['Gender']); ?></td></tr>
        <tr><td><strong>Date of Birth:</strong></td><td><?php echo htmlspecialchars($row['Dob']); ?></td></tr>
        <tr><td><strong>Department:</strong></td><td><?php echo htmlspecialchars($row['Department']); ?></td></tr>
        <tr><td><strong>Address:</strong></td><td><?php echo htmlspecialchars($row['Address']); ?></td></tr>
        <tr><td><strong>Phone Number:</strong></td><td><?php echo htmlspecialchars($row['Phonenumber']); ?></td></tr>
        <tr><td><strong>Staff ID:</strong></td><td><?php echo htmlspecialchars($row['Staff_ID']); ?></td></tr>
        <tr><td><strong>Emergency Contact Name:</strong></td><td><?php echo htmlspecialchars($row['Emergency_Name']); ?></td></tr>
        <tr><td><strong>Emergency Contact Relation:</strong></td><td><?php echo htmlspecialchars($row['Emergency_Relation']); ?></td></tr>
        <tr><td><strong>Emergency Contact Number:</strong></td><td><?php echo htmlspecialchars($row['Emergency_Contact']); ?></td></tr>
        <tr><td><strong>Date Joined:</strong></td><td><?php echo htmlspecialchars($row['date_joined']); ?></td></tr>
        <tr><td><strong>Service Year:</strong></td><td><?php echo htmlspecialchars($row['Service_Year']); ?></td></tr>
        <tr><td><strong>Car Plate:</strong></td><td><?php echo htmlspecialchars($row['Car_Plate']); ?></td></tr>
    </table>
    <br>
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
