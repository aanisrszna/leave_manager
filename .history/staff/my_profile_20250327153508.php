<?php include('includes/header.php')?>
<?php include('../includes/session.php')?>

<?php
// Fetch user details

// Update profile information
if (isset($_POST['new_update'])) {
    $staff_id = $_POST['staff_id'];
    $empid = $session_id;
    $fname = $_POST['fname'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $password = $_POST['password'];
    $department = $_POST['department'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];
    $phonenumber = $_POST['phonenumber'];
    $emergency_name = $_POST['emergency_name'];
    $emergency_relation = $_POST['emergency_relation'];
    $emergency_contact = $_POST['emergency_contact'];
    $date_joined = $_POST['date_joined'];
    $service_year = $_POST['service_year'];
    $car_plate = $_POST['car_plate'];

    // Fetch current password from database
    $result = mysqli_query($conn, "SELECT Password FROM tblemployees WHERE emp_id='$session_id'");
    if ($result && mysqli_num_rows($result) > 0) {
        $row_password = mysqli_fetch_assoc($result);
        $current_hashed_password = $row_password['Password'];
    } else {
        $current_hashed_password = null;
    }

    // Hash the password only if the user enters a new one
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    } else {
        $hashed_password = $current_hashed_password; // Keep the old password if no new one is entered
    }

    $query = "UPDATE tblemployees SET 
        FirstName='$fname', 
        EmailId='$email', 
        Gender='$gender', 
        Dob='$dob', 
        Password='$hashed_password', 
        Department='$department', 
        Address='$address', 
        Phonenumber='$phonenumber', 
        Staff_ID='$staff_id', 
        Emergency_Name='$emergency_name', 
        Emergency_Relation='$emergency_relation', 
        Emergency_Contact='$emergency_contact', 
        date_joined='$date_joined', 
        Service_Year='$service_year', 
        Car_Plate='$car_plate' 
        WHERE emp_id='$session_id'";

    $result = mysqli_query($conn, $query);

    if ($result) {
        echo "<script>alert('Your records Successfully Updated');</script>";
        echo "<script type='text/javascript'> document.location = 'my_profile.php'; </script>";
    } else {
        die(mysqli_error($conn));
    }
}

// Update profile picture
if (isset($_POST["update_image"])) {
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($image);

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $query = "UPDATE tblemployees SET location='$image' WHERE emp_id='$session_id'";
            $result = mysqli_query($conn, $query);

            if ($result) {
                echo "<script>alert('Profile Picture Updated');</script>";
                echo "<script type='text/javascript'> document.location = 'my_profile.php'; </script>";
            } else {
                die(mysqli_error($conn));
            }
        } else {
            echo "<script>alert('Error uploading file.');</script>";
        }
    } else {
        echo "<script>alert('Please Select Picture to Update');</script>";
    }
}
?>

<body>
	<?php include('includes/navbar.php')?>

	<?php include('includes/right_sidebar.php')?>

	<?php include('includes/left_sidebar.php')?>

	<div class="mobile-menu-overlay"></div>

	<div class="mobile-menu-overlay"></div>

	<div class="main-container">
		<div class="pd-ltr-20 xs-pd-20-10">
			<div class="min-height-200px">
				<div class="page-header">
					<div class="row">
						<div class="col-md-12 col-sm-12">
							<div class="title">
								<h4>Profile</h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="admin_dashboard">Dashboard</a></li>
									<li class="breadcrumb-item active" aria-current="page">Profile</li>
								</ol>
							</nav>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-30">
						<div class="pd-20 card-box height-100-p">

							<?php $query= mysqli_query($conn,"select * from tblemployees LEFT JOIN tbldepartments ON tblemployees.Department = tbldepartments.DepartmentShortName where emp_id = '$session_id'")or die(mysqli_error());
								$row = mysqli_fetch_array($query);
							?>

							<div class="profile-photo">
								<a href="modal" data-toggle="modal" data-target="#modal" class="edit-avatar"><i class="fa fa-pencil"></i></a>
								<img src="<?php echo (!empty($row['location'])) ? '../uploads/'.$row['location'] : '../uploads/NO-IMAGE-AVAILABLE.jpg'; ?>" alt="" class="avatar-photo">
								<form method="post" enctype="multipart/form-data">
									<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
										<div class="modal-dialog modal-dialog-centered" role="document">
											<div class="modal-content">
												<div class="weight-500 col-md-12 pd-5">
													<div class="form-group">
														<div class="custom-file">
															<input name="image" id="file" type="file" class="custom-file-input" accept="image/*" onchange="validateImage('file')">
															<label class="custom-file-label" for="file" id="selector">Choose file</label>		
														</div>
													</div>
												</div>
												<div class="modal-footer">
													<input type="submit" name="update_image" value="Update" class="btn btn-primary">
													<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
												</div>
											</div>
										</div>
									</div>
								</form>
							</div>
							<h5 class="text-center h5 mb-0"><?php echo $row['FirstName']; ?></h5>
							<p class="text-center text-muted font-14"><?php echo $row['Position_Staff']; ?></p>
							<div class="profile-info">
								<h5 class="mb-20 h5 text-blue">Contact Information</h5>
								<ul>
									<li>
										<span>Email Address:</span>
										<?php echo $row['EmailId']; ?>
									</li>
									<li>
										<span>Phone Number:</span>
										<?php echo $row['Phonenumber']; ?>
									</li>
									<li>
										<span>My Role:</span>
										<?php echo $row['role']; ?>
									</li>
									<li>
										<span>Address:</span>
										<?php echo $row['Address']; ?>
									</li>
								</ul>

								<br><br> <!-- Adds spacing -->

								<h5 class="mb-20 h5 text-blue">Emergency Information</h5>
								<ul>
									<li>
										<span>Name:</span>
										<?php echo $row['Emergency_Name']; ?>
									</li>
									<li>
										<span>Relation:</span>
										<?php echo $row['Emergency_Relation']; ?>
									</li>
									<li>
										<span>Phone Number:</span>
										<?php echo $row['Emergency_Contact']; ?>
									</li>
								</ul>
							</div>

						</div>
					</div>
					<div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 mb-30">
						<div class="card-box height-100-p overflow-hidden">
							<div class="profile-tab height-100-p">
								<div class="tab height-100-p">
									<ul class="nav nav-tabs customtab" role="tablist">
										
										<li class="nav-item">
											<a class="nav-link" data-toggle="tab" href="#setting" role="tab">Settings</a>
										</li>
									</ul>
									<div class="tab-content">
										
										<!-- Setting Tab start -->
										<!-- Setting Tab start -->
										<div class="tab-pane fade height-100-p" id="setting" role="tabpanel">
											<div class="profile-setting">
												<form method="POST" enctype="multipart/form-data">
													<div class="profile-edit-list row">
														<div class="col-md-12"><h4 class="text-blue h5 mb-20">Edit Your Personal Setting</h4></div>

														<?php
														$query = mysqli_query($conn, "SELECT * FROM tblemployees WHERE emp_id = '$session_id'") or die(mysqli_error());
														$row = mysqli_fetch_array($query);
														?>
														
														<!-- Full Name -->
														<div class="weight-500 col-md-6">
															<div class="form-group">
																<label>Full Name</label>
																<input name="fname" class="form-control form-control-lg" type="text" required autocomplete="off" value="<?php echo $row['FirstName']; ?>">
															</div>
														</div>

														<!-- Staff ID -->
														<div class="weight-500 col-md-6">
															<div class="form-group">
																<label>Staff ID</label>
																<input name="staff_id" class="form-control form-control-lg" type="text" required autocomplete="off" value="<?php echo $row['Staff_ID']; ?>">
															</div>
														</div>

														<!-- Email Address -->
														<div class="weight-500 col-md-6">
															<div class="form-group">
																<label>Email Address</label>
																<input name="email" class="form-control form-control-lg" type="text" required autocomplete="off" value="<?php echo $row['EmailId']; ?>">
															</div>
														</div>

														<!-- Phone Number -->
														<div class="weight-500 col-md-6">
															<div class="form-group">
																<label>Phone Number</label>
																<input name="phonenumber" class="form-control form-control-lg" type="text" required autocomplete="off" value="<?php echo $row['Phonenumber']; ?>">
															</div>
														</div>

														<!-- Date of Birth -->
														<div class="weight-500 col-md-6">
															<div class="form-group">
																<label>Date Of Birth</label>
																<input name="dob" class="form-control form-control-lg date-picker" type="text" required autocomplete="off" value="<?php echo $row['Dob']; ?>">
															</div>
														</div>

														<!-- Gender -->
														<div class="weight-500 col-md-6">
															<div class="form-group">
																<label>Gender</label>
																<select name="gender" class="custom-select form-control" required>
																	<option value="<?php echo $row['Gender']; ?>"><?php echo $row['Gender']; ?></option>
																	<option value="male">Male</option>
																	<option value="female">Female</option>
																</select>
															</div>
														</div>

														<!-- Date Joined -->
														<div class="weight-500 col-md-6">
															<div class="form-group">
																<label>Date Joined</label>
																<input name="date_joined" class="form-control form-control-lg date-picker" type="text" required autocomplete="off" value="<?php echo $row['date_joined']; ?>">
															</div>
														</div>

														<!-- Service Year -->
														<div class="weight-500 col-md-6">
															<div class="form-group">
																<label>Service Year</label>
																<input name="service_year" class="form-control" type="text" required autocomplete="off" value="<?php echo $row['Service_Year']; ?>">
															</div>
														</div>

														<!-- Address -->
														<div class="weight-500 col-md-12">
															<div class="form-group">
																<label>Address</label>
																<input name="address" class="form-control form-control-lg" type="text" required autocomplete="off" value="<?php echo $row['Address']; ?>">
															</div>
														</div>

														<!-- Department -->
														<div class="weight-500 col-md-4">
															<div class="form-group">
																<label>Department</label>
																<select name="department" class="custom-select form-control" required>
																	<?php
																	$query_staff = mysqli_query($conn, "SELECT * FROM tblemployees JOIN tbldepartments WHERE emp_id = '$session_id'") or die(mysqli_error());
																	$row_staff = mysqli_fetch_array($query_staff);
																	?>
																	<option value="<?php echo $row_staff['DepartmentShortName']; ?>"><?php echo $row_staff['DepartmentName']; ?></option>
																	<?php
																	$query = mysqli_query($conn, "SELECT * FROM tbldepartments");
																	while ($row = mysqli_fetch_array($query)) {
																	?>
																	<option value="<?php echo $row['DepartmentShortName']; ?>"><?php echo $row['DepartmentName']; ?></option>
																	<?php } ?>
																</select>
															</div>
														</div>

														<!-- Car Plate -->
														<div class="weight-500 col-md-4">
															<div class="form-group">
																<label>Car Plate</label>
																<input name="car_plate" class="form-control" type="text" required autocomplete="off" value="<?php echo $row['Car_Plate']; ?>">
															</div>
														</div>

														<!-- Emergency Contact Name -->
														<div class="weight-500 col-md-4">
															<div class="form-group">
																<label>Emergency Contact Name</label>
																<input name="emergency_name" class="form-control" type="text" required autocomplete="off" value="<?php echo $row['Emergency_Name']; ?>">
															</div>
														</div>

														<!-- Emergency Relation -->
														<div class="weight-500 col-md-4">
															<div class="form-group">
																<label>Emergency Relation</label>
																<input name="emergency_relation" class="form-control" type="text" required autocomplete="off" value="<?php echo $row['Emergency_Relation']; ?>">
															</div>
														</div>

														<!-- Emergency Contact Number -->
														<div class="weight-500 col-md-4">
															<div class="form-group">
																<label>Emergency Contact Number</label>
																<input name="emergency_contact" class="form-control" type="text" required autocomplete="off" value="<?php echo $row['Emergency_Contact']; ?>">
															</div>
														</div>

														<!-- Change Password -->
														<div class="weight-500 col-md-4">
															<div class="form-group">
																<label>Change Password</label>
																<input name="password" type="password" class="form-control wizard-required" autocomplete="off" placeholder="Enter new password (optional)">
															</div>
														</div>

														<!-- Save & Update Button -->
														<div class="weight-500 col-md-6">
															<div class="form-group">
																<div class="modal-footer justify-content-center">
																	<button class="btn btn-primary" name="new_update" id="new_update" data-toggle="modal">Save & Update</button>
																</div>
															</div>
														</div>
													</div>
												</form>
											</div>
										</div>
										<!-- Setting Tab End -->

										<!-- Setting Tab End -->
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php include('includes/footer.php'); ?>
		</div>
	</div>
	<!-- js -->
	<?php include('includes/scripts.php')?>

	<script type="text/javascript">
		var loader = function(e) {
			let file = e.target.files;

			let show = "<span>Selected file : </span>" + file[0].name;
			let output = document.getElementById("selector");
			output.innerHTML = show;
			output.classList.add("active");
		};

		let fileInput = document.getElementById("file");
		fileInput.addEventListener("change", loader);
	</script>
	<script type="text/javascript">
		 function validateImage(id) {
		    var formData = new FormData();
		    var file = document.getElementById(id).files[0];
		    formData.append("Filedata", file);
		    var t = file.type.split('/').pop().toLowerCase();
		    if (t != "jpeg" && t != "jpg" && t != "png") {
		        alert('Please select a valid image file');
		        document.getElementById(id).value = '';
		        return false;
		    }
		    if (file.size > 1050000) {
		        alert('Max Upload size is 1MB only');
		        document.getElementById(id).value = '';
		        return false;
		    }

		    return true;
		}
	</script>
</body>
</html>