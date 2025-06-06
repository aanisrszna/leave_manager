<?php include('includes/header.php'); ?>
<?php include('../includes/session.php'); ?>

<?php
if (isset($_GET['delete'])) {
    $delete = $_GET['delete'];
    $sql = "DELETE FROM tblemployees WHERE emp_id = " . intval($delete);
    $result = mysqli_query($conn, $sql);
    if ($result) {
        echo "<script>alert('Staff deleted Successfully');</script>";
        echo "<script type='text/javascript'> document.location = 'staff.php'; </script>";
    }
}

// Set status filter for Active / Inactive employees
$status_filter = '';
if (isset($_GET['status']) && in_array($_GET['status'], ['Active', 'Inactive'])) {
    $status = $_GET['status'];
    $status_filter = " AND tblemployees.status = '" . mysqli_real_escape_string($conn, $status) . "'";
}
?>

<body>

<?php include('includes/navbar.php'); ?>
<?php include('includes/right_sidebar.php'); ?>
<?php include('includes/left_sidebar.php'); ?>

<div class="mobile-menu-overlay"></div>

<div class="main-container">
    <div class="pd-ltr-20">

        <div class="title pb-20">
            <h2 class="h3 mb-0">Staff Overview</h2>
        </div>

        <div class="row pb-10">
            <!-- Your existing widgets code -->
            <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                <div class="card-box height-100-p widget-style3">
                    <?php
                    $sql = "SELECT emp_id FROM tblemployees WHERE role <> 'Admin'";
                    $query = $dbh->prepare($sql);
                    $query->execute();
                    $empcount = $query->rowCount();
                    ?>
                    <div class="d-flex flex-wrap">
                        <div class="widget-data">
                            <div class="weight-700 font-24 text-dark"><?php echo ($empcount); ?></div>
                            <div class="font-14 text-secondary weight-500">Total Staffs</div>
                        </div>
                        <div class="widget-icon">
                            <div class="icon" data-color="#00eccf"><i class="icon-copy dw dw-user-2"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                <div class="card-box height-100-p widget-style3">
                    <?php
                    $query_reg_staff = mysqli_query($conn, "SELECT * FROM tblemployees WHERE role = 'Staff'");
                    $count_reg_staff = mysqli_num_rows($query_reg_staff);
                    ?>
                    <div class="d-flex flex-wrap">
                        <div class="widget-data">
                            <div class="weight-700 font-24 text-dark"><?php echo htmlentities($count_reg_staff); ?></div>
                            <div class="font-14 text-secondary weight-500">Staffs</div>
                        </div>
                        <div class="widget-icon">
                            <div class="icon" data-color="#09cc06"><span class="icon-copy fa fa-hourglass"></span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                <div class="card-box height-100-p widget-style3">
                    <?php
                    $query_reg_hod = mysqli_query($conn, "SELECT * FROM tblemployees WHERE role = 'Manager'");
                    $count_reg_hod = mysqli_num_rows($query_reg_hod);
                    ?>
                    <div class="d-flex flex-wrap">
                        <div class="widget-data">
                            <div class="weight-700 font-24 text-dark"><?php echo ($count_reg_hod); ?></div>
                            <div class="font-14 text-secondary weight-500">Manager</div>
                        </div>
                        <div class="widget-icon">
                            <div class="icon"><i class="icon-copy fa fa-hourglass-end" aria-hidden="true"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                <div class="card-box height-100-p widget-style3">
                    <?php
                    $query_reg_admin = mysqli_query($conn, "SELECT * FROM tblemployees WHERE role = 'Director'");
                    $count_reg_admin = mysqli_num_rows($query_reg_admin);
                    ?>
                    <div class="d-flex flex-wrap">
                        <div class="widget-data">
                            <div class="weight-700 font-24 text-dark"><?php echo ($count_reg_admin); ?></div>
                            <div class="font-14 text-secondary weight-500">Director</div>
                        </div>
                        <div class="widget-icon">
                            <div class="icon" data-color="#ff5b5b"><i class="icon-copy fa fa-hourglass-o" aria-hidden="true"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Buttons for Active / Inactive -->
        <div class="mb-3">
            <a href="staff.php" class="btn btn-sm btn-outline-primary <?php if (!isset($_GET['status'])) echo 'active'; ?>">All</a>
            <a href="staff.php?status=Active" class="btn btn-sm btn-outline-success <?php if (isset($_GET['status']) && $_GET['status'] == 'Active') echo 'active'; ?>">Active</a>
            <a href="staff.php?status=Inactive" class="btn btn-sm btn-outline-danger <?php if (isset($_GET['status']) && $_GET['status'] == 'Inactive') echo 'active'; ?>">Inactive</a>
        </div>

        <div class="card-box mb-30">
            <div class="pd-20">
                <h2 class="text-blue h4">ALL STAFF</h2>
            </div>
            <div class="pb-20">
                <table class="data-table table stripe hover nowrap">
                    <thead>
                        <tr>
                            <th class="table-plus">FULL NAME</th>
                            <th>EMAIL</th>
                            <th>DEPARTMENT</th>
                            <th>POSITION</th>
                            <th class="datatable-nosort">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $staff_query = mysqli_query($conn, "SELECT * FROM tblemployees 
                            LEFT JOIN tbldepartments ON tblemployees.Department = tbldepartments.DepartmentShortName 
                            WHERE role IN ('Director', 'Manager', 'Staff') 
                            $status_filter
                            ORDER BY tblemployees.emp_id");

                        while ($row = mysqli_fetch_array($staff_query)) {
                        ?>
                            <tr>
                                <td class="table-plus">
                                    <div class="name-avatar d-flex align-items-center">
                                        <div class="avatar mr-2 flex-shrink-0">
                                            <img src="<?php echo (!empty($row['location'])) ? '../uploads/' . $row['location'] : '../uploads/NO-IMAGE-AVAILABLE.jpg'; ?>" class="border-radius-100 shadow" width="40" height="40" alt="">
                                        </div>
                                        <div class="txt">
                                            <div class="weight-600"><?php echo $row['FirstName']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo $row['EmailId']; ?></td>
                                <td><?php echo $row['DepartmentName']; ?></td>
                                <td><?php echo $row['Position_Staff']; ?></td>
                                <td>
                                    <div class="dropdown">
                                        <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                            <i class="dw dw-more"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                            <a class="dropdown-item" href="edit_staff.php?edit=<?php echo $row['emp_id']; ?>"><i class="dw dw-edit2"></i> Edit</a>
                                            <a class="dropdown-item" href="staff.php?delete=<?php echo $row['emp_id']; ?>" onclick="return confirm('Are you sure to delete this staff?');"><i class="dw dw-delete-3"></i> Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>

                </table>
            </div>
        </div>

        <?php include('includes/footer.php'); ?>
    </div>
</div>

<?php include('includes/scripts.php'); ?>
</body>

</html>
