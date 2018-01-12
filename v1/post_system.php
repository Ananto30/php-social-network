<?php 

session_start();
include 'dbcon.php';
include 'php_functions.php';

$uname = $_SESSION["username"];



if (isset($_POST['post_area'])) {
	$post = $_POST['post_area'];
	$profile_name = $_POST['profile_name'];
	$frnd = false;
	$owner = false;

	$check = $conn->query("SELECT accept FROM frndreq WHERE (sender='$uname' AND receiver='$profile_name') OR (sender='$profile_name' AND receiver='$uname')");
	if ($check) {
		$row = $check->fetch_assoc();
		if ($row['accept']==0) {
		
		}else{
			$frnd = true;
		}
	}
	if ($uname == $profile_name) {
		$owner = true;
	}
	if ($frnd || $owner) {
		$post = test_input($post);
		$post = mysqli_real_escape_string($conn, $post);
		$post_sql = "INSERT INTO post (posted_by, posted_in, post_time, post, seen) VALUES ('$uname', '$profile_name', NOW(), '$post', 0)";
		if ($conn->query($post_sql) === TRUE) {
			echo "<p id='alerts' style='color:green;'>Posted!</p>";
			//reload the posts from server
			wall_post($profile_name,$conn,$uname);

		}
	}

		
	
	
}

if(isset($_POST['dlt_post'])) {
	$profile_name = $_POST['profile_name'];
	$post_id = $_POST['post_id'];
	$check = $conn->query("SELECT posted_by FROM post WHERE post_id='$post_id'");
	$row = $check->fetch_assoc();
	if ($uname != $row['posted_by'] && $uname!= $profile_name) {
		wall_post($profile_name,$conn,$uname);
		echo "<p id='alerts' style='color:red;'>you are not allowed</p>";
	} else {
		$postdlt_sql = "DELETE FROM `post` WHERE post_id='$post_id'";
		if($conn->query($postdlt_sql) == true) {
			echo "<p id='alerts' style='color:red;'>Deleted!</p>";
			wall_post($profile_name,$conn,$uname);
		}
	}
	
}
?>
