<?php include('includes/header.php'); ?>
<?php include('../includes/session.php'); ?>
<?php $get_id = $_GET['edit']; ?>

<?php 
if (isset($_POST['edit'])) {
    $leavetype = htmlspecialchars($_POST['leavetype'], ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');
    $assigned_day = intval($_POST['assigned_day']); // Ensure assigned days are numeric
    $needproof = htmlspecialchars($_POST['needproof'], ENT_QUOTES, 'UTF-8'); // Ensure proof is safe

    // Update query using prepared statements
    $stmt = $conn->prepare("UPDATE tblleavetype SET LeaveType = ?, Description = ?, assigned_day = ?, NeedProof = ? WHERE id = ?");
    $stmt->bind_param("ssiiis", $leavetype, $description, $available_day, $assigned_day, $needproof, $get_id);

    if ($stmt->execute()) {
        echo "<script>alert('Leave Type updated successfully');</script>";
        echo "<script type='text/javascript'> document.location = 'leave_type.php'; </script>";
    } else {
        echo "<script>alert('Error updating Leave Type');</script>";
    }
    $stmt->close();
}
?>

<body>
    <?php include('includes/navbar.php'); ?>
    <?php include('includes/right_sidebar.php'); ?>
    <?php include('includes/left_sidebar.php'); ?>

    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="title">
                                <h4>Edit Leave Type</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="admin_dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Edit Leave Type</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-center">                
                    <!-- Edit Leave Type Form -->
                    <div class="col-lg-6 col-md-8 col-sm-12 mb-30">
                        <div class="card-box pd-30 pt-10 height-100-p">
                            <h2 class="mb-30 h4">Edit Leave Type</h2>
                            <section>
                                <?php
                                $query = mysqli_query($conn, "SELECT * FROM tblleavetype WHERE id = '$get_id'") or die(mysqli_error($conn));
                                $row = mysqli_fetch_array($query);
                                ?>
                                <form method="post">
                                    <div class="form-group">
                                        <label>Leave Type</label>
                                        <input name="leavetype" type="text" class="form-control" required="true" value="<?php echo htmlentities($row['LeaveType']); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea name="description" class="form-control" required><?php echo htmlentities($row['Description']); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Assigned Days</label>
                                        <input name="assigned_day" type="number" class="form-control" min="1" value="<?php echo htmlentities($row['assigned_day']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Need Proof</label>
                                        <select name="needproof" class="form-control" required>
                                            <option value="Yes" <?php echo ($row['NeedProof'] == 'Yes' ? 'selected' : ''); ?>>Yes</option>
                                            <option value="No" <?php echo ($row['NeedProof'] == 'No' ? 'selected' : ''); ?>>No</option>
                                        </select>
                                    </div>
                                    <div class="text-right">
                                        <button class="btn btn-primary" type="submit" name="edit">Update</button>
                                    </div>
                                </form>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
            <?php include('includes/footer.php'); ?>
        </div>
    </div>
    <?php include('includes/scripts.php'); ?>
</body>
</html>
