<?php
include('includes/header.php');
include('../includes/session.php');

$logoDir  = realpath(__DIR__ . '/../vendors/images');
$logoName = 'org.png';
$logoFile = $logoDir . DIRECTORY_SEPARATOR . $logoName;

$msg = null;
$error = null;

// Handle upload and replace
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['replace_logo'])) {
    if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Upload failed. Please choose a PNG file.';
    } else {
        $f = $_FILES['logo'];

        // Validate file type & size
        $isPng = mime_content_type($f['tmp_name']) === 'image/png';
        if ($f['size'] > 2 * 1024 * 1024) {
            $error = 'File too large (max 2 MB).';
        } elseif (!$isPng) {
            $error = 'Please upload a PNG file only.';
        } else {
            // Replace old logo
            if (file_exists($logoFile)) {
                unlink($logoFile); // delete old
            }
            if (move_uploaded_file($f['tmp_name'], $logoFile)) {
                $msg = 'Logo replaced successfully.';
            } else {
                $error = 'Failed to replace the logo.';
            }
        }
    }
}
?>

<body>
    <?php include('includes/navbar.php')?>
    <?php include('includes/right_sidebar.php')?>
    <?php include('includes/left_sidebar.php')?>


    <div class="mobile-menu-overlay"></div>

    <div class="main-container">
        <div class="pd-ltr-20">

            <?php if ($msg): ?>
                <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="row justify-content-center mb-3">
                <div class="col-md-10 text-center">
                    <?php $ver = file_exists($logoFile) ? filemtime($logoFile) : time(); ?>
                    <img src="../vendors/images/<?= $logoName ?>?v=<?= $ver ?>"
                         class="img-fluid"
                         style="max-width:100%; height:auto;" alt="Organization Logo">
                </div>
            </div>

            <!-- EDIT BUTTON SECTION -->
            <div class="row justify-content-center">
                <div class="col-md-10 text-center">
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="replace_logo" value="1">
                        <input type="file" id="logoFile" name="logo" accept="image/png"
                               style="display:none" onchange="this.form.submit()">
                        <button type="button" class="btn btn-primary"
                                onclick="document.getElementById('logoFile').click();">
                            <i class="fa fa-edit"></i> Edit Logo
                        </button>
                        <small class="d-block mt-2 text-muted">
                            Upload a new <code>org.png</code> (PNG only, max 2 MB)
                        </small>
                    </form>
                </div>
            </div>

        </div>
    </div>
</body>
