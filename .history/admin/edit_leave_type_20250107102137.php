<?php include('includes/header.php'); ?>
<?php include('../includes/session.php'); ?>
<?php $get_id = $_GET['edit']; ?>

<?php 
if (isset($_GET['delete'])) {
    $leave_type_id = $_GET['delete'];
    $sql = "DELETE FROM tblleavetype WHERE id = ".$leave_type_id;
    $result = mysqli_query($conn, $sql);
    if ($result) {
        echo "<script>alert('Leave Type deleted successfully');</script>";
        echo "<script type='text/javascript'> document.location = 'leave_type.php'; </script>";
    }
}
?>

<?php
if (isset($_POST['edit'])) {
    $leavetype = htmlspecialchars($_POST['leavetype'], ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');
    $assigned_day = date('Y-m-d', strtotime($_POST['assigned_day']));

    $stmt = $conn->prepare("UPDATE tblleavetype SET LeaveType = ?, Description = ?, assigned_day = ? WHERE id = ?");
    $stmt->bind_param("sssi", $leavetype, $description, $assigned_day, $get_id);

    if ($stmt->execute()) {
        echo "<script>alert('Record successfully updated');</script>";
        echo "<script type='text/javascript'> document.location = 'leave_type.php'; </script>";
    } else {
        echo "<script>alert('Error updating record');</script>";
    }
}
?>

<body>
    <div class="pre-loader">
        <div class="pre-loader-box">
            <div class="loader-logo"><img src="../vendors/images/deskapp-logo-svg.png" alt=""></div>
            <div class='loader-progress' id="progress_div">
                <div class='bar' id='bar1'></div>
            </div>
            <div class='percent' id='percent1'>0%</div>
            <div class="loading-text">
                Loading...
            </div>
        </div>
    </div>

    <?php include('includes/navbar.php'); ?>
    <?php include('includes/right_sidebar.php'); ?>
    <?php include('includes/left_sidebar.php'); ?>

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
                                    <li class="breadcrumb-item active" aria-current="page">Edit Leave Type</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Edit Leave Type Form -->
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-30">
                        <div class="card-box pd-30 pt-10 height-100-p">
                            <h2 class="mb-30 h4">Edit Leave Type</h2>
                            <section>
                                <?php
                                $query = mysqli_query($conn, "SELECT * FROM tblleavetype WHERE id = '$get_id'") or die(mysqli_error());
                                $row = mysqli_fetch_array($query);
                                ?>
                                <form name="save" method="post">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Leave Type</label>
                                                <input name="leavetype" type="text" class="form-control" required="true" autocomplete="off" value="<?php echo $row['LeaveType']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Leave Description</label>
                                                <textarea name="description" style="height: 5em;" class="form-control text_area" type="text"><?php echo $row['Description']; ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Assigned Day</label>
                                                <input name="assigned_day" class="form-control" type="date" value="<?php echo $row['assigned_day']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 text-right">
                                        <div class="dropdown">
                                            <input class="btn btn-primary" type="submit" value="UPDATE" name="edit" id="edit">
                                        </div>
                                    </div>
                                </form>
                            </section>
                        </div>
                    </div>

                    <!-- Leave Type List -->
                    <div class="col-lg-8 col-md-6 col-sm-12 mb-30">
                        <div class="card-box pd-30 pt-10 height-100-p">
                            <h2 class="mb-30 h4">Leave Type List</h2>
                            <div class="pb-20">
                                <table class="data-table table stripe hover nowrap">
                                    <thead>
                                        <tr>
                                            <th class="table-plus">LEAVE TYPE</th>
                                            <th class="table-plus">DESCRIPTION</th>
                                            <th>ASSIGNED DAY</th>
                                            <th class="datatable-nosort">ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT * FROM tblleavetype";
                                        $query = $dbh->prepare($sql);
                                        $query->execute();
                                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                                        if ($query->rowCount() > 0) {
                                            foreach ($results as $result) {
                                        ?>
                                        <tr>
                                            <td><?php echo htmlentities($result->LeaveType); ?></td>
                                            <td><?php echo htmlentities($result->Description); ?></td>
                                            <td><?php echo htmlentities($result->assigned_day); ?></td>
                                            <td>
                                                <div class="table-actions">
                                                    <a href="leave_type.php?delete=<?php echo htmlentities($result->id); ?>" data-color="#e95959" onclick="return confirm('Are you sure?');"><i class="icon-copy dw dw-delete-3"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php } } ?>
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
    <?php include('includes/scripts.php'); ?>
</body>
</html>
