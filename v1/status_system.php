<?php

session_start();
include 'dbcon.php';
include 'php_functions.php';
$uname = $_SESSION["username"];







if (isset($_POST['status_area'])) {
	$post = $_POST['status_area'];
	$post = test_input($post);
	$post = mysqli_real_escape_string($conn, $post);
	$post_sql = "INSERT INTO status (status_by, status_time, status) VALUES ('$uname', NOW(), '$post')";
	if ($conn->query($post_sql) === TRUE) {
		echo "<p id='alerts' style='color:green;'>New status!</p>";
		show_status_with_comments($conn, $uname);
		
	}
}

if(isset($_POST['dlt_post'])) {
	$id = $_POST['id'];
	$check = $conn->query("SELECT status_by FROM status WHERE status_id='$id'");
	$row = $check->fetch_assoc();
	if ($uname != $row['status_by']) {
		echo "<p id='alerts' style='color:red;'>you are not allowed</p>";
	}else {
		$postdlt_sql = "DELETE FROM status WHERE status_id='$id'";
		if($conn->query($postdlt_sql) == true) {
			echo "<p id='alerts' style='color:red;'>Deleted!!</p>";
			show_status_with_comments($conn, $uname);
			
			
			
		}
	}
}


if (isset($_POST['comment_area'])) {
	$status_id = $_POST['status_id'];
	$comment_in1 = $conn->query("SELECT * FROM status WHERE status_id='$status_id'");
	$comment_in = $comment_in1->fetch_assoc();
	$comment_in = $comment_in['status_by'];
	$seen = 0;
	if ($uname == $comment_in) {
		$seen = 1;
	}
	$post = $_POST['comment_area'];
	$post = test_input($post);
	$post = mysqli_real_escape_string($conn, $post);
	$post_sql = "INSERT INTO status_comments (comment_by, comment_time, comment, status_id, seen_by_owner, comment_in, seen_by_commenter) VALUES ('$uname', NOW(), '$post', '$status_id', '$seen', '$comment_in', 1)";
	
	if ($conn->query($post_sql) === TRUE) {
		$other_notification = mysqli_query($conn, "update status_comments set seen_by_commenter=0 where status_id='$status_id' and comment_by !='$uname'");
		show_comments_of_status($status_id, $conn, $uname);
		echo "<p id='alerts' style='color:green;'>Commented!!</p>";

	}
}
if(isset($_POST['dlt_comment'])) {
	$comment_id = $_POST['comment_id'];
	$status_id = $_POST['status_id'];
	$check = $conn->query("SELECT comment_by,comment_in FROM status_comments WHERE comment_id='$comment_id'");
	$row = $check->fetch_assoc();
	if ($uname != $row['comment_by'] && $uname != $row['comment_in']) {
		show_comments_of_status($status_id, $conn, $uname);
		echo "<p id='alerts' style='color:red;'>you are not allowed</p>";
	} else {
		$postdlt_sql = "DELETE FROM `status_comments` WHERE comment_id='$comment_id'";
		if($conn->query($postdlt_sql) == true) {

			show_comments_of_status($status_id, $conn, $uname);
			echo "<p id='alerts' style='color:red;'>Deleted!</p>";

		}
	}
	
}

?>
