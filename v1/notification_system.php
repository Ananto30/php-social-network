<?php
session_start();
include 'dbcon.php';
include 'php_functions.php';

$uname = $_SESSION["username"];

$notification_count = 0;

$frndreqrcv = "";
$frndreqcheck = "SELECT * FROM frndreq WHERE receiver='$uname' AND accept=0";
$rslt = $conn->query($frndreqcheck);
if ($rslt->num_rows > 0) {
	while($row = $rslt->fetch_assoc()) {
		$frndreqrcv .= "<p><a href='profile.php?profile_name=".$row['sender']."'>".show_name($conn,$row['sender'])."</a> invited you to be friend";
		$notification_count++;
	}
}

$comments = "";
$commentscheck = "SELECT * FROM status_comments WHERE comment_in='$uname' AND seen_by_owner=0 ORDER BY comment_time DESC";
$rslt = $conn->query($commentscheck);
if ($rslt->num_rows > 0) {
	while($row = $rslt->fetch_assoc()) {
		$comments .= "<p>".show_name($conn,$row['comment_by'])." posted comment in your status in ".my_time($row['comment_time'])." <button onclick='seen_comment(\"".$row['comment_id']."\");'> see</button>";
		$notification_count++;
	}
}

$comments_others = "";
$commentscheck_others = "select * from status_comments where comment_by!='$uname' and comment_in!='$uname' and comment_time>any(SELECT max(comment_time) FROM status_comments WHERE comment_by='$uname' AND seen_by_commenter=0) and status_id in (SELECT status_id FROM status_comments WHERE comment_by='$uname' AND seen_by_commenter=0) ORDER BY comment_time DESC";
$rslt = $conn->query($commentscheck_others);
if ($rslt->num_rows > 0) {
	while($row = $rslt->fetch_assoc()) {
		$comments .= "<p>".show_name($conn,$row['comment_by'])." posted comment on a status you commented in ".my_time($row['comment_time'])." <button onclick='seen_comment(\"".$row['comment_id']."\",\"".$row['status_id']."\");'> see</button>";
		$notification_count++;
	}
}

$wallpost = "";
$walpostcheck = "SELECT * FROM post WHERE posted_in='$uname' and posted_by!='$uname' AND seen=0 ORDER BY post_time DESC";
$rslt = $conn->query($walpostcheck);
if ($rslt->num_rows > 0) {
	while($row = $rslt->fetch_assoc()) {
		$wallpost .= "<p>".show_name($conn,$row['posted_by'])." posted something new on your profile in ".my_time($row['post_time'])." <button onclick='seen_post(\"".$row['post_id']."\");'> see</button>";
		$notification_count++;
	}
}


if (isset($_POST['notification_count'])) {
	if($notification_count==0) {

	}else {
		echo '<span style="float: right; background-color:gray; border-radius: 50%; margin-right: 5%;color: white; height: 20px; width: 20px;text-align: center;">'.$notification_count.'</span>';	
	}	
}

if (isset($_POST['frnd_req'])) {
	echo $frndreqrcv;
}
if (isset($_POST['comments'])) {
	echo $comments;
}
if (isset($_POST['wallpost'])) {
	echo $wallpost;
}
if (isset($_POST['seen_comment'])) {
	$comment_id = $_POST['comment_id'];
	$status_id = $_POST['status_id'];
	mysqli_query($conn, "UPDATE status_comments SET seen_by_owner=1 WHERE comment_id='$comment_id' and comment_in='$uname'");
	mysqli_query($conn, "UPDATE status_comments SET seen_by_commenter=1 WHERE status_id='$status_id' and comment_by='$uname'");
}
if (isset($_POST['seen_post'])) {
	$post_id = $_POST['post_id'];
	mysqli_query($conn, "UPDATE post SET seen=1 WHERE post_id='$post_id'");
}
?>