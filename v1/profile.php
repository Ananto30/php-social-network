<?php
include 'header.php';
$owner = false;
$name = "";
$city = "";
$pic = "";
$bday = "";
$gender = "";
$email = "";
$reg_date = "";
$profile_name = "";
$last_login = "";

$uname = $_SESSION["username"];


if (isset($_GET['profile_name'])) {
	$profile_name = $_GET['profile_name'];
}else{
	$profile_name = $uname;
}

if($profile_name == $uname) {
	$owner = true;
}

include 'dbcon.php';

$info_sql = "SELECT * FROM user,info WHERE user.username='$profile_name' and user.ID=info.ID";

if ($conn->query($info_sql)->num_rows > 0) {
	$row = $conn->query($info_sql)->fetch_assoc();
	$name = $row['fname']." ".$row['lname'];
	$city = $row['city'];
	$bday = $row['birth'];
	$gender = $row['gender'];
	$email = $row['email'];
	$reg_date = $row['reg_date'];
	$last_login = $row['last_login'];
	$language = $row['language'];
	$phone_number = $row['phone_number'];
	$professional_skill = $row['professional_skill'];
	$nick_name = $row['nick_name'];
	$working_as = $row['working_as'];
}


$frndreqsent = false;
$frnd = false;
$frndreqcheck = "SELECT * FROM frndreq WHERE sender='$uname' AND receiver='$profile_name'";
if ($conn->query($frndreqcheck)->num_rows > 0) {
	$row = $conn->query($frndreqcheck)->fetch_assoc();
	if($row['accept'] == 0) {
		$frndreqsent = true;
	}else{
		$frnd = true;
	}
}
$frndreqrcv = false;
$frndreqcheck = "SELECT * FROM frndreq WHERE sender='$profile_name' AND receiver='$uname'";
if ($conn->query($frndreqcheck)->num_rows > 0) {
	$row = $conn->query($frndreqcheck)->fetch_assoc();
	if($row['accept'] == 0) {
		$frndreqrcv = true;
	}else{
		$frnd = true;
	}
}




$friends = "";
$friends_sql = "SELECT * FROM frndreq WHERE (sender='$profile_name' OR receiver='$profile_name') AND accept='1'";
$reslt = $conn->query($friends_sql);
if ($reslt->num_rows > 0) {
	while($row = $reslt->fetch_assoc()) {
		if($row['sender']!=$profile_name) {
			$pr = $row['sender'];
			$friends .= "<a href='profile.php?profile_name=".$pr."'>$pr</a>, " ;
		}
		if( $row['receiver']!=$profile_name) {
			$pr = $row['receiver'];
			$friends .= "<a href='profile.php?profile_name=".$pr."'>$pr</a>, " ;
		}
	}
}else{
	if($owner) {
		$friends = "You have no friends. Go to users and invite friends :) ";
	}
	
}



?>

<html>
<head>
	<title>Profile</title>
	<link rel="shortcut icon" type="image/x-icon" href="images/icon.ico" />
	<script type="text/javascript">

		

		function post_status(profile_name) {
			var ajax = ajaxObj("POST", "post_system.php");
			var post_area = document.getElementById("post_area").value;
			if (post_area=="") {
				alert("Enter something noob");
				return false;
			}
			ajax.onreadystatechange = function() {
				if(ajaxReturn(ajax) == true) {
					_("post_state").innerHTML = ajax.responseText;
					$jq_alert();
				}
			}
			document.getElementById("post_area").value ="";
			ajax.send("profile_name="+profile_name+"&post_area="+post_area);
		}

		function dlt_status(profile_name, post_id) {
			var ajax = ajaxObj("POST", "post_system.php");
			ajax.onreadystatechange = function() {
				if(ajaxReturn(ajax) == true) {
					_("post_state").innerHTML = ajax.responseText;
					$jq_alert();
				}
			}
			ajax.send("post_id="+post_id+"&dlt_post=1&profile_name="+profile_name);
		}

		function send_frndreq(profile_name) {
			var ajax = ajaxObj("POST", "friend_system.php");
			ajax.onreadystatechange = function() {
				if(ajaxReturn(ajax) == true) {
					_("frnd_state").innerHTML = ajax.responseText;
				}
			}
			ajax.send("profile_name="+profile_name+"&frnd_req=1");
		}
		function accept_frndreq(profile_name) {
			var ajax = ajaxObj("POST", "friend_system.php");
			ajax.onreadystatechange = function() {
				if(ajaxReturn(ajax) == true) {
					_("frnd_state").innerHTML = ajax.responseText;
					_("post_something").innerHTML = "<textarea id='post_area' rows='5' cols='30'></textarea><button type='button' onclick='post_status(\""+profile_name+"\")'>Acquaint!</button> ";
				}
			}
			ajax.send("profile_name="+profile_name+"&accept=1");
		}
		function reject_frndreq(profile_name) {
			var ajax = ajaxObj("POST", "friend_system.php");
			ajax.onreadystatechange = function() {
				if(ajaxReturn(ajax) == true) {
					_("frnd_state").innerHTML = ajax.responseText;
				}
			}
			ajax.send("profile_name="+profile_name+"&reject=1");
		}
		function rmv_frndreq(profile_name) {
			var ajax = ajaxObj("POST", "friend_system.php");
			ajax.onreadystatechange = function() {
				if(ajaxReturn(ajax) == true) {
					_("frnd_state").innerHTML = ajax.responseText;
					_("post_something").innerHTML = "";
				}
			}
			ajax.send("profile_name="+profile_name+"&rmv=1");
		}
		$("#pro_pic").click (function() {
			$("#pro_pic").toggle( function() {
				$("#pro_pic").css({ 'width' : '100%'});
			}, function () {
				$("#pro_pic").css({ 'width' : '100px'});
			});
		});

	</script>
</head>
<body class="profile">
	<div id="header" ><h4><?php echo $profile_name ?></h4></div>
	<div id="container" >
		<div id="info">
			<div id="little_profile" >
				<span style="float: left;display: inline-block;padding: 10px;"><?php 
				$pic_sql = mysqli_query($conn, "SELECT pro_pic_path FROM info,user WHERE username='$profile_name' AND pro_pic_path!='' and user.ID=info.ID");
				if (mysqli_num_rows($pic_sql)) {
					$row = mysqli_fetch_array($pic_sql);
					$pro_pic_path = $row['pro_pic_path'];
					echo '<a class="pic_click" target="_blank" href="'.$pro_pic_path.'" ><img id="pro_pic" src="'.$pro_pic_path.'" ></a>';
				}else{
					if ($gender == "male") {
						echo '<img id="pro_pic" src="uploads/default_male.jpg" height="100px" width="100px">';
					}else{
						echo '<img id="pro_pic" src="uploads/default_female.jpg" height="100px" width="100px">'; 
					}
					
				}
				?>
			</span>
			<span style="display: inline-block;color: gray;" >
				<h2><?php echo show_name($conn, $profile_name); ?></h2>
				<p> <?php echo $city ?></p>
				<p><?php echo $email ?></p>

			</span>
			</div>
			
			<div id="profile_info">
			<a style="font-size: 18px;" href="gallery.php?profile_name=<?php echo $profile_name; ?>">See Gallery</a>
			
			
			
			
			<br>
			<?php 
			if ($owner) {
				echo '<form id="upload_file" method="post" action="file_upload.php" enctype="multipart/form-data">
				Image upload: <br>
				<input type="file" id="fileToUpload" name="fileToUpload" ><br>
				<input type="submit" value="Upload" name="upload_pic">
			</form>
			<a href="edit_profile.php">Edit profile</a><br>';
				// echo '<input id="fileToUpload" type="file" name="fileToUpload" ><button type="button" onclick="upload_pic()">upload</button>';
			include 'file_system.php';
			echo "Gallery limit ".$foldersize."/5 MB<br><progress value=\"".($foldersizeinbytes/1048576)."\" max='5'></progress>";
		}
		
		?>
		<br>
		<button id="toggle_button">See Detail Info</button>
		<script>
		$( "#toggle_button" ).click(function() {
		  $( "#toggle_info" ).slideToggle( "slow" );
		});
		</script>
		<div id="toggle_info" style="display: none;">
			<table style="width: 70%;">
				
				<tr>
					<td>City </td>
					<td>: <?php echo $city ?></td>
				</tr>
				<tr>
					<td>Date of birth </td>
					<td>: <?php echo date("d M Y", strtotime($bday));?></td>
				</tr>
				<tr>
					<td>Gender </td>
					<td>: <?php echo $gender ?></td>
				</tr>
				<tr>
					<td>Email </td>
					<td>: <?php echo $email ?></td>
				</tr>
				<tr>
					<td>Registered in </td>
					<td>: <?php echo my_time($reg_date); ?></td>
				</tr>
				<tr>
					<td>Last login </td>
					<td>: <?php echo my_time($last_login); ?></td>
				</tr>
				<tr>
					<td>Languages </td>
					<td>: <?php echo $language; ?></td>
				</tr>
				<tr>
					<td>Proffesional skills </td>
					<td>: <?php echo $professional_skill; ?></td>
				</tr>
				<tr>
					<td>Working as </td>
					<td>: <?php echo $working_as; ?></td>
				</tr>
				<tr>
					<td>Nickname </td>
					<td>: <?php echo $nick_name; ?></td>
				</tr>
				<tr>
					<td>Phone Number </td>
					<td>: <?php echo $phone_number; ?></td>
				</tr>

			</table>
		</div>
		<br>
		<span id="frnd_state">
			<?php
			if (!$owner) {
				if ($frndreqsent) {
					echo "<p style='color:grey;'>Waiting to be friend</p>";
				}else if($frnd) {
					echo "<p style='color:green;'>Friend</p><button type='button' onclick='rmv_frndreq(\"".$profile_name."\")'>Remove friend</button>";
				}else if($frndreqrcv) {
					echo "<button type='button' onclick='accept_frndreq(\"".$profile_name."\")'>Accept</button>
					<button type='button' onclick='reject_frndreq(\"".$profile_name."\")'>Reject</button>";

				}
				else{
					echo "<button type='button' onclick='send_frndreq(\"".$profile_name."\")'>Send Friend Request</button>";
				}

			}
			?>
		</span>
		</div>
	</div>
	<br>
	

		<?php

		if ($owner || $frnd) {
			echo "<div id='post_something'><textarea id='post_area'></textarea>
			<button type='button' onclick='post_status(\"".$profile_name."\")'>Post</button></div> ";
		}
		?>

		
	
	<p id="latest"></p>
	
	<div >
		<span id="post_state">
			<?php 
			wall_post($profile_name,$conn,$uname);
			?>
		</span>
	</div>
</div>
</body>
</html>