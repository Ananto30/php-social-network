<?php 
include 'header.php';


?>
<!DOCTYPE html>
<html>
<head>
	<title>Notices</title>
	<script type="text/javascript">

		window.onload = show_frndreq();
		window.onload = show_comments();
		window.onload = show_posts();

		function show_frndreq() {
			var ajax = ajaxObj("POST", "notification_system.php");
			ajax.onreadystatechange = function() {
				if(ajaxReturn(ajax) == true) {
					if(ajax.responseText != "") {
						_("frnd_req").innerHTML = ajax.responseText;
					}
				}
			}
			ajax.send("frnd_req=1");
		}
		function show_comments() {
			var ajax = ajaxObj("POST", "notification_system.php");
			ajax.onreadystatechange = function() {
				if(ajaxReturn(ajax) == true) {
					if(ajax.responseText != "") {
						_("comments").innerHTML = ajax.responseText;
					}
					
				}
			}
			ajax.send("comments=1");
		}
		

		function seen_comment(comment_id, status_id) {
			var ajax = ajaxObj("POST", "notification_system.php");
			ajax.onreadystatechange = function() {
				if(ajaxReturn(ajax) == true) {
				}
			}
			var c = 'comments'+comment_id;
			ajax.send("seen_comment=1&comment_id="+comment_id+"&status_id="+status_id);
			window.location.href = 'index.php#comments'+comment_id;
			
		}
		
		function show_posts() {
			var ajax = ajaxObj("POST", "notification_system.php");
			ajax.onreadystatechange = function() {
				if(ajaxReturn(ajax) == true) {
					if(ajax.responseText != "") {
						_("wallpost").innerHTML = ajax.responseText;
					}
					
				}
			}
			ajax.send("wallpost=1");
		}
		function seen_post(post_id) {
			var ajax = ajaxObj("POST", "notification_system.php");
			ajax.onreadystatechange = function() {
				if(ajaxReturn(ajax) == true) {
				}
			}
			ajax.send("seen_post=1&post_id="+post_id);
			window.location.href = 'profile.php#post'+post_id;
			
			
		}

	</script>
</head>
<body class="notification">
<div id="header" ><h4>Notices</h4></div>
	<div id="container">
		<div >
			<h3><u>Friend Requests</u></h3>
			<span id="frnd_req">
				No new friend invitation
			</span>
		</div>
		<div>
			<h3><u>New comments</u></h3>
			<span id="comments">
				No new comment in your status
			</span>
		</div>
		<div>
			<h3><u>New post on your profile</u></h3>
			<span id="wallpost">
				No new post on your profile
			</span>
		</div>
	</div>
</body>
</html>