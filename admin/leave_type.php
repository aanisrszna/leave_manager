<?php include('includes/header.php') ?>
<?php include('../includes/session.php') ?>

<?php 
// Handle delete functionality
if (isset($_GET['delete'])) {
    $leave_type_id = $_GET['delete'];
    $sql = "DELETE FROM tblleavetype WHERE id = ".$leave_type_id;
    $result = mysqli_query($conn, $sql);
    if ($result) {
        echo "<script>alert('LeaveType deleted Successfully');</script>";
        echo "<script type='text/javascript'> document.location = 'leave_type.php'; </script>";
    }
}

// Handle add functionality
if (isset($_POST['add'])) {
    $leavetype = $_POST['leavetype'];
    $description = $_POST['description'];
    $assigned_day = intval($_POST['assigned_day']); // Convert assigned days to an integer
    $need_proof = $_POST['need_proof']; // Get Need Proof value

    $query = mysqli_query($conn, "SELECT * FROM tblleavetype WHERE LeaveType = '$leavetype'") or die(mysqli_error());
    $count = mysqli_num_rows($query);

    if ($count > 0) { 
        echo "<script>alert('LeaveType Already exists');</script>";
    } else {
        $query = mysqli_query($conn, "INSERT INTO tblleavetype (LeaveType, Description, assigned_day, NeedProof) 
            VALUES ('$leavetype', '$description', '$assigned_day', '$need_proof')") or die(mysqli_error());

        if ($query) {
            echo "<script>alert('LeaveType Added');</script>";
            echo "<script type='text/javascript'> document.location = 'leave_type.php'; </script>";
        }
    }
}
?>

<body>
    <?php include('includes/navbar.php') ?>
    <?php include('includes/right_sidebar.php') ?>
    <?php include('includes/left_sidebar.php') ?>

    <div class="mobile-menu-overlay"></div>

    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="title">
                                <h4>Leave Type List</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="admin_dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Leave Type Module</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Add Leave Type Form -->
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-30">
                        <div class="card-box pd-30 pt-10 height-100-p">
                            <h2 class="mb-30 h4">New Leave Type</h2>
                            <section>
                                <form name="save" method="post">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Leave Type</label>
                                                <input name="leavetype" type="text" class="form-control" required="true" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Leave Description</label>
                                                <textarea name="description" style="height: 5em;" class="form-control text_area" type="text"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Assigned Days</label>
                                                <input name="assigned_day" class="form-control" type="number" min="0.5" step="0.5" placeholder="Enter the number of days" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Need Proof</label>
                                                <select name="need_proof" class="form-control" required>
                                                    <option value="" disabled selected>Select</option>
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 text-right">
                                        <div class="dropdown">
                                            <input class="btn btn-primary" type="submit" value="REGISTER" name="add" id="add">
                                        </div>
                                    </div>
                                </form>
                            </section>
                        </div>
                    </div>

                    <!-- Leave Type List Table -->
                    <div class="col-lg-8 col-md-6 col-sm-12 mb-30">
                        <div class="card-box pd-30 pt-10 height-100-p">
                            <h2 class="mb-30 h4">Leave Type List</h2>
                            <div class="pb-20">
                                <table class="data-table table stripe hover nowrap">
                                    <thead>
                                        <tr>
                                            <th class="table-plus">LEAVETYPE</th>
                                            <th class="table-plus">DESCRIPTION</th>
                                            <th class="table-plus">ASSIGNED DAYS</th>
                                            <th class="table-plus">NEED PROOF</th>
                                            <th class="datatable-nosort">ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $sql = "SELECT * FROM tblleavetype";
                                        $query = $dbh->prepare($sql);
                                        $query->execute();
                                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                                        $cnt = 1;
                                        if ($query->rowCount() > 0) {
                                            foreach ($results as $result) { ?>  
                                                <tr>
                                                    <td><?php echo htmlentities($result->LeaveType); ?></td>
                                                    <td><?php echo htmlentities($result->Description); ?></td>
                                                    <td><?php echo htmlentities($result->assigned_day . " days"); ?></td>
                                                    <td><?php echo htmlentities($result->NeedProof); ?></td>
                                                    <td>
                                                        <div class="table-actions">
                                                            <a href="edit_leave_type.php?edit=<?php echo htmlentities($result->id); ?>" data-color="#265ed7">
                                                                <i class="icon-copy dw dw-edit2"></i>
                                                            </a>
                                                            <a href="leave_type.php?delete=<?php echo htmlentities($result->id); ?>" data-color="#e95959">
                                                                <i class="icon-copy dw dw-delete-3"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php $cnt++; } 
                                        } ?>  
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include('includes/footer.php'); ?>
        </div>
    </div>
    <?php include('includes/scripts.php') ?>
</body>

