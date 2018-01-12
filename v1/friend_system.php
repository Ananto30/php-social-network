<?php
	session_start();
	include 'dbcon.php';
	$uname = $_SESSION["username"];


	if (isset($_POST['frnd_req'])) {
			
		$profile_name = $_POST['profile_name'];
			$frnd_req = "INSERT INTO frndreq (sender, receiver, accept) VALUES ('$uname', '$profile_name', FALSE)";
			if ($conn->query($frnd_req) === TRUE) {
				   
				    echo "<p style='color:green;'>Friend request sent</p>";
				} else {
				    echo "Error: " . $frnd_req . "<br>" . $conn->error;
				}
		
	}

	if(isset($_POST['rmv'])) {
		$profile_name = $_POST['profile_name'];
		$frnd_sql = "DELETE FROM `frndreq` WHERE (sender='$profile_name' AND receiver='$uname') OR (sender='$uname' AND receiver='$profile_name') ";
		if($conn->query($frnd_sql) == true) {
			echo "<p style='color:red;'>Friend removed</p>";
		}
	}

	if (isset($_POST['accept'])) {
		$profile_name = $_POST['profile_name'];
		$frnd_sql = mysqli_query($conn, "UPDATE `frndreq` SET `accept`= 1 WHERE `sender`='$profile_name' AND `receiver`='$uname'");
		if($frnd_sql) {
			echo "<p style='color:green;'>Accepted!</p>";
		}

	}
	
	if (isset($_POST['reject'])) {
		$profile_name = $_POST['profile_name'];
		$frnd_sql = "DELETE FROM `frndreq` WHERE sender='$profile_name' AND receiver='$uname'";
		if($conn->query($frnd_sql) == true) {
			echo "<p style='color:red;'>Rejected!</p>";
		}
	}
?>