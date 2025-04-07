<?php include('includes/header.php')?>
<?php include('../includes/session.php')?>

<?php
$query = mysqli_query($conn,"select * from tblemployees where emp_id = '$session_id' ")or die(mysqli_error());
$row = mysqli_fetch_array($query);
if(isset($_POST['new_update']))
{
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
	$row = mysqli_fetch_assoc($result);
	$current_hashed_password = $row['Password'];

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

if (isset($_POST["update_image"])) {
	$image = $_FILES['image']['name'];

	if(!empty($image)){
		move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/'.$image);
		$location = $image;	
	} else {
		echo "<script>alert('Please Select Picture to Update');</script>";
		exit();
	}

    $result = mysqli_query($conn, "UPDATE tblemployees SET location='$location' WHERE emp_id='$session_id'") or die(mysqli_error($conn));
    
    if ($result) {
     	echo "<script>alert('Profile Picture Updated');</script>";
     	echo "<script type='text/javascript'> document.location = 'my_profile.php'; </script>";
	} else {
	  die(mysqli_error($conn));
   }
}
?>

<body>
	<?php include('includes/navbar.php')?>

	<?php include('includes/right_sidebar.php')?>

	<?php include('includes/left_sidebar.php')?>

	<div class="mobile-menu-overlay"></div>

	<div class="mobile-menu-overlay"></div>

	<?php
	// Fetch employee details
	$query = mysqli_query($conn, "SELECT * FROM tblemployees WHERE emp_id = '$session_id'") or die(mysqli_error());
	$row = mysqli_fetch_array($query);
	?>

	<div class="main-container">
		<div class="pd-ltr-20 xs-pd-20-10">
			<div class="min-height-200px">
				<div class="page-header">
					<div class="row">
						<div class="col-md-12">
							<div class="title">
								<h4>Profile</h4>
							</div>
							<nav aria-label="breadcrumb">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="admin_dashboard">Dashboard</a></li>
									<li class="breadcrumb-item active" aria-current="page">Profile</li>
								</ol>
							</nav>
						</div>
					</div>
				</div>

				<div class="row">
					<!-- Profile Card -->
					<div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-30">
						<div class="pd-20 card-box height-100-p">
							<div class="profile-photo">
								<a href="#" data-toggle="modal" data-target="#profileModal" class="edit-avatar">
									<i class="fa fa-pencil"></i>
								</a>
								<img src="<?= !empty($row['location']) ? '../uploads/' . $row['location'] : '../uploads/NO-IMAGE-AVAILABLE.jpg' ?>" 
									alt="Profile Picture" class="avatar-photo">
							</div>

							<h5 class="text-center h5 mb-0"><?= htmlspecialchars($row['FirstName']) ?></h5>
							<p class="text-center text-muted font-14"><?= htmlspecialchars($row['Position_Staff']) ?></p>

							<!-- Contact Info -->
							<div class="profile-info">
								<h5 class="mb-20 h5 text-blue">Contact Information</h5>
								<ul>
									<li><strong>Email:</strong> <?= htmlspecialchars($row['EmailId']) ?></li>
									<li><strong>Phone:</strong> <?= htmlspecialchars($row['Phonenumber']) ?></li>
									<li><strong>Role:</strong> <?= htmlspecialchars($row['role']) ?></li>
									<li><strong>Address:</strong> <?= htmlspecialchars($row['Address']) ?></li>
								</ul>

								<h5 class="mb-20 h5 text-blue">Emergency Contact</h5>
								<ul>
									<li><strong>Name:</strong> <?= htmlspecialchars($row['Emergency_Name']) ?></li>
									<li><strong>Relation:</strong> <?= htmlspecialchars($row['Emergency_Relation']) ?></li>
									<li><strong>Phone:</strong> <?= htmlspecialchars($row['Emergency_Contact']) ?></li>
								</ul>
							</div>
						</div>
					</div>

					<!-- Profile Settings -->
					<div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 mb-30">
						<div class="card-box height-100-p overflow-hidden">
							<div class="profile-tab height-100-p">
								<div class="tab height-100-p">
									<ul class="nav nav-tabs customtab">
										<li class="nav-item">
											<a class="nav-link active" data-toggle="tab" href="#settings">Settings</a>
										</li>
									</ul>
									<div class="tab-content">
										<div class="tab-pane fade show active" id="settings">
											<div class="profile-setting">
												<form method="POST" action="update_profile.php">
													<h4 class="text-blue h5 mb-20">Edit Your Personal Settings</h4>

													<div class="row">
														<div class="col-md-6">
															<label>Full Name</label>
															<input type="text" name="fname" class="form-control" required value="<?= htmlspecialchars($row['FirstName']) ?>">
														</div>
														<div class="col-md-6">
															<label>Staff ID</label>
															<input type="text" name="staff_id" class="form-control" required value="<?= htmlspecialchars($row['Staff_ID']) ?>">
														</div>
														<div class="col-md-6">
															<label>Email Address</label>
															<input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($row['EmailId']) ?>">
														</div>
														<div class="col-md-6">
															<label>Phone Number</label>
															<input type="text" name="phonenumber" class="form-control" required value="<?= htmlspecialchars($row['Phonenumber']) ?>">
														</div>
														<div class="col-md-6">
															<label>Date of Birth</label>
															<input type="date" name="dob" class="form-control" required value="<?= htmlspecialchars($row['Dob']) ?>">
														</div>
														<div class="col-md-6">
															<label>Gender</label>
															<select name="gender" class="custom-select form-control" required>
																<option value="<?= $row['Gender'] ?>"><?= ucfirst($row['Gender']) ?></option>
																<option value="male">Male</option>
																<option value="female">Female</option>
															</select>
														</div>
														<div class="col-md-6">
															<label>Address</label>
															<input type="text" name="address" class="form-control" required value="<?= htmlspecialchars($row['Address']) ?>">
														</div>
														<div class="col-md-6">
															<label>Department</label>
															<select name="department" class="custom-select form-control" required>
																<option value="<?= $row['DepartmentShortName'] ?>"><?= $row['DepartmentName'] ?></option>
																<?php
																$deptQuery = mysqli_query($conn, "SELECT * FROM tbldepartments");
																while ($dept = mysqli_fetch_assoc($deptQuery)) {
																	echo "<option value='{$dept['DepartmentShortName']}'>{$dept['DepartmentName']}</option>";
																}
																?>
															</select>
														</div>
														<div class="col-md-6">
															<label>Change Password</label>
															<input type="password" name="password" class="form-control" placeholder="New password (optional)">
														</div>
													</div>

													<h4 class="text-blue h5 mt-4">Emergency Contact</h4>
													<div class="row">
														<div class="col-md-6">
															<label>Name</label>
															<input type="text" name="emergency_name" class="form-control" required value="<?= htmlspecialchars($row['Emergency_Name']) ?>">
														</div>
														<div class="col-md-6">
															<label>Relation</label>
															<input type="text" name="emergency_relation" class="form-control" required value="<?= htmlspecialchars($row['Emergency_Relation']) ?>">
														</div>
														<div class="col-md-6">
															<label>Phone</label>
															<input type="text" name="emergency_contact" class="form-control" required value="<?= htmlspecialchars($row['Emergency_Contact']) ?>">
														</div>
													</div>

													<div class="mt-3 text-center">
														<button type="submit" class="btn btn-primary">Save & Update</button>
													</div>
												</form>
											</div>
										</div>
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