<?php
	session_start();
	if ((isset($_SESSION['username']))) {
		header("Location: index.php");
	}
	$error = "";
	function test_input($data) {
	   $data = trim($data);
	   $data = stripslashes($data);
	   $data = htmlspecialchars($data);
	   return $data;
	}

	if (isset($_POST['submit'])) {
	    $u = test_input($_POST['username']);
	    $p = test_input($_POST['password']);


	    include('dbcon.php');

	    $username = mysqli_real_escape_string($conn, $u);
	    $password = mysqli_real_escape_string($conn, $p);

	    $result = mysqli_query($conn, "SELECT * FROM user WHERE username='$username' AND password='$password'");
	    if (mysqli_num_rows($result)) {
	        $res = mysqli_fetch_array($result);
	        if($res['verify']==0) {
	        	$error = 'Your email is not verified. PLease check your inbox.';
	        }else{
	        	$id = $res['ID'];
	        	$active_sql = mysqli_query($conn, "UPDATE info SET last_login=NOW() WHERE ID='$id'");
		        $_SESSION ['username'] = $res['username'];
		        $_SESSION ['id'] = $res['ID'];
		        header("Location: index.php");
	        }
	        
	    } else {
	        $error = "No user found!!";
	    }
	}
?>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<meta name="description" content="Torko is a social networking website for people of Bangladesh. Register and enjoy" />
	<meta name="robots" content="index,follow,noarchive">
	<meta name="keywords" content="Torko, social network, Bangladesh" />

	<title>Log into Torko</title>
	<link rel="shortcut icon" type="image/x-icon" href="images/icon.ico" />
	
	<style type="text/css">
		body {
			margin-top: 40px;
			font-family: Trebuchet MS,Lucida Grande,Lucida Sans Unicode,Lucida Sans,Tahoma,sans-serif;

		}
		table {
			margin: auto;

		}
		input {
			width: 100%;
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
</head>
<body>
<div id="the_form">
	<h3>Login</h3>
	
	<form name="login_form" method="post" action="login.php">
		<table>
			<tr>
				<td>Username</td>
				<td><input type="text" name="username"></input></td>
			</tr>
			<tr>
				<td>Password</td>
				<td><input type="password" name="password"></input></td>
			</tr>
			
		</table>
		<br>
		<div style="text-align: center;">
			<input type="submit" name="submit" value="login">
			<p>Need an account? <a href="register.php">Register here</a> </p>
			<p>Forget password? <a href="forget_pass.php">Click here</a> </p>
			<p style="color: red;"><?php echo $error ?></p>
		</div>
	</form>
	
	</div>
</body>
</html>
