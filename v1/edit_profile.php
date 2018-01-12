<?php
include 'header.php';
include 'dbcon.php';
$valid = true;
$success = '';
$error =  '';

$fname = '';
$lname = '';
$city = '';

$language = '';
$phone_number = '';
$professional_skill = '';
$nick_name = '';
$working_as = '';


$sql = mysqli_query($conn, "SELECT * FROM user,info WHERE username='$uname' and user.ID=info.ID");
$row = mysqli_fetch_array($sql);
$fname = $row['fname'];
$lname = $row['lname'];
$city = $row['city'];

$language = $row['language'];
$phone_number = $row['phone_number'];
$professional_skill = $row['professional_skill'];
$nick_name = $row['nick_name'];
$working_as = $row['working_as'];

if (isset($_POST['submit'])) {
	$lname = test_input($_POST['lname']);
	$fname = test_input($_POST['fname']);
	$city = test_input($_POST['city']);

	$language = test_input($_POST['language']);
	$phone_number = test_input($_POST['phone_number']);
	$professional_skill = test_input($_POST['professional_skill']);
	$nick_name = test_input($_POST['nick_name']);
	$working_as = test_input($_POST['working_as']);

	$lname = mysqli_real_escape_string($conn, $lname);
	$fname = mysqli_real_escape_string($conn, $fname);
	$city = mysqli_real_escape_string($conn, $city);

	$language = mysqli_real_escape_string($conn, $language);
	$phone_number = mysqli_real_escape_string($conn, $phone_number);
	$professional_skill = mysqli_real_escape_string($conn, $professional_skill);
	$nick_name = mysqli_real_escape_string($conn, $nick_name);
	$working_as = mysqli_real_escape_string($conn, $working_as);

	if($lname=="" || $fname=="" || $city=="") {
		$error = "You must enter firstname, lastname and city";
		$valid = false;
	}
	if ($valid) {

		$sql = "UPDATE user SET fname='$fname', lname='$lname' WHERE username='$uname'";
		mysqli_query($conn, "update info set city='$city', language='$language', phone_number='$phone_number', professional_skill='$professional_skill', nick_name='$nick_name', working_as='$working_as' where ID='$id'");
		if ($conn->query($sql) === TRUE) {
			$success = "<p style='color:green;'>Updated successfully</p>";
				// header('Location: profile.php');
		} else {
			echo "Error: " . $sql . "<br>" . $conn->error;
		}
	}
}
if (isset($_POST['pass_reset'])) {
	$old_pass = test_input($_POST['password_old']);
	$new_pass = test_input($_POST['password_new']);
	$new2_pass = test_input($_POST['password_new2']);
	$old_pass = mysqli_real_escape_string($conn, $old_pass);
	$new_pass = mysqli_real_escape_string($conn, $new_pass);
	$new2_pass = mysqli_real_escape_string($conn, $new2_pass);

	if($new_pass!=$new2_pass) {
		$error = 'New password not mathced';
		$valid = false;
	}
	if(strlen($new_pass)<6) {
		$error = 'Password must be atleast 6 characters';
		$valid = false;
	}
	$check_pre_pass = mysqli_query($conn, "Select * from users where username='$uname' and password='$old_pass'");
	if (mysqli_num_rows($check_pre_pass)) {
		
	}else{
		$error = 'You entered wrong old password';
		$valid = false;
	}
	if($valid ) {
		$upadte_pass = mysqli_query($conn, "update users set password='$new_pass' where username='$uname'");
		$success = "<p style='color:green;'>Updated successfully</p>";
	}
}
?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>
	<div id="header" ><h4 style="height: 2px;">Edit profile</h4></div>
	<div id="container">
		<p id="alerts" style="color: red;"><?php echo $error; ?></p>
		<p id="alerts" style="color: green;"><?php echo $success; ?></p>
		<form id="edit_form" name="edit_form" method="post" action="edit_profile.php" onsubmit="return confirm('Do you want to save these?')">
			<h3><u>Basic Information</u></h3>
			
			<table>
				
				<tr>
					<td>First name</td>
					<td><input autocomplete="off" type="text" name="fname" value="<?php echo $fname; ?>" required></input></td>
				</tr>
				<tr>
					<td>Last name</td>
					<td><input autocomplete="off" type="text" name="lname" value="<?php echo $lname; ?>" required></input></td>
				</tr>
				<tr>
					<td>Language you know</td>
					<td><input autocomplete="off" type="text" name="language" value="<?php echo $language; ?>" ></input></td>
				</tr>
				<tr>
					<td>Professional skills</td>
					<td><input autocomplete="off" type="text" name="professional_skill" value="<?php echo $professional_skill; ?>" ></input></td>
				</tr>
				<tr>
					<td>Phone number</td>
					<td><input autocomplete="off" type="number" name="phone_number" value="<?php echo $phone_number; ?>" ></input></td>
				</tr>
				<tr>
					<td>Working as</td>
					<td><input autocomplete="off" type="text" name="working_as" value="<?php echo $working_as; ?>" ></input></td>
				</tr>
				<tr>
					<td>Nickname</td>
					<td><input autocomplete="off" type="text" name="nick_name" value="<?php echo $nick_name; ?>" ></input></td>
				</tr>
				<tr>
					<td>City</td>
					<td><select id="city" name="city" required>
						<option value="Dhaka">Dhaka</option>
						<option value="Chittagong">Chittagong</option>
						<option value="Khulna">Khulna</option>
						<option value="Rangpur">Rangpur</option>
						<option value="Mymensingh">Mymensingh</option>
						<option value="Sylhet">Sylhet</option>
						<option value="Rajshahi">Rajshahi</option>
						<option value="Barisal">Barisal</option>
					</select></td>
					<script type="text/javascript">
						document.getElementById('city').value = "<?php echo $city; ?>";
					</script>
				</tr>

			</table>
			<input  type="submit" name="submit" value="Save">
		</form>
		<br>
		<form id="edit_form" name="edit_pass" method="post" action="edit_profile.php" >
			<h3><u>Password change</u></h3>
			<table>
				
				
				<tr>
					<td>Old Password</td>
					<td><input onfocusout="checkpass()" type="password" name="password_old"></input> <span style="color: red;" id="passerror"></span></td>
				</tr>
				<tr>
					<td>New Password</td>
					<td><input onfocusout="checkpass()" type="password" name="password_new"></input> <span style="color: red;" id="passerror"></span></td>
				</tr>
				<tr>
					<td>New Password retype</td>
					<td><input onfocusout="checkpass()" type="password" name="password_new2"></input> <span style="color: red;" id="passerror"></span></td>
				</tr>
				

			</table>
			<input  type="submit" name="pass_reset" value="Save">
		</form>
		
	</div>
	
</body>
</html>