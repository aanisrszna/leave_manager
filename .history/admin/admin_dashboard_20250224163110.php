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
                        $sql = "SELECT emp_id from tblemployees";
                        $query = $dbh->prepare($sql);
                        $query->execute();
                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                        $empcount = $query->rowCount();
                        ?>
                        <div class="d-flex flex-wrap">
                            <div class="widget-data">
                                <div class="weight-700 font-24 text-dark"><?php echo($empcount); ?></div>
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
                        $sql = "SELECT id from tblleave where RegRemarks=:status";
                        $query = $dbh->prepare($sql);
                        $query->bindParam(':status', $status, PDO::PARAM_STR);
                        $query->execute();
                        $results = $query->fetchAll(PDO::FETCH_OBJ);
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
                <!-- Pending Leaves -->
                <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                    <div class="card-box height-100-p widget-style3">
                        <?php
                        $status = 0;
                        $sql = "SELECT id from tblleave where RegRemarks=:status";
                        $query = $dbh->prepare($sql);
                        $query->bindParam(':status', $status, PDO::PARAM_STR);
                        $query->execute();
                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                        $leavecount = $query->rowCount();
                        ?>
                        <div class="d-flex flex-wrap">
                            <div class="widget-data">
                                <div class="weight-700 font-24 text-dark"><?php echo($leavecount); ?></div>
                                <div class="font-14 text-secondary weight-500">Pending Leave</div>
                            </div>
                            <div class="widget-icon">
                                <div class="icon"><i class="icon-copy fa fa-hourglass-end" aria-hidden="true"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Rejected Leaves -->
                <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                    <div class="card-box height-100-p widget-style3">
                        <?php
                        $status = 2;
                        $sql = "SELECT id from tblleave where RegRemarks=:status";
                        $query = $dbh->prepare($sql);
                        $query->bindParam(':status', $status, PDO::PARAM_STR);
                        $query->execute();
                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                        $leavecount = $query->rowCount();
                        ?>
                        <div class="d-flex flex-wrap">
                            <div class="widget-data">
                                <div class="weight-700 font-24 text-dark"><?php echo($leavecount); ?></div>
                                <div class="font-14 text-secondary weight-500">Rejected Leave</div>
                            </div>
                            <div class="widget-icon">
                                <div class="icon" data-color="#ff5b5b"><i class="icon-copy fa fa-hourglass-o" aria-hidden="true"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="title pb-10">
                <h2 class="h3 mb-0">Employee Leave Information</h2>

                <!-- Add the PDF Generation Buttons -->
                <div style="display: flex; gap: 10px;">
                    <form method="POST" action="employee_report.php">
                        <button type="submit" name="generate_pdf" class="btn btn-primary">Generate PDF Report</button>
                    </form>
                    <!-- <form method="POST" action="bar_chart.php">
                        <button type="submit" name="generate_pdf" class="btn btn-success">Bar Chart</button>
                    </form> -->
                </div>
            </div>

            <div class="row">
                <?php
                // Fetch distinct employees
                $sql = "SELECT DISTINCT tblemployees.emp_id, tblemployees.FirstName, tblemployees.Staff_ID
                        FROM employee_leave
                        JOIN tblemployees ON employee_leave.emp_id = tblemployees.emp_id";
                $query = $dbh->prepare($sql);
                $query->execute();
                $employees = $query->fetchAll(PDO::FETCH_OBJ);

                foreach ($employees as $employee) {
                ?>
                    <div class='col-xl-6 col-lg-6 col-md-12 mb-3'>
                        <div class='card shadow-sm'>
                            <div class='card-header bg-primary text-white d-flex justify-content-between align-items-center' style="cursor: pointer;"
                                data-bs-toggle="collapse" data-bs-target="#leaveDetails-<?php echo $employee->emp_id; ?>">
                                <h5 class='mb-0'><?php echo htmlentities($employee->FirstName); ?> (Staff ID: <?php echo htmlentities($employee->Staff_ID); ?>)</h5>
                                <span class="badge bg-light text-dark">View Details</span>
                            </div>
                            <div id="leaveDetails-<?php echo $employee->emp_id; ?>" class="collapse">
                                <div class='card-body p-3'>
                                    <table class='table table-hover'>
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Leave Type</th>
                                                <th>Available Days</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
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
                                                    <td><?php echo htmlentities($leave->LeaveType); ?></td>
                                                    <td><?php echo htmlentities($leave->available_day); ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

        </div>
    </div>

    <?php include('includes/scripts.php'); ?>
</body>
<!-- <?php include('includes/footer.php'); ?> -->
</html>
