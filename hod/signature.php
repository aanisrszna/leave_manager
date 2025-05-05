<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>E-Leave Manager</title>

    <!-- CSS and JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/south-street/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

    <!-- Signature Pad -->
    <link rel="stylesheet" href="../assets/css/jquery.signature.css">
    <link rel="stylesheet" href="../src/css/jquery.signature.css">
    <script src="../src/js/jquery.signature.js"></script>

    <!-- Meta & Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="../vendors/images/riverraven.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../vendors/images/riverraven.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../vendors/images/riverraven.png">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!-- Fonts and Styles -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../vendors/styles/core.css">
    <link rel="stylesheet" href="../vendors/styles/icon-font.min.css">
    <link rel="stylesheet" href="../src/plugins/jquery-steps/jquery.steps.css">
    <link rel="stylesheet" href="../src/plugins/datatables/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../src/plugins/datatables/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="../vendors/styles/style.css">

    <!-- Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-119386393-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'UA-119386393-1');
    </script>

    <!-- Signature Style -->
    <style>
        .kbw-signature { width: 400px; height: 200px;}
        #sig canvas { width: 100% !important; height: auto; }
    </style>
</head>

<?php include('../includes/config.php'); ?>
<?php include('../includes/session.php'); ?>

<?php 
if(isset($_POST['upload']))
{
    $query= mysqli_query($conn,"select * from tblemployees where emp_id = '$session_id'")or die(mysqli_error());
    $row = mysqli_fetch_assoc($query);

    $firstname = $row['FirstName'];
    $cut = substr($firstname, 1, 2);
    $phone = $row['Phonenumber'];
    $folderPath = "../signature/";

    $filename = "";

    // If PNG file is uploaded
    if (!empty($_FILES['signature_file']['name'])) {
        $uploadFile = $_FILES['signature_file'];
        $fileType = strtolower(pathinfo($uploadFile['name'], PATHINFO_EXTENSION));

        if ($fileType === 'png') {
            $filename = "uploadhod_" . $cut . "_" . $phone . "_" . $session_id . ".png";
            $filePath = $folderPath . $filename;
            move_uploaded_file($uploadFile["tmp_name"], $filePath);
        } else {
            echo "<script>alert('Only PNG files are allowed.');</script>";
            exit;
        }
    } else if (!empty($_POST['signed'])) {
        // If canvas signature is submitted
        $image_parts = explode(";base64,", $_POST['signed']);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);

        $filename = "hod_" . $cut . "_" . $phone . "_" . $session_id . "." . $image_type;
        $filePath = $folderPath . $filename;
        file_put_contents($filePath, $image_base64);
    }

    if ($filename !== "") {
        $result = mysqli_query($conn,"update tblemployees set signature='$filename' where emp_id='$session_id'")or die(mysqli_error());
        echo "<script>alert('Signature saved successfully');</script>";
    }
}
?>

<body>

<?php include('includes/navbar.php'); ?>
<?php include('includes/right_sidebar.php'); ?>
<?php include('includes/left_sidebar.php'); ?>

<div class="mobile-menu-overlay"></div>

<div class="main-container">
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="title">
                            <h4>Signature List</h4>
                        </div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="admin_dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Signature Module</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Signature Form -->
                <div class="col-lg-6 col-md-6 col-sm-12 mb-30">
                    <div class="card-box pd-30 pt-10 height-100-p">
                        <h2 class="mb-30 h4">Signature Canvas</h2>
                        <form method="POST" enctype="multipart/form-data">
                            <div id="sig"></div>
                            <br>
                            <button class="btn btn-outline-danger" id="clear">Clear Signature</button>
                            <br><br>
                            <label>Or Upload Signature (PNG only):</label>
                            <input type="file" name="signature_file" accept="image/png" class="form-control-file mb-3">
                            <textarea id="signature64" name="signed" style="display: none"></textarea>
                            <button class="btn btn-primary mt-2" name="upload">Submit Signature</button>
                        </form>
                    </div>
                </div>

                <!-- Signature Display -->
                <div class="col-lg-6 col-md-6 col-sm-12 mb-30">
                    <div class="card-box pd-30 pt-10 height-100-p">
                        <h2 class="mb-30 h4">Signature File</h2>
                        <div class="pb-20">
                            <table class="data-table table stripe hover nowrap">
                                <thead>
                                    <tr>
                                        <th class="table-plus">SIGNATURE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $query= mysqli_query($conn,"select * from tblemployees where emp_id = '$session_id'")or die(mysqli_error());
                                    while ($row = mysqli_fetch_array($query)) {
                                        $id = $row['emp_id'];
                                    ?>
                                    <tr>
                                        <td class="table-plus">
                                            <div class="name-avatar d-flex align-items-center">
                                                <div class="avatar mr-2 flex-shrink-0">
                                                    <img src="<?php echo (!empty($row['signature'])) ? '../signature/'.$row['signature'] : '../signature/NO-IMAGE-AVAILABLE.jpg'; ?>" class="border-radius-100 shadow" width="100" height="70" alt="">
                                                </div>
                                                <div class="txt">
                                                    <div class="weight-600"><?php echo $row['FirstName']; ?></div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php } ?>  
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>  
</div>

<!-- JS Scripts -->
<script>
    var sig = $('#sig').signature({syncField: '#signature64', syncFormat: 'PNG'});
    $('#clear').click(function(e) {
        e.preventDefault();
        sig.signature('clear');
        $("#signature64").val('');
    });
</script>

<script src="../vendors/scripts/core.js"></script>
<script src="../vendors/scripts/script.min.js"></script>
<script src="../vendors/scripts/process.js"></script>
<script src="../vendors/scripts/layout-settings.js"></script>

</body>
</html>
