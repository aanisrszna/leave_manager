<?php
session_start();
include('includes/config.php');

if (isset($_POST['signin'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Step 1: Retrieve the hashed password from the database
    $stmt = $conn->prepare("SELECT emp_id, role, Department, Password FROM tblemployees WHERE EmailId = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stored_hash = $row['Password']; // Hashed password from DB

        // Step 2: Verify using password_verify()
        if (password_verify($password, $stored_hash)) {
            $_SESSION['alogin'] = $row['emp_id'];
            $_SESSION['arole'] = $row['role'];
            $_SESSION['adepart'] = $row['Department'];


            // Step 3: Redirect based on role
            switch ($row['role']) {
                case 'Admin':
                    echo "<script type='text/javascript'> document.location = 'admin/admin_dashboard.php'; </script>";
                    break;
                case 'Staff':
                    echo "<script type='text/javascript'> document.location = 'staff/index.php'; </script>";
                    break;
                case 'Director':
                    echo "<script type='text/javascript'> document.location = 'director/index.php'; </script>";
                    break;
                default:
                    echo "<script type='text/javascript'> document.location = 'hod/index.php'; </script>";
                    break;
            }
        } else {
            echo "<script>alert('Invalid email or password.');</script>";
        }
    } else {
        echo "<script>alert('Invalid email or password.');</script>";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
	<!-- Basic Page Info -->
	<meta charset="utf-8">
	<title>E-Leave Manager System</title>

	<!-- Site favicon -->
	<link rel="apple-touch-icon" sizes="180x180" href="vendors/images/riverraven.png">
	<link rel="icon" type="image/png" sizes="32x32" href="vendors/images/riverraven.png">
	<link rel="icon" type="image/png" sizes="16x16" href="vendors/images/riverraven.png">

	<!-- Mobile Specific Metas -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<!-- Google Font -->
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="vendors/styles/core.css">
	<link rel="stylesheet" type="text/css" href="vendors/styles/icon-font.min.css">
	<link rel="stylesheet" type="text/css" href="vendors/styles/style.css">

	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-119386393-1"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());

		gtag('config', 'UA-119386393-1');
	</script>
</head>
<body class="login-page">
	<div class="login-header box-shadow">
		<div class="container-fluid d-flex justify-content-between align-items-center">
			<div class="brand-logo">
				<a href="login.html">
					<img src="vendors/images/riverraven.png" alt="">
				</a>
			</div>
		</div>
	</div>
	<div class="login-wrap d-flex align-items-center flex-wrap justify-content-center">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-md-6 col-lg-7">
					<img src="vendors/images/login-page-img.png" alt="">
				</div>
				<div class="col-md-6 col-lg-5">
					<div class="login-box bg-white box-shadow border-radius-10">
						<div class="login-title">
							<h2 class="text-center text-primary">E-Leave Manager River Raven</h2>
						</div>
						<form name="signin" method="post">
						
							<div class="input-group custom">
								<input type="text" class="form-control form-control-lg" placeholder="Email ID" name="username" id="username">
								<div class="input-group-append custom">
									<span class="input-group-text"><i class="icon-copy fa fa-envelope-o" aria-hidden="true"></i></span>
								</div>
							</div>
							<div class="input-group custom">
								<input type="password" class="form-control form-control-lg" placeholder="Password"name="password" id="password">
								<div class="input-group-append custom">
									<span class="input-group-text"><i class="dw dw-padlock1"></i></span>
								</div>
							</div>
						
							<div class="row">
								<div class="col-sm-12">
									<div class="input-group mb-0">
									   <input class="btn btn-primary btn-lg btn-block" name="signin" id="signin" type="submit" value="Sign In">
									</div>
								</div>
							</div>
							<a href="forgot_password.php">Forgot Password?</a>

						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- js -->
	<script src="vendors/scripts/core.js"></script>
	<script src="vendors/scripts/script.min.js"></script>
	<script src="vendors/scripts/process.js"></script>
	<script src="vendors/scripts/layout-settings.js"></script>
</body>
</html>