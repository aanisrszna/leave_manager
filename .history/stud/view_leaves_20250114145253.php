<?php error_reporting(0); ?>
<?php include('includes/header.php') ?>
<?php include('../includes/session.php') ?>

<style>
    input[type="text"] {
        font-size: 16px;
        color: #0f0d1b;
        font-family: Verdana, Helvetica;
    }

    .btn-outline:hover {
        color: #fff;
        background-color: #524d7d;
        border-color: #524d7d;
    }

    textarea {
        font-size: 16px;
        color: #0f0d1b;
        font-family: Verdana, Helvetica;
    }

    textarea.text_area {
        height: 8em;
        font-size: 16px;
        color: #0f0d1b;
        font-family: Verdana, Helvetica;
    }
</style>

<body>

    <?php include('includes/navbar.php') ?>
    <?php include('includes/right_sidebar.php') ?>
    <?php include('includes/left_sidebar.php') ?>

    <div class="mobile-menu-overlay"></div>

    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="title">
                                <h4>LEAVE DETAILS</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Leave</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="pd-20 card-box mb-30">
                    <div class="clearfix">
                        <div class="pull-left">
                            <h4 class="text-blue h4">Leave Details</h4>
                            <p class="mb-20"></p>
                        </div>
                    </div>
                    <form method="post" action="">

                        <?php
                        if (!isset($_GET['edit']) && empty($_GET['edit'])) {
                            header('Location: index.php');
                        } else {

                            $lid = intval($_GET['edit']);
                            $sql = "SELECT tblleave.id as lid, tblemployees.FirstName, tblemployees.LastName, tblemployees.emp_id, tblemployees.Gender, tblemployees.Phonenumber, tblemployees.EmailId, tblemployees.Position_Staff, tblemployees.Staff_ID, tblleave.LeaveType, tblleave.ToDate, tblleave.FromDate, tblleave.RequestedDays, tblleave.PostingDate, tblleave.DaysOutstand, tblleave.Sign, tblleave.WorkCovered, tblleave.HodRemarks, tblleave.RegRemarks, tblleave.HodSign, tblleave.RegSign, tblleave.HodDate, tblleave.RegDate, tblleave.RequestedDays from tblleave join tblemployees on tblleave.empid=tblemployees.emp_id where tblleave.id=:lid";
                            $query = $dbh->prepare($sql);
                            $query->bindParam(':lid', $lid, PDO::PARAM_STR);
                            $query->execute();
                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                            $cnt = 1;
                            if ($query->rowCount() > 0) {
                                foreach ($results as $result) {
                        ?>

                                    <div class="row">
                                        <div class="col-md-4 col-sm-12">
                                            <div class="form-group">
                                                <label style="font-size:16px;"><b>Full Name</b></label>
                                                <input type="text" class="selectpicker form-control" data-style="btn-outline-primary" readonly value="<?php echo htmlentities($result->FirstName . " " . $result->LastName); ?>">
                                            </div>
                                        </div>
                                        <!-- Remaining code for other fields -->
                                    </div>
                                    <!-- Continue with the remaining fields -->
                        <?php
                                }
                            }
                        }
                        ?>
                    </form>
                </div>
            </div>
            <?php include('includes/footer.php'); ?>
        </div>
    </div>
    <!-- js -->

    <?php include('includes/scripts.php') ?>
