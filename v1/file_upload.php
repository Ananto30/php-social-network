<?php 
	
session_start();
$uname = $_SESSION['username'];
$id = $_SESSION['id'];
include 'dbcon.php';

if(isset($_POST['upload_pic'])) {
	$target_dir = "uploads/";
	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	$uploadOk = 1;
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
// Check if image file is a actual image or fake image
	$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
	if($check !== false) {
		echo "File is an image - " . $check["mime"] . ".";
		$uploadOk = 1;
	} else {
		echo "File is not an image.";
		$uploadOk = 0;
	}
	
	// Check if file already exists
	if (file_exists($target_file)) {
		echo "Sorry, file already exists.";
		$uploadOk = 0;
	}
	// Check file size
	if ($_FILES["fileToUpload"]["size"] > 1048576) {
		echo "Sorry, your file is too large.";
		$uploadOk = 0;
	}
	// Allow certain file formats
	if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
		&& $imageFileType != "gif" ) {
		echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
	$uploadOk = 0;
}
	// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
	echo "Sorry, your file was not uploaded.";
	header("Location: profile.php");
	// if everything is ok, try to upload file
} else {
	$temp =  $_FILES["fileToUpload"]["name"];
	$dir = "uploads/".$uname;
	if (!file_exists($dir)) {
		mkdir($dir, 0777, true);
	}
	
	$newfile = $dir."/". $temp;
	if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $newfile)) {
		// $pro_pic_sql = mysqli_query($conn, "SELECT pro_pic_path FROM users WHERE username='$uname' AND pro_pic_path!=''");
		// if (mysqli_num_rows($pro_pic_sql)) {
		// 	$row = mysqli_fetch_array($pro_pic_sql);
		// 	unlink($row['pro_pic_path']);
		// }
		
		mysqli_query($conn, "UPDATE info SET pro_pic_path='$newfile' WHERE ID='$id'");

		header("Location: profile.php");
		echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
	} else {
		echo "Sorry, there was an error uploading your file.";
		header("Location: profile.php");
	}
}
}


?>