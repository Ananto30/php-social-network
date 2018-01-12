<?php
include 'check_login.php';
include 'php_functions.php';
$uname = $_SESSION['username'];
$id = $_SESSION['id'];

//setting server timezone in coockies
$d = new DateTime();
$dtz = ($d->getOffset())/3600;
setcookie('server_time_zone', $dtz, time() + (86400));


?>

<html>
<head>

	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
	<script type="text/javascript" src="main.js"></script>
	<link rel="stylesheet" href="main.css">


	<script >	
		$jq_alert = function(){
			$("#alerts").fadeOut(3000)	
		};
	</script>
	<script type="text/javascript">
		function setCookie(cname, cvalue, exdays) {
			var d = new Date();
			d.setTime(d.getTime() + (exdays*24*60*60*1000));
			var expires = "expires="+d.toUTCString();
			document.cookie = cname + "=" + cvalue + "; " + expires;
		}

		function getCookie(cname) {
			var name = cname + "=";
			var ca = document.cookie.split(';');
			for(var i=0; i<ca.length; i++) {
				var c = ca[i];
				while (c.charAt(0)==' ') c = c.substring(1);
				if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
			}
			return "";
		}
		//setting browser timzone in coockies
		var d = new Date();
		var dtz = -(d.getTimezoneOffset())/60;
		setCookie('browser_time_zone', dtz, 1);


		//notification counter checks new notification in every 5 seconds
		window.onload = notification_count();
		function notification_count() {
			var ajax = ajaxObj("POST", "notification_system.php");
			ajax.onreadystatechange = function() {
				if(ajaxReturn(ajax) == true) {
					_("notification_count").innerHTML = ajax.responseText;
				}
			}
			ajax.send("notification_count=1");
		}
		setInterval(function(){
			notification_count();
		}, 5000);

		//scrolling fuction if there is an anchor in the url

		$(document).ready(function() {
			if (window.location.hash != null && window.location.hash != '') {
				$('body').animate({
					scrollTop: $(window.location.hash).offset().top -200
				}, 1500);
				$(window.location.hash).css('border', '1px solid green'); 
			}
				
		});

	</script>
</head>
<body>

	<div>
		<nav>
			<ul>
				<li class="home"><a href="index.php">Public Posts</a></li>
				<li class="profile"><a href="profile.php">Profile</a></li>
				<li class="notification"><a href="notification.php">Notices<span id="notification_count"></span></a></li>
				<li class="users"><a href="users.php">Users</a></li>
				<li><a  href="logout.php">Leave</a></li>
			</ul>
		</nav>

	</div>
	
</body>
</html>