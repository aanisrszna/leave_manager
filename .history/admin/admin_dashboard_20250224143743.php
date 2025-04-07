<?php include('includes/header.php'); ?>
<?php include('../includes/session.php'); ?>
<body>
    <?php include('includes/navbar.php'); ?>
    <?php include('includes/right_sidebar.php'); ?>
    <?php include('includes/left_sidebar.php'); ?>

    <div class="mobile-menu-overlay"></div>

    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="title pb-20">
                <h2 class="h3 mb-0">Data Information</h2>
            </div>
            <div class="row pb-10">
                <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                    <div class="card-box height-100-p widget-style3">
                        <?php
                        $sql = "SELECT emp_id FROM tblemployees";
                        $query = $dbh->prepare($sql);
                        $query->execute();
                        $empcount = $query->rowCount();
                        ?>
                        <div class="d-flex flex-wrap">
                            <div class="widget-data">
                                <div class="weight-700 font-24 text-dark"><?php echo $empcount; ?></div>
                                <div class="font-14 text-secondary weight-500">Total Staffs</div>
                            </div>
                            <div class="widget-icon">
                                <div class="icon" data-color="#00eccf"><i class="icon-copy dw dw-user-2"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Approved Leaves -->
                <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                    <div class="card-box height-100-p widget-style3">
                        <?php
                        $status = 1;
                        $sql = "SELECT id FROM tblleave WHERE RegRemarks=:status";
                        $query = $dbh->prepare($sql);
                        $query->bindParam(':status', $status, PDO::PARAM_STR);
                        $query->execute();
                        $leavecount = $query->rowCount();
                        ?>
                        <div class="d-flex flex-wrap">
                            <div class="widget-data">
                                <div class="weight-700 font-24 text-dark"><?php echo htmlentities($leavecount); ?></div>
                                <div class="font-14 text-secondary weight-500">Approved Leave</div>
                            </div>
                            <div class="widget-icon">
                                <div class="icon" data-color="#09cc06"><span class="icon-copy fa fa-hourglass"></span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="title pb-10">
                <h2 class="h3 mb-0">Employee Leave Information</h2>
            </div>

            <div class="row pb-0">
                <?php
                // Fetch distinct employees
                $sql = "SELECT DISTINCT tblemployees.emp_id, tblemployees.FirstName, tblemployees.Staff_ID
                        FROM employee_leave
                        JOIN tblemployees ON employee_leave.emp_id = tblemployees.emp_id";
                $query = $dbh->prepare($sql);
                $query->execute();
                $employees = $query->fetchAll(PDO::FETCH_OBJ);

                // Loop through each employee
                foreach ($employees as $employee) {
                ?>
                    <div class='col-xl-6 col-lg-6 col-md-12 mb-20'>
                        <a href="edit_staff.php?edit=<?php echo urlencode($employee->emp_id); ?>" style="text-decoration: none;">
                            <div class='card-box height-100-p widget-style3' style="cursor: pointer;">
                                <div class='d-flex justify-content-center align-items-center' 
                                     style='background-color:rgb(70, 142, 209); color: white; padding: 10px;'>
                                    <h3 class='h4 mb-0' style='font-weight: 600;'>
                                        <?php echo htmlentities($employee->FirstName); ?> (Staff ID: <?php echo htmlentities($employee->Staff_ID); ?>)
                                    </h3>
                                </div>
                                <div class='table-responsive mt-0'>
                                    <table class='table table-bordered table-striped table-hover'>
                                        <thead>
                                            <tr>
                                                <th style='background-color:rgba(119, 124, 120, 0.72); color: white;'>Leave Type</th>
                                                <th style='background-color: rgba(119, 124, 120, 0.72); color: white;'>Available Days</th>
                                            </tr>
                                        </thead>
                                        <tbody style='background-color: #f9f9f9;'>
                                            <?php
                                            // Fetch leave details for each employee
                                            $sql_leaves = "SELECT tblleavetype.LeaveType, employee_leave.available_day
                                                           FROM employee_leave
                                                           JOIN tblleavetype ON employee_leave.leave_type_id = tblleavetype.id
                                                           WHERE employee_leave.emp_id = :emp_id";
                                            $query_leaves = $dbh->prepare($sql_leaves);
                                            $query_leaves->bindParam(':emp_id', $employee->emp_id, PDO::PARAM_INT);
                                            $query_leaves->execute();
                                            $leave_details = $query_leaves->fetchAll(PDO::FETCH_OBJ);

                                            foreach ($leave_details as $leave) {
                                            ?>
                                                <tr>
                                                    <td style='background-color: #f9f9f9;'><?php echo htmlentities($leave->LeaveType); ?></td>
                                                    <td style='background-color: #f9f9f9;'><?php echo htmlentities($leave->available_day); ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <?php include('includes/scripts.php'); ?>
</body>
<!-- <?php include('includes/footer.php'); ?> -->
</html>
