<?php 
include('includes/header.php'); 
include('../includes/session.php'); 
$get_id = $_GET['edit']; 

if (isset($_POST['edit'])) {

    $leavetype    = trim($_POST['leavetype']);
    $description  = trim($_POST['description']);
    $assigned_day = floatval($_POST['assigned_day']);

    $needproof = ($_POST['needproof'] === 'Yes') ? 'Yes' : 'No';
    $isdisplay = ($_POST['is_display'] === 'Yes') ? 'Yes' : 'No';

    $stmt = $conn->prepare("
        UPDATE tblleavetype 
        SET LeaveType = ?, 
            Description = ?, 
            assigned_day = ?, 
            NeedProof = ?, 
            IsDisplay = ?
        WHERE id = ?
    ");

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param(
        "ssdssi",
        $leavetype,
        $description,
        $assigned_day,
        $needproof,
        $isdisplay,
        $get_id
    );

    if ($stmt->execute()) {
        echo "<script>alert('Leave Type updated successfully');</script>";
        echo "<script>window.location='leave_type.php';</script>";
    } else {
        echo "<script>alert('Update failed: {$stmt->error}');</script>";
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
                                // Query to get the existing leave type data
                                $query = mysqli_query($conn, "SELECT * FROM tblleavetype WHERE id = '$get_id'") or die(mysqli_error($conn));
                                $row = mysqli_fetch_array($query);
                                ?>
                                <form method="post">
                                    <div class="form-group">
                                        <label>Leave Type</label>
                                        <input name="leavetype" type="text" class="form-control" required value="<?php echo htmlentities($row['LeaveType']); ?>">
                                    </div>

                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea name="description" class="form-control" required><?php echo htmlentities($row['Description']); ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label>Assigned Day</label>
                                        <input name="assigned_day" type="number" step="0.01" class="form-control" min="0" value="<?php echo htmlentities($row['assigned_day']); ?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Need Proof</label><br>
                                        <!-- Radios use Yes/No to match DB -->
                                        <input name="needproof" type="radio" value="Yes" id="needproof_yes" 
                                            <?php echo ($row['NeedProof'] === 'Yes') ? 'checked' : ''; ?> required>
                                        <label for="needproof_yes">Yes</label>

                                        <input name="needproof" type="radio" value="No" id="needproof_no" 
                                            <?php echo ($row['NeedProof'] === 'No') ? 'checked' : ''; ?> required>
                                        <label for="needproof_no">No</label>
                                    </div>

                                    <!-- NEW: Display on Pie Chart -->
                                    <div class="form-group">
                                        <label>Display on Bar Chart</label><br>
                                        <input name="is_display" type="radio" value="Yes" id="is_display_yes"
                                            <?php echo (isset($row['IsDisplay']) && $row['IsDisplay'] === 'Yes') ? 'checked' : ''; ?> required>
                                        <label for="is_display_yes">Yes</label>

                                        <input name="is_display" type="radio" value="No" id="is_display_no"
                                            <?php echo (isset($row['IsDisplay']) && $row['IsDisplay'] === 'No') ? 'checked' : ''; ?> required>
                                        <label for="is_display_no">No</label>
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
