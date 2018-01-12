<?php
include 'dbcon.php';
include 'php_functions.php';

$error = '';
$success = '';
$valid = true;

if (isset($_POST['submit'])) {
	$email = test_input($_POST['email']);
	$email = mysqli_real_escape_string($conn, $email);

	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$error = "Invalid email format";
		$valid = false; 
	}else
	if ($email=='') {
		$error = "Enter you email noob! Where will i send this shit";
		$valid = false; 
	}else {
		$check_email_exist = mysqli_query($conn, "select * from user where email='$email'");
		if (mysqli_num_rows($check_email_exist)) {
			$row = mysqli_fetch_array($check_email_exist);
			if($row['verify']==0) {
				$error = "You haven't verified your email. Please check your inbox";
				$valid = false; 
			}

		}else{
			$error = "You haven't registered yet. <a href='register.php'>click here</a> to register";
			$valid = false; 
		}
	}
	
	
	if ($valid) {
		$sql = mysqli_query($conn, "select * from user where email='$email'");
		$row = mysqli_fetch_array($sql);

		$to      = $email; // Send email to our user
				$subject = 'Password reset'; // Give the email a subject 
				$message = '

Your credentials below.

------------------------
Username: '.$row['username'].'
Password: '.$row['password'].'
------------------------

Change the password for your safety.

'; // Our message above including the link

				$headers = 'From:noreply@torko.ml' . "\r\n"; // Set from headers
				mail($to, $subject, $message, $headers); // Send our email
				$success = "Email sent to your mail account. Please check your inbox.";
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link rel="stylesheet" href="main.css">
</head>
<body>
	<form id="edit_form" name="forget_form" method="post" action="forget_pass.php">
		<h3><u>Password reset</u></h3>

		<table>

			<tr >
				<td >Enter email  </td>
				<td><input autocomplete="off" type="text" name="email" required></input></td>
			</tr>

		</table>
		<input  type="submit" name="submit" value="Send password to email">
		<p style="color:red;"><?php echo $error?></p>
		<p style='color:green;'><?php echo $success ?></p>
	</form>
	
</body>
</html>