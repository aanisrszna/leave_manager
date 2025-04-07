<?php include('includes/header.php') ?>
<?php include('../includes/session.php') ?>
<body>

    <?php include('includes/navbar.php') ?>
    <?php include('includes/right_sidebar.php') ?>
    <?php include('includes/left_sidebar.php') ?>

    <div class="mobile-menu-overlay"></div>

    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="row">
                <div class="col-lg-12 col-md-12 mb-20">
                    <div class="card-box height-100-p pd-20 min-height-200px">
                        <div class="d-flex justify-content-between pb-10">
                            <div class="h5 mb-0">Employee Leave Types</div>
                        </div>
                        <div class="user-list">
                            <ul>
                                <?php
                                // Correct JOIN Query to Fetch Leave Types
                                $query = mysqli_query($conn, "
                                    SELECT 
                                        el.emp_id,
                                        lt.LeaveType,
                                        lt.Description,
                                        el.available_day
                                    FROM 
                                        employee_leave el
                                    INNER JOIN 
                                        tblleavetype lt 
                                    ON 
                                        el.leave_type_id = lt.id
                                    WHERE 
                                        el.emp_id = '$session_id'
                                ") or die(mysqli_error($conn));

                                while ($row = mysqli_fetch_array($query)) {
                                    ?>
                                    <li class="d-flex align-items-center justify-content-between">
                                        <div class="txt">
                                            <div class="font-14 weight-600">Leave Type: <?php echo $row['LeaveType']; ?></div>
                                            <div class="font-12 weight-500" data-color="#b2b1b6">Description: <?php echo $row['Description']; ?></div>
                                            <div class="font-12 weight-500" data-color="#17a2b8">Available Days: <?php echo $row['available_day']; ?></div>
                                        </div>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <?php include('includes/footer.php'); ?>
        </div>
    </div>

    <!-- js -->
    <?php include('includes/scripts.php') ?>
</body>
</html>
