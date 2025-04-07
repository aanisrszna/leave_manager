<?php
include('includes/header.php');
include('../includes/session.php');

// Check session and redirect if expired
if (!isset($_SESSION['emp_id'])) {
    header("Location: login.php"); // Redirect to login page
    exit();
}
$emp_id = $_SESSION['emp_id'];
?>
<body>
    <?php include('includes/navbar.php') ?>
    <?php include('includes/right_sidebar.php') ?>
    <?php include('includes/left_sidebar.php') ?>

    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="card-box pd-20 height-100-p mb-30"></div>
            <div class="title pb-20">
                <h2 class="h3 mb-0">Your Leave Balance</h2>
            </div>
            <div class="row pb-10">
                <?php
                $sql = "SELECT lt.LeaveType AS leave_name, el.available_day AS available_days
                        FROM tblleavetype lt
                        JOIN employee_leave el ON lt.id = el.leave_type_id
                        WHERE el.emp_id = :emp_id";
                $query = $dbh->prepare($sql);
                $query->bindParam(':emp_id', $emp_id, PDO::PARAM_INT);
                $query->execute();
                $results = $query->fetchAll(PDO::FETCH_OBJ);

                if ($query->rowCount() > 0) {
                    foreach ($results as $result) { ?>
                        <div class="col-xl-4 col-lg-4 col-md-6 mb-20">
                            <div class="card-box height-100-p widget-style3">
                                <div class="d-flex flex-wrap">
                                    <div class="widget-data">
                                        <div class="weight-700 font-24 text-dark">
                                            <?php echo htmlentities($result->available_days); ?>
                                        </div>
                                        <div class="font-14 text-secondary weight-500">
                                            <?php echo htmlentities($result->leave_name); ?>
                                        </div>
                                    </div>
                                    <div class="widget-icon">
                                        <div class="icon" data-color="#00eccf">
                                            <i class="icon-copy dw dw-calendar"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }
                } else { ?>
                    <div class="col-12">
                        <div class="alert alert-warning text-center">
                            No leave balance data available.
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</body>
