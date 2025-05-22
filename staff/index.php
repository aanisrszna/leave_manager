<?php include('includes/header.php'); ?>
<?php include('../includes/session.php'); ?>

<body>
    <?php include('includes/navbar.php'); ?>
    <?php include('includes/right_sidebar.php'); ?>
    <?php include('includes/left_sidebar.php'); ?>

    <div class="mobile-menu-overlay"></div>

    <div class="main-container">
        <div class="pd-ltr-20">

            <!-- Page Title -->
            <div class="title pb-20">
                <h2 class="h3 mb-0">Leave Entitlement</h2>
            </div>

            <!-- Leave Status Notification -->
            <?php
            $latestLeaveQuery = mysqli_query($conn, "
                SELECT id, RegRemarks, empid, notification_shown 
                FROM tblleave 
                WHERE empid = '$session_id' 
                ORDER BY id DESC 
                LIMIT 1
            ") or die(mysqli_error($conn));

            if (mysqli_num_rows($latestLeaveQuery) > 0) {
                $leave = mysqli_fetch_array($latestLeaveQuery);
                $leave_id = $leave['id'];
                $notification_shown = $leave['notification_shown'];

                if ($notification_shown == 0) {
                    $notification_displayed = false;

                    if ($leave['RegRemarks'] == 1) {
                        echo "<div class='alert alert-success'>🎉 Your Leave Has Been Approved!</div>";
                        $notification_displayed = true;
                    } elseif ($leave['RegRemarks'] == 2) {
                        echo "<div class='alert alert-danger'>😔 Sorry, Your Leave Has Been Rejected.</div>";
                        $notification_displayed = true;
                    }

                    if ($notification_displayed) {
                        mysqli_query($conn, "
                            UPDATE tblleave 
                            SET notification_shown = 1 
                            WHERE id = '$leave_id'
                        ") or die(mysqli_error($conn));
                    }
                }
            } else {
                echo "<div class='alert alert-warning'>No leave record found for this employee.</div>";
            }
            ?>

            <!-- Leave Entitlement Cards -->
            <div class="row pb-10">
                <?php
                $session_id = mysqli_real_escape_string($conn, $session_id);
                $query = mysqli_query($conn, "
                    SELECT 
                        el.leave_type_id,
                        lt.LeaveType,
                        el.available_day,
                        lt.assigned_day
                    FROM 
                        employee_leave el
                    INNER JOIN 
                        tblleavetype lt ON el.leave_type_id = lt.id
                    WHERE 
                        el.emp_id = '$session_id'
                        AND lt.LeaveType NOT IN ('Emergency Leave', 'Unpaid Leave')
                ") or die(mysqli_error($conn));

                while ($row = mysqli_fetch_array($query)) {
                    $assigned_day = max($row['assigned_day'], 1);
                    $progress_percentage = ($row['available_day'] / $assigned_day) * 100;
                ?>
                    <div class="col-xl-3 col-lg-6 col-md-12 mb-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-body text-center">
                                <i class="fas fa-calendar-check fa-2x text-primary mb-3"></i>
                                <h5 class="card-title font-weight-bold text-dark" style="font-size: 1rem;">
                                    <?= htmlspecialchars($row['LeaveType']); ?>
                                </h5>
                                <p class="text-muted" style="font-size: 0.8rem;">
                                    Available Days: <strong><?= htmlspecialchars($row['available_day']); ?></strong> /
                                    <strong><?= htmlspecialchars($row['assigned_day']); ?></strong>
                                </p>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: <?= min($progress_percentage, 100); ?>%;"
                                        aria-valuenow="<?= $row['available_day']; ?>"
                                        aria-valuemin="0"
                                        aria-valuemax="<?= $assigned_day; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <!-- Calendar & Pie Chart Section -->
            <div class="row">
                <div class="col-md-6">
                    <?php include('../calendar.php'); ?>
                </div>
                <div class="col-md-6">
                    <?php include('../piechart.php'); ?>
                </div>
            </div>

            <!-- Organization Chart Section -->
            <div class="row justify-content-center mt-4">
                <div class="col-md-10">
                    <div class="card shadow-sm border-0">
                        <div class="card-header text-center">
                            <h5 class="mb-0">Organization Chart</h5>
                        </div>
                        <div class="card-body text-center">
                            <img src="../vendors/images/organization.png" alt="Organization Chart"
                                class="img-fluid" style="max-width: 100%; height: auto;" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Spacer -->
            <div class="my-5"></div>

            <?php include('includes/footer.php'); ?>
        </div>
    </div>

    <?php include('includes/scripts.php'); ?>
</body>
</html>
