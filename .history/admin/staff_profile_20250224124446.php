<?php include('includes/header.php'); ?>
<?php include('../includes/session.php'); ?>

<?php
if (isset($_GET['emp_id'])) {
    $emp_id = intval($_GET['emp_id']); // Get emp_id from URL

    // Fetch employee details
    $sql = "SELECT * FROM tblemployees WHERE emp_id = :emp_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':emp_id', $emp_id, PDO::PARAM_INT);
    $query->execute();
    $employee = $query->fetch(PDO::FETCH_OBJ);

    if (!$employee) {
        echo "<script>alert('Employee not found!'); window.location.href='dashboard.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('No employee selected!'); window.location.href='dashboard.php';</script>";
    exit;
}
?>

<body>
    <?php include('includes/navbar.php'); ?>
    <?php include('includes/right_sidebar.php'); ?>
    <?php include('includes/left_sidebar.php'); ?>

    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="title pb-20">
                <h2 class="h3 mb-0">Staff Profile</h2>
            </div>

            <div class="card-box">
                <h4 class="text-dark"><?= htmlentities($employee->FirstName . ' ' . $employee->LastName); ?></h4>
                <p><strong>Staff ID:</strong> <?= htmlentities($employee->Staff_ID); ?></p>
                <p><strong>Email:</strong> <?= htmlentities($employee->EmailId); ?></p>
                <p><strong>Phone:</strong> <?= htmlentities($employee->Phonenumber); ?></p>
                <p><strong>Department:</strong> <?= htmlentities($employee->Department); ?></p>
                <p><strong>Position:</strong> <?= htmlentities($employee->Position); ?></p>
            </div>
        </div>
    </div>

    <?php include('includes/scripts.php'); ?>
</body>
</html>
