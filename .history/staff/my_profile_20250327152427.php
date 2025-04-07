<?php include('includes/header.php')?>
<?php include('../includes/session.php')?>

<?php
// Ensure database connection is available
$query = mysqli_query($conn, "SELECT * FROM tblemployees WHERE emp_id = '$session_id'") or die(mysqli_error($conn));
$row = mysqli_fetch_array($query);

// Debugging: Check if data is retrieved correctly
echo "<pre>";
print_r($row);
echo "</pre>";

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
	$row_password = mysqli_fetch_assoc($result);
	$current_hashed_password = $row_password['Password'] ?? '';

	// Hash the password only if the user enters a new one
	$hashed_password = !empty($password) ? password_hash($password, PASSWORD_BCRYPT) : $current_hashed_password;

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

<!-- HTML Form -->
<form method="POST">
    <input type="text" name="car_plate" class="form-control" value="<?= $row['Car_Plate'] ?? '' ?>">
    <input type="text" name="emergency_name" class="form-control" value="<?= $row['Emergency_Name'] ?? '' ?>">
    <input type="text" name="emergency_relation" class="form-control" value="<?= $row['Emergency_Relation'] ?? '' ?>">
    <input type="text" name="emergency_contact" class="form-control" value="<?= $row['Emergency_Contact'] ?? '' ?>">
</form>
