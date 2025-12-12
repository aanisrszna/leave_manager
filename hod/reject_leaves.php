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
                            <th>LEAVE DURATION</th>
                            <th>MY REMARKS</th>
                            <th>DIRECTOR REMARKS</th>
                            <th class="datatable-nosort">ACTION</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        // ✅ Use prepared statement to avoid SQL injection
                        $sql = "SELECT 
                                    tl.id AS lid,
                                    emp.FirstName,
                                    emp.emp_id,
                                    emp.Gender,
                                    emp.Phonenumber,
                                    emp.EmailId,
                                    emp.Position_Staff,
                                    emp.Staff_ID,
                                    emp.location,
                                    tl.LeaveType,
                                    tl.ToDate,
                                    tl.FromDate,
                                    tl.PostingDate,
                                    tl.RequestedDays,
                                    tl.DaysOutstand,
                                    tl.Sign,
                                    tl.HodRemarks,
                                    tl.RegRemarks,
                                    tl.HodSign,
                                    tl.RegSign,
                                    tl.HodDate,
                                    tl.RegDate
                                FROM tblleave tl
                                JOIN tblemployees emp 
                                    ON tl.empid = emp.emp_id
                                WHERE emp.role = 'Staff'
                                AND emp.Department = ?
                                AND tl.HodRemarks = 2
                                ORDER BY tl.id DESC";

                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("s", $session_depart);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        while ($row = $result->fetch_assoc()) {

                            $staffName = htmlspecialchars($row['FirstName'] ?? '');

                            $fromDateRaw = $row['FromDate'] ?? '';
                            $toDateRaw   = $row['ToDate'] ?? '';

                            $fromDate = $fromDateRaw ? date("d/m/Y", strtotime($fromDateRaw)) : '-';
                            $toDate   = $toDateRaw ? date("d/m/Y", strtotime($toDateRaw)) : '-';

                            $orderDate = $fromDateRaw ? date('Ymd', strtotime($fromDateRaw)) : '';

                            $imgFile = (!empty($row['location']))
                                ? '../uploads/' . basename($row['location'])
                                : '../uploads/NO-IMAGE-AVAILABLE.jpg';
                            ?>
                            <tr>
                                <td class="table-plus">
                                    <div class="name-avatar d-flex align-items-center">
                                        <div class="avatar mr-2 flex-shrink-0">
                                            <img src="<?php echo $imgFile; ?>"
                                                class="border-radius-100 shadow"
                                                width="40" height="40" alt="">
                                        </div>
                                        <div class="txt">
                                            <div class="weight-600"><?php echo $staffName; ?></div>
                                        </div>
                                    </div>
                                </td>

                                <td data-order="<?php echo $orderDate; ?>">
                                    <?php echo $fromDate . " to " . $toDate; ?>
                                </td>

                                <td>
                                    <?php
                                    $hod = (int)($row['HodRemarks'] ?? 0);
                                    if ($hod === 1) {
                                        echo '<span style="color: green">Approved</span>';
                                    } elseif ($hod === 2) {
                                        echo '<span style="color: red">Rejected</span>';
                                    } else {
                                        echo '<span style="color: blue">Pending</span>';
                                    }
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    $reg = (int)($row['RegRemarks'] ?? 0);
                                    if ($reg === 1) {
                                        echo '<span style="color: green">Approved</span>';
                                    } elseif ($reg === 2) {
                                        echo '<span style="color: red">Rejected</span>';
                                    } else {
                                        echo '<span style="color: blue">Pending</span>';
                                    }
                                    ?>
                                </td>

                                <td>
                                    <a class="btn btn-link font-24 p-0 line-height-1"
                                    href="leave_details.php?leaveid=<?php echo (int)$row['lid']; ?>">
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
