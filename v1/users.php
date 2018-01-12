<?php
include 'header.php';
include 'dbcon.php';

$frnd = "";
$active = "";
$pic = "";
$show_users = "";
$user_info = '';

$sql = "SELECT * FROM user,info WHERE username!='$uname' and user.ID=info.ID order by last_login DESC";
$result = $conn->query($sql);
if($result->num_rows > 0) {

	while ($row =$result->fetch_assoc()) {

		$name = $row['username'];
		if ($row['pro_pic_path']) {
			$pro_pic_path = $row['pro_pic_path'];
			$pic = '<img id="user_pic" style="float:left;" src="'.$pro_pic_path.'" height="100px" width="100px">';
		}else{
			if ($row['gender'] == "male") {
				$pic =  '<img id="user_pic" style="float:left;" src="uploads/default_male.jpg" height="100px" width="100px">';
			}else{
				$pic =  '<img id="user_pic" style="float:left;" src="uploads/default_female.jpg" height="100px" width="100px">'; 
			}
			
		}
		if($conn->query("SELECT * FROM frndreq WHERE receiver='$name' AND sender='$uname' AND accept='0'")->num_rows >0) {
			
			$frnd = 'Friend invitation sent';
		}else if($conn->query("SELECT * FROM frndreq WHERE sender='$name' AND receiver='$uname' AND accept='0'")->num_rows >0) {
			
			$frnd = 'Invited you to be friend';
		}else if($conn->query("SELECT * FROM frndreq WHERE (sender='$name' AND receiver='$uname') OR (receiver='$name' AND sender='$uname') AND accept='1'")->num_rows >0){
			$frnd = 'You are Friend!';
		}else {
			$frnd = '';
		}

		$user_info .= "<span id='user_info'><br>".$frnd."<br>".$row['city']."<br>".$row['working_as'];

		$show_users .= "<div id='show_users'>".$pic."<span style='display:inline-block;padding-left:10px;width:180px;'><a href='profile.php?profile_name=".$name."'>".show_name($conn, $name)."</a>".$user_info."</span></div>";
		$frnd = '';
		$active = '';
		$pic = '';
		$user_info = '';
	}
}
?>

<html>
<head>
	<title>Users</title>
</head>
<body class="users">
	<div id="header" ><h4 style="height: 2px;">All users</h4></div>
	<div id="container" >
		<?php echo $show_users ?>
	</div>
</body>
</html>