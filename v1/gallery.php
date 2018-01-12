<?php
include 'header.php';

$images = '';


if (isset($_GET['profile_name'])) {
	$profile_name = $_GET['profile_name'];
}else{
	$profile_name = $uname;
}

$dir = "uploads/".$profile_name;
if (file_exists($dir)) {
	foreach(glob($dir.'/*.*') as $file) {
	    $images .= '<a id="pic_click" href="'.$file.'" target="_blank"><img id="gallery_pic" src="'.$file.'"></a>';
	}
}else {
	$images = "There is no image";
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Images</title>
</head>
<body class="profile">
<div id="header" ><h4>Gallery</h4></div>
	<div id="container">
		<?php
			echo $images;
		?>
	</div>
</body>
</html>