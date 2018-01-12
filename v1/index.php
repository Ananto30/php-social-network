<?php
include 'header.php';
include 'dbcon.php';
$statuses = "";
$comment = "";


?>
<!DOCTYPE html>
<html>
<head>

	<meta name="description" content="Torko is a social networking website for people of Bangladesh. Register and enjoy" />
	<meta name="robots" content="index,follow,noarchive">
	<meta name="keywords" content="Torko, social network, Bangladesh" />

	<title>Home</title>
	<link rel="shortcut icon" type="image/x-icon" href="images/icon.ico" />

	<script type="text/javascript">
		function status_update() {
			var ajax = ajaxObj("POST", "status_system.php");
			var status_area = document.getElementById("status_area").value;
			if (status_area=="") {
				alert("Enter something noob");
				return false;
			}
			ajax.onreadystatechange = function() {
				if(ajaxReturn(ajax) == true) {
					_("status_state").innerHTML = ajax.responseText;
					$jq_alert();
				}
			}
			document.getElementById("status_area").value ="";
			ajax.send("status_area="+status_area);
		}

		function status_dlt(id) {
			var ajax = ajaxObj("POST", "status_system.php");
			var a = confirm('All the comments will also be deleted. Are you sure?');
			if(a==true) {
				ajax.onreadystatechange = function() {
					if(ajaxReturn(ajax) == true) {
						_("status_state").innerHTML = ajax.responseText;
						$jq_alert();
					}
				}
				ajax.send("id="+id+"&dlt_post=1");
			}
			
		}
		function comment(status_id) {
			var ajax = ajaxObj("POST", "status_system.php");
			var comment_area = document.getElementById("comment_area"+status_id).value;

			if (comment_area=="") {
				alert("Enter something noob");
				return false;
			}
			ajax.onreadystatechange = function() {
				if(ajaxReturn(ajax) == true) {
					_("status_comments"+status_id).innerHTML = ajax.responseText;
					$jq_alert();

				}
			}
			document.getElementById("comment_area"+status_id).value ="";
			ajax.send("comment_area="+comment_area+"&status_id="+status_id);
		}

		function comment_dlt(comment_id, status_id) {
			var ajax = ajaxObj("POST", "status_system.php");
			var a = confirm('Are you sure?');
			if (a==true) {
				ajax.onreadystatechange = function() {
					if(ajaxReturn(ajax) == true) {
						_("status_comments"+status_id).innerHTML = ajax.responseText;
						$jq_alert();
					}
				}
				ajax.send("comment_id="+comment_id+"&dlt_comment=1&status_id="+status_id);
			}			
		}

		
	</script>
</head>
<body class="home">
	<div id="header" ><h4>Public Posts</h4></div>
	<div id="container" >
		<div id="status">
			<span>
				<textarea id='status_area'></textarea>
				<span id="linkbox"></span>
				<button type='button' onclick='status_update()'>Post</button> 
			</span>
		</div>
		<div id="status_state">
			<?php 

			show_status_with_comments($conn, $uname);
			?>
		</div>
	</div>	
</body>
</html>