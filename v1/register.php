<?php
session_start();
if ((isset($_SESSION['username']))) {
	header("Location: index.php");
}
function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}
include 'dbcon.php';
$error=""; 
$success = "";

$username=""; 
$lname=""; 
$fname=""; 
$email="";
$yearOfBirth="";
$monthOfBirth="";
$dateOfBirth="";
$bday="";

if (isset($_POST['namecheck'])) {
	$name = $_POST['name'];
	$sql = mysqli_query($conn, "SELECT username FROM user WHERE username='$name'");
	if (mysqli_num_rows($sql)) {
		echo "Username exists choose another one";
	}
	die();
}
if (isset($_POST['emailcheck'])) {
	$name = $_POST['email'];
	$sql = mysqli_query($conn, "SELECT email FROM user WHERE email='$email'");
	if (mysqli_num_rows($sql)) {
		echo "Email already exists";
	}
	die();
}


if (isset($_POST['submit'])) {


	$username = test_input($_POST['username']);
	$lname = test_input($_POST['lname']);
	$fname = test_input($_POST['fname']);
	$password = test_input($_POST['password']);
	$gender = test_input($_POST['gender']);
	$city = test_input($_POST['city']);
	$email = test_input($_POST['email']);

	$valid = true;

	$yearOfBirth = $_POST['yearOfBirth'];
	$monthOfBirth = $_POST['monthOfBirth'];
	$dateOfBirth = $_POST['dateOfBirth'];

	$hash = md5( rand(0,1000) );

	if ($yearOfBirth != '' && $monthOfBirth != '' && $dateOfBirth != '') {
		$bday = $yearOfBirth.'-'.$monthOfBirth.'-'.$dateOfBirth;
	}else{
		$valid = false;
		$error = "Enter Birthday";
	}

	$username = mysqli_real_escape_string($conn, $username);
	$lname = mysqli_real_escape_string($conn, $lname);
	$fname = mysqli_real_escape_string($conn, $fname);
	$password = mysqli_real_escape_string($conn, $password);
	$gender = mysqli_real_escape_string($conn, $gender);
	$city = mysqli_real_escape_string($conn, $city);
	$email = mysqli_real_escape_string($conn, $email);


	if($username=="" || $lname=="" || $fname=="" || $password=="" || $gender=="" || $city=="" || $email=="" || $bday=="") {
		$error = "Please enter all the fields";
		$valid = false;
	}
	if ($gender!='male' && $gender!='female') {
		$error = "Transgender not allowed";
		$valid = false;
	}
	if (strlen($username)<6) {
		$error = "username should have atleast 6 characters";
		$valid = false;
	}
	if (strlen($password)<6) {
		$error = "Password should have atleast 6 characters";
		$valid = false;
	}
	if (preg_match('/\s/',$username)) {
		$error = "Username must not contain any space!";
		$valid = false;
	}
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$error = "Invalid email format";
		$valid = false; 
	}
	$checkNameExist = mysqli_query ($conn, "SELECT username FROM user WHERE username='$username'");
	if (mysqli_num_rows($checkNameExist)) {
		$error = "Username already exists. Please select different username";
		$valid = false;
	}
	$checkEmailExist = mysqli_query ($conn, "SELECT email FROM user WHERE email='$email'");
	if (mysqli_num_rows($checkEmailExist)) {
		$error = "Email already exixst!";
		$valid = false;
	}
	if ($valid) {

		$sql = "INSERT INTO user (username, password, fname, lname, email, hash, verify)
		VALUES ('$username', '$password', '$fname', '$lname', '$email', '$hash', 0)";
		
		
		if ($conn->query($sql) === TRUE) {
			$id = mysqli_query($conn, "select ID from user where username='$username'");
			$row = mysqli_fetch_array($id);
			$id2 = $row['ID'];
			$sql_info = "insert into info (ID, city, gender, reg_date, birth) values ('$id2', '$city', '$gender', NOW(), '$bday')";
			if($conn->query($sql_info)) {
			}else {
				echo "Error: " . $sql_info . "<br>" . $conn->error;
			}
				


				$to      = $email; // Send email to our user
				$subject = 'Signup | Verification for Torko'; // Give the email a subject 
				$message = '

Thanks for signing up!
Your account has been created, you can login with the following credentials after you have activated your account by pressing the url below.

------------------------
Username: '.$username.'
------------------------

Please click this link to activate your account:
http://torko.ml/verify.php?email='.$email.'&hash='.$hash.'

'; // Our message above including the link

				$headers = 'From:noreply@torko.ml' . "\r\n"; // Set from headers
				mail($to, $subject, $message, $headers); // Send our email
				

				$success = "You registered successfully. Please verify your email to login";
			} else {
				echo "Error: " . $sql . "<br>" . $conn->error;
			}
		}	


	}
	?>
	<!DOCTYPE html>
	<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Register</title>
		<link rel="shortcut icon" type="image/x-icon" href="images/icon.ico" />

		<style type="text/css">
			body {
				margin-top: 40px;
				font-family: Trebuchet MS,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Tahoma,sans-serif;

			}
			table {
				margin: auto;
			}
			
			#the_form {
				box-shadow:0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
				background-color: #f1f1f1;
				margin: auto;
				width: 60%;
				padding: 20px;
			}
			h3 {
				text-align: center;
				background-color: #4CAF50;
				padding: 20px;
				border-radius: 3px;
			}
			a {
				text-decoration: none;
				color: #0066ff;	
			}
			a:hover {
				text-decoration: underline;
			}
		</style>


		<script type="text/javascript" src="main.js"></script>
		<script type="text/javascript">
			function checkname() {
				var n = _("reg_form").username.value;
				_("nameerror").innerHTML = "";
				if (n.length < 6) {
					_("nameerror").innerHTML = "Enter atleast 6 characters";
				}

				if (n.indexOf(' ') >= 0) {
					_("nameerror").innerHTML = "Name should not have any space";
				}
				var ajax = ajaxObj("POST", "register.php");
				ajax.onreadystatechange = function() {
					if(ajaxReturn(ajax) == true) {
						if (ajax.responseText!="") {
							_("nameerror").innerHTML = ajax.responseText;
						}					
					}
				}
				ajax.send("namecheck=1&name="+n);
			}

			function validateEmail(email) {
				var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
				return re.test(email);
			}

			function checkpass() {
				var n = _("reg_form").password.value;
				_("passerror").innerHTML = "";
				if (n.length < 6) {
					_("passerror").innerHTML = "Enter atleast 6 characters";
				}
			}

			function checkemail() {
				var n = _("reg_form").email.value;
				_("emailerror").innerHTML = "";
				if (!validateEmail(n)) {
					_("emailerror").innerHTML = "Invalid email format";
				}

				var ajax = ajaxObj("POST", "register.php");
				ajax.onreadystatechange = function() {
					if(ajaxReturn(ajax) == true) {
						if (ajax.responseText!="") {
							_("emailerror").innerHTML = ajax.responseText;
						}					
					}
				}
				ajax.send("emailcheck=1&email="+n);
			}


		</script>
	</head>
	<body>

		<div id="the_form">
			<h3>Register</h3>
			<form id="reg_form" name="registration_form" method="post" action="register.php">
				<table>
					<tr>
						<td>Enter username</td>
						<td><input autocomplete="off" onfocusout="checkname()" type="text" name="username" value="<?php echo isset($_POST['username']) ? $_POST['username'] : '' ; ?>"></input> <span style="color: red;" id="nameerror"></span>   </td>
					</tr>
					<tr>
						<td>Enter first name</td>
						<td><input autocomplete="off" type="text" name="fname" value="<?php echo isset($_POST['fname']) ? $_POST['fname'] : '' ; ?>"></input></td>
					</tr>
					<tr>
						<td>Enter last name</td>
						<td><input autocomplete="off" type="text" name="lname" value="<?php echo isset($_POST['lname']) ? $_POST['lname'] : '' ; ?>"></input></td>
					</tr>
					<tr>
						<td>Enter password</td>
						<td><input onfocusout="checkpass()" type="password" name="password"></input> <span style="color: red;" id="passerror"></span></td>
					</tr>
					<tr >
						<td >Enter email  </td>
						<td><input onfocusout="checkemail()" autocomplete="off" type="text" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : '' ; ?>"></input><span style="color: red;" id="emailerror"></span> </td>
					</tr>

					<tr>
						<td colspan="2">
							(please use valid email as you have to verify email to login) 
						</td>
					</tr>
					<tr>
						<td>Birthday</td>
						<td>
							<select id="yearOfBirth" name="yearOfBirth">
								<option value="">---Select year---</option>
								<?php for ($i = 1980; $i < (date('Y')-10); $i++) : ?>
									<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
								<?php endfor; ?>
							</select>
							<select id="monthOfBirth" name="monthOfBirth">
								<option value="">---Select month---</option>
								<?php for ($i = 1; $i <= 12; $i++) : ?>
									<option value="<?php echo ($i < 10) ? '0'.$i : $i; ?>"><?php echo $i; ?></option>
								<?php endfor; ?>
							</select>
							<select id="dateOfBirth" name="dateOfBirth">
								<option value="">---Select date---</option>
								<?php for ($i = 1; $i <= 31; $i++) : ?>
									<option value="<?php echo ($i < 10) ? '0'.$i : $i; ?>"><?php echo $i; ?></option>
								<?php endfor; ?>
							</select>
						</td>
					</tr>
					<tr>
						<td>Choose your city</td>
						<td><select id="city" name="city">
							<option value="Dhaka" selected="">Dhaka</option>
							<option value="Chittagong">Chittagong</option>
							<option value="Khulna">Khulna</option>
							<option value="Rangpur">Rangpur</option>
							<option value="Mymensingh">Mymensingh</option>
							<option value="Sylhet">Sylhet</option>
							<option value="Rajshahi">Rajshahi</option>
							<option value="Barisal">Barisal</option>
						</select></td>
						<script type="text/javascript">
							document.getElementById('city').value = "<?php echo isset($_POST['city']) ? $_POST['city'] : '' ; ?>";
							document.getElementById('yearOfBirth').value = "<?php echo isset($_POST['yearOfBirth']) ? $_POST['yearOfBirth'] : '' ; ?>";
							document.getElementById('monthOfBirth').value = "<?php echo isset($_POST['monthOfBirth']) ? $_POST['monthOfBirth'] : '' ; ?>";
							document.getElementById('dateOfBirth').value = "<?php echo isset($_POST['dateOfBirth']) ? $_POST['dateOfBirth'] : '' ; ?>";
						</script>
					</tr>
					<tr>
						<td>Gender </td>
						<td><input type="radio" name="gender" value="male" checked> Male<br>
							<input type="radio" name="gender" value="female"> Female</td>
					</tr>	
					</table>
					<div style="text-align: center;">
						<input  type="submit" name="submit" value="register">
						<p>Already have an account? <a href="login.php">Login here</a></p>
						<p style="color:red;"><?php echo $error?></p>
					</div>
				</form>
				<p style='color:green;'><?php echo $success ?></p>
				
				
			</div>
		</body>
		</html>