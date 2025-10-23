<?php include('includes/header.php')?>
<?php include('../includes/session.php')?>
<body>

    <?php include('includes/navbar.php')?>
    <?php include('includes/right_sidebar.php')?>
    <?php include('includes/left_sidebar.php')?>


    <div class="mobile-menu-overlay"></div>

    <div class="main-container">
        <div class="pd-ltr-20">

            <div class="row justify-content-center">
                <div class="col-md-10 text-center">
                    <?php
                    $orgPathFs = realpath(__DIR__ . '/../vendors/images/org.png');
                    $orgUrl    = '../vendors/images/org.png';
                    $ver       = ($orgPathFs && file_exists($orgPathFs)) ? filemtime($orgPathFs) : time();
                    ?>
                    <img src="<?= $orgUrl ?>?v=<?= $ver ?>" alt="Organization Logo"
                         class="img-fluid" style="max-width: 100%; height: auto;" />
                </div>
            </div>

        </div>
    </div>

</body>
