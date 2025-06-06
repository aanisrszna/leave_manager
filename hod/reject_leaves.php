<?php include('includes/header.php') ?>
<?php include('../includes/session.php') ?>
<body>
<?php include('includes/navbar.php') ?>
<?php include('includes/right_sidebar.php') ?>
<?php include('includes/left_sidebar.php') ?>

<div class="mobile-menu-overlay"></div>

<div class="main-container">
    <div class="pd-ltr-20">
        <div class="page-header">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="title">
                        <h4>Leave Portal</h4>
                    </div>
                    <nav aria-label="breadcrumb" role="navigation">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="admin_dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Rejected by HOD</li>
                        </ol>
                    </nav>
                </div>
            </div> 
        </div>

        <div class="card-box mb-30">
            <div class="pd-20">
                <h2 class="text-blue h4">LEAVE APPLICATIONS REJECTED BY HOD</h2>
            </div>
            <div class="pb-20">
                <table class="data-table table stripe hover nowrap">
                    <thead>
                        <tr>
                            <th class="table-plus datatable-nosort">STAFF NAME</th>
                            <th>LEAVE TYPE</th>
                            <th>APPLIED DATE</th>
                            <th>MY REMARKS</th>
                            <th>DIRECTOR REMARKS</th>
                            <th class="datatable-nosort">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sql = "SELECT tblleave.id as lid, tblemployees.FirstName, tblemployees.emp_id, tblemployees.Gender, tblemployees.Phonenumber, tblemployees.EmailId, tblemployees.Position_Staff, tblemployees.Staff_ID, tblleave.LeaveType, tblleave.ToDate, tblleave.FromDate, tblleave.PostingDate, tblleave.RequestedDays, tblleave.DaysOutstand, tblleave.Sign, tblleave.HodRemarks, tblleave.RegRemarks, tblleave.HodSign, tblleave.RegSign, tblleave.HodDate, tblleave.RegDate, tblemployees.location 
                        FROM tblleave 
                        JOIN tblemployees ON tblleave.empid = tblemployees.emp_id 
                        WHERE tblemployees.role = 'Staff' 
                        AND tblemployees.Department = '$session_depart' 
                        AND tblleave.HodRemarks = 2 
                        ORDER BY lid";

                        $query = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                        while ($row = mysqli_fetch_array($query)) {
                        ?>  
                        <tr>
                            <td class="table-plus">
                                <div class="name-avatar d-flex align-items-center">
                                    <div class="avatar mr-2 flex-shrink-0">
                                        <img src="<?php echo (!empty($row['location'])) ? '../uploads/'.$row['location'] : '../uploads/NO-IMAGE-AVAILABLE.jpg'; ?>" class="border-radius-100 shadow" width="40" height="40" alt="">
                                    </div>
                                    <div class="txt">
                                        <div class="weight-600"><?php echo $row['FirstName']; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo $row['LeaveType']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['PostingDate'])); ?></td>
                            <td>
                                <?php
                                $stats = $row['HodRemarks'];
                                if ($stats == 1) {
                                    echo '<span style="color: green">Approved</span>';
                                } elseif ($stats == 2) {
                                    echo '<span style="color: red">Rejected</span>';
                                } else {
                                    echo '<span style="color: blue">Pending</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                $stats = $row['RegRemarks'];
                                if ($stats == 1) {
                                    echo '<span style="color: green">Approved</span>';
                                } elseif ($stats == 2) {
                                    echo '<span style="color: red">Rejected</span>';
                                } else {
                                    echo '<span style="color: blue">Pending</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <a class="btn btn-link font-24 p-0 line-height-1" href="leave_details.php?leaveid=<?php echo $row['lid']; ?>">
                                    <i class="dw dw-eye"></i>
                                </a>
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

<!-- JS Scripts -->
<script src="../vendors/scripts/core.js"></script>
<script src="../vendors/scripts/script.min.js"></script>
<script src="../vendors/scripts/process.js"></script>
<script src="../vendors/scripts/layout-settings.js"></script>
<script src="../src/plugins/apexcharts/apexcharts.min.js"></script>
<script src="../src/plugins/datatables/js/jquery.dataTables.min.js"></script>
<script src="../src/plugins/datatables/js/dataTables.bootstrap4.min.js"></script>
<script src="../src/plugins/datatables/js/dataTables.responsive.min.js"></script>
<script src="../src/plugins/datatables/js/responsive.bootstrap4.min.js"></script>

<!-- Export Buttons -->
<script src="../src/plugins/datatables/js/dataTables.buttons.min.js"></script>
<script src="../src/plugins/datatables/js/buttons.bootstrap4.min.js"></script>
<script src="../src/plugins/datatables/js/buttons.print.min.js"></script>
<script src="../src/plugins/datatables/js/buttons.html5.min.js"></script>
<script src="../src/plugins/datatables/js/buttons.flash.min.js"></script>
<script src="../src/plugins/datatables/js/vfs_fonts.js"></script>

<script src="../vendors/scripts/datatable-setting.js"></script>
</body>
</html>
