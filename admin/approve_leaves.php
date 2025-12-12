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
                        <h4>Approved Leave Applications</h4>
                    </div>
                    <nav aria-label="breadcrumb" role="navigation">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="admin_dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Approved Leave</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="card-box mb-30">
            <div class="pd-20 d-flex justify-content-between align-items-center">
                <h2 class="text-blue h4">APPROVED APPLICATIONS</h2>
                <a href="leaves.php" class="btn btn-outline-secondary btn-sm">← Back to All Leaves</a>
            </div>
            <div class="pb-20">
                <table class="data-table table stripe hover nowrap">
                    <thead>
                        <tr>
                            <th class="table-plus datatable-nosort">STAFF NAME</th>
                            <th>LEAVE DURATION</th>
                            <th>MANAGER STATUS</th>
                            <th>DIRECTOR STATUS</th>
                            <th class="datatable-nosort">ACTION</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        // Fetch approved (RegRemarks=1) leaves
                        $sql = "SELECT 
                                    tblleave.id AS lid,
                                    tblemployees.FirstName,
                                    tblemployees.Role,
                                    tblleave.LeaveType,
                                    tblleave.PostingDate,
                                    tblleave.RegRemarks,
                                    tblleave.HodRemarks,
                                    tblleave.ToDate,
                                    tblleave.FromDate
                                FROM tblleave
                                JOIN tblemployees ON tblleave.empid = tblemployees.emp_id
                                WHERE tblleave.RegRemarks = 1
                                ORDER BY tblleave.id DESC";

                        $query = $dbh->prepare($sql);
                        $query->execute();
                        $results = $query->fetchAll(PDO::FETCH_OBJ);

                        if ($results) {
                            foreach ($results as $row) {
                                // Safe values
                                $staffName = htmlspecialchars($row->FirstName ?? '');
                                $role      = $row->Role ?? '';

                                $fromDateRaw = $row->FromDate ?? '';
                                $toDateRaw   = $row->ToDate ?? '';

                                $fromDateDisplay = $fromDateRaw ? date("d/m/Y", strtotime($fromDateRaw)) : '-';
                                $toDateDisplay   = $toDateRaw ? date("d/m/Y", strtotime($toDateRaw)) : '-';

                                // For DataTables sorting
                                $orderDate = $fromDateRaw ? date('Ymd', strtotime($fromDateRaw)) : '';
                                ?>
                                <tr>
                                    <td class="table-plus">
                                        <div class="txt">
                                            <div class="weight-600"><?php echo $staffName; ?></div>
                                        </div>
                                    </td>

                                    <td data-order="<?php echo $orderDate; ?>">
                                        <?php echo $fromDateDisplay . " to " . $toDateDisplay; ?>
                                    </td>

                                    <td>
                                        <?php
                                        // Manager/Admin = NA for manager status
                                        if ($role === 'Manager' || $role === 'Admin') {
                                            echo '<span style="color: gray">NA</span>';
                                        } else {
                                            $hodStatus = $row->HodRemarks;

                                            if ($hodStatus == 1) {
                                                echo '<span style="color: green">Approved</span>';
                                            } elseif ($hodStatus == 2) {
                                                echo '<span style="color: red">Rejected</span>';
                                            } else {
                                                echo '<span style="color: blue">Pending</span>';
                                            }
                                        }
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        $regStatus = $row->RegRemarks;

                                        if ($regStatus == 1) {
                                            echo '<span style="color: green">Approved</span>';
                                        } elseif ($regStatus == 2) {
                                            echo '<span style="color: red">Rejected</span>';
                                        } else {
                                            echo '<span style="color: blue">Pending</span>';
                                        }
                                        ?>
                                    </td>

                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle"
                                                    type="button" data-toggle="dropdown">
                                                <i class="dw dw-more"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="leave_details.php?leaveid=<?php echo (int)$row->lid; ?>">
                                                    <i class="dw dw-eye"></i> View
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>


            <?php include('includes/footer.php'); ?>
        </div>
    </div>
</div>

<!-- js -->
<script src="../vendors/scripts/core.js"></script>
<script src="../vendors/scripts/script.min.js"></script>
<script src="../vendors/scripts/process.js"></script>
<script src="../vendors/scripts/layout-settings.js"></script>
<script src="../src/plugins/apexcharts/apexcharts.min.js"></script>
<script src="../src/plugins/datatables/js/jquery.dataTables.min.js"></script>
<script src="../src/plugins/datatables/js/dataTables.bootstrap4.min.js"></script>
<script src="../src/plugins/datatables/js/dataTables.responsive.min.js"></script>
<script src="../src/plugins/datatables/js/responsive.bootstrap4.min.js"></script>
<script src="../src/plugins/datatables/js/dataTables.buttons.min.js"></script>
<script src="../src/plugins/datatables/js/buttons.bootstrap4.min.js"></script>
<script src="../src/plugins/datatables/js/buttons.print.min.js"></script>
<script src="../src/plugins/datatables/js/buttons.html5.min.js"></script>
<script src="../src/plugins/datatables/js/buttons.flash.min.js"></script>
<script src="../src/plugins/datatables/js/vfs_fonts.js"></script>
<script src="../vendors/scripts/datatable-setting.js"></script>

</body>
</html>
