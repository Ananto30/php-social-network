<?php


function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}
function my_time($input) {
	$hours = $_COOKIE['browser_time_zone'] - $_COOKIE['server_time_zone'];
	$date = new DateTime($input);
	$date->modify("+".$hours." hours");
	$date = $date->format('Y-m-d H:i');
	return date("d M Y g:i A", strtotime($date));
}
function _make_url_clickable_cb($matches) {
	$ret = '';
	$url = $matches[2];

	if ( empty($url) )
		return $matches[0];
	// removed trailing [.,;:] from URL
	if ( in_array(substr($url, -1), array('.', ',', ';', ':')) === true ) {
		$ret = substr($url, -1);
		$url = substr($url, 0, strlen($url)-1);
	}
	return $matches[1] . "<a href=\"$url\" rel=\"nofollow\" target='_blank'>$url</a>" . $ret;
}

function _make_web_ftp_clickable_cb($matches) {
	$ret = '';
	$dest = $matches[2];
	$dest = 'http://' . $dest;

	if ( empty($dest) )
		return $matches[0];
	// removed trailing [,;:] from URL
	if ( in_array(substr($dest, -1), array('.', ',', ';', ':')) === true ) {
		$ret = substr($dest, -1);
		$dest = substr($dest, 0, strlen($dest)-1);
	}
	return $matches[1] . "<a href=\"$dest\" rel=\"nofollow\" target='_blank'>$dest</a>" . $ret;
}

function _make_email_clickable_cb($matches) {
	$email = $matches[2] . '@' . $matches[3];
	return $matches[1] . "<a href=\"mailto:$email\" target='_blank'>$email</a>";
}

function make_clickable($ret) {
	$ret = ' ' . $ret;
	// in testing, using arrays here was found to be faster
	$ret = preg_replace_callback('#([\s>])([\w]+?://[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', '_make_url_clickable_cb', $ret);
	$ret = preg_replace_callback('#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', '_make_web_ftp_clickable_cb', $ret);
	$ret = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', '_make_email_clickable_cb', $ret);

	// this one is not in an array because we need it to run last, for cleanup of accidental links within links
	$ret = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret);
	$ret = trim($ret);
	return $ret;
}
function show_status_with_comments($conn, $uname) {
	$status_sql = "SELECT * FROM status ORDER BY status_time DESC ";
	$reslt = $conn->query($status_sql);
	if ($reslt->num_rows > 0) {
		while($row = $reslt->fetch_assoc()) {
			
			$status_id = $row['status_id'];
			$comments = "<span id='status_comments".$status_id."'>";
			$comment_sql = "SELECT * FROM status_comments,status WHERE status_comments.status_id=status.status_id AND status_comments.status_id='$status_id' ORDER BY status_comments.comment_time ASC ";
			$reslt2 = $conn->query($comment_sql);
			if ($reslt2->num_rows > 0) {
				while($row2 = $reslt2->fetch_assoc()) {
					$dlt = "";
					$comments .= "<span  style='display:block;' id='comments".$row2['comment_id']."'>";
					if($row2['comment_by']==$uname || $row['status_by']==$uname) {
						$dlt = "<button type='button' class='delete_button' onclick='comment_dlt(\"".$row2['comment_id']."\",\"".$row['status_id']."\")'>delete</button>";
					}
					$comments .=  "<p style='margin-left:40px;'>".show_name($conn, $row2['comment_by'])."<span id='comment_description'> commented in ".my_time($row2['comment_time'])." ".$dlt."</span><br>".emoticons($row2['comment'])."</p></span>";

				}

			}
			$comments .= "</span><br>";
			$dlt = "";
			if($row['status_by']==$uname) {
				$dlt = "<button type='button' class='delete_button' onclick='status_dlt(\"".$row['status_id']."\")'>delete</button>";
			}
			echo "<div id='other_status'>".show_name($conn, $row['status_by'])."<span id='status_description'> posted in ".my_time($row['status_time'])." ".$dlt."</span><br><hr>".make_clickable(emoticons($row['status']))."<br>".$comments."
			<span><textarea class='comment_area' id='comment_area".$row['status_id']."'></textarea>
				<button style='float:right;' type='button' onclick='comment(\"".$row['status_id']."\")'>Comment</button> 
			</span></div>";
			$comments = "";
			$status_id = "";
		}
	}
}

function show_comments_of_status($status_id, $conn, $uname) {
	$comments = "<span id='status_comments".$status_id."'>";
	$comment_sql = "SELECT * FROM status_comments,status WHERE status_comments.status_id=status.status_id AND status_comments.status_id='$status_id' ORDER BY status_comments.comment_time ASC ";
	$reslt2 = $conn->query($comment_sql);
	if ($reslt2->num_rows > 0) {
		while($row2 = $reslt2->fetch_assoc()) {
			$dlt = "";
			$comments .= "<span style='display:block;' id='comments".$row2['comment_id']."'>";
			if($row2['comment_by']==$uname || $row2['comment_in']==$uname) {
				$dlt = "<button type='button' class='delete_button' onclick='comment_dlt(\"".$row2['comment_id']."\",\"".$row2['status_id']."\")'>delete</button>";
			}
			$comments .=  "<p style='margin-left:40px;'>".show_name($conn, $row2['comment_by'])."<span id='comment_description'> commented in ".my_time($row2['comment_time'])." ".$dlt."</span><br>".emoticons($row2['comment'])."</p></span>";

		}
		$comments .= "</span><br>";
	}
	echo $comments;
}
function wall_post($profile_name,$conn,$uname) {
	
	$prevpost_sql = "SELECT * FROM post WHERE posted_in='$profile_name' ORDER BY post_time DESC";
	$reslt = $conn->query($prevpost_sql);
	if ($reslt->num_rows > 0) {
		while($row = $reslt->fetch_assoc()) {
			$dlt = "";
			if($row['posted_by']==$uname || $row['posted_in']==$uname) {
				$dlt = "<button type='button' class='delete_button' onclick='dlt_status(\"".$profile_name."\",\"".$row['post_id']."\")'>delete</button>";
			}
			echo "<div id='posts'> <span style='display:block;padding:10px;' id='post".$row['post_id']."' >".show_name($conn, $row['posted_by'])."<span id='comment_description'> posted in ".my_time($row['post_time'])." ".$dlt."</span><br><hr>".emoticons($row['post'])."</span></div>";
		}
	}
	
}

function is_url($url) {
	$regex = "((https?|ftp)\:\/\/)?"; // SCHEME 
    $regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass 
    $regex .= "([a-z0-9-.]*)\.([a-z]{2,3})"; // Host or IP 
    $regex .= "(\:[0-9]{2,5})?"; // Port 
    $regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path 
    $regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query 
    $regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor 

    if(preg_match("/^$regex$/", $url)) 
    { 
    	return true; 
    }else{
    	return false;
    } 
}

function emoticons($text) {
	if (is_url($text)) {
		return $text;
	}
	$array = array(
		":)"    => '<img src="images/emoticons/smile.png"/>',
		":-)"   => '<img src="images/emoticons/smile.png"/>',
		":("    => '<img src="images/emoticons/sad.png"/>',
		":-("   => '<img src="images/emoticons/sad.png"/>',
		":'("   => '<img src="images/emoticons/cry.png"/>',
		":D"    => '<img src="images/emoticons/teeth.png"/>',
		";D"    => '<img src="images/emoticons/teeth.png"/>',
		":-D"   => '<img src="images/emoticons/teeth.png"/>',
		";)"    => '<img src="images/emoticons/wink.png"/>',
		";-)"   => '<img src="images/emoticons/wink.png"/>',
		":p"    => '<img src="images/emoticons/tongue.png"/>',
		";p"    => '<img src="images/emoticons/tongue.png"/>',
		":P"    => '<img src="images/emoticons/tongue.png"/>',
		";P"    => '<img src="images/emoticons/tongue.png"/>',
		":-p"   => '<img src="images/emoticons/tongue.png"/>',
		":-P"   => '<img src="images/emoticons/tongue.png"/>',
		">:o"   => '<img src="images/emoticons/angry.png"/>',
		">:O"   => '<img src="images/emoticons/angry.png"/>',
		">:-O"   => '<img src="images/emoticons/angry.png"/>',
		">:-o"   => '<img src="images/emoticons/angry.png"/>',
		":@"   => '<img src="images/emoticons/angry.png"/>',
		":o"    => '<img src="images/emoticons/surprised.png"/>',
		":-o"   => '<img src="images/emoticons/surprised.png"/>',
		":O"    => '<img src="images/emoticons/surprised.png"/>',
		":-O"   => '<img src="images/emoticons/surprised.png"/>',
		"-_-"   => '<img src="images/emoticons/squinting.png"/>',
		"(y)"    => '<img src="images/emoticons/like.png"/>',
		"(Y)"    => '<img src="images/emoticons/like.png"/>',
		"O:)"   => '<img src="images/emoticons/angel.png"/>',
		"o:)"   => '<img src="images/emoticons/angel.png"/>',
		"o:-)"   => '<img src="images/emoticons/angel.png"/>',
		"O:-)"   => '<img src="images/emoticons/angel.png"/>',
		"o.O"   => '<img src="images/emoticons/confused.png"/>',
		"O.o"   => '<img src="images/emoticons/confused.png"/>',
		":3"   => '<img src="images/emoticons/duckface.png"/>',
		"3:D"   => '<img src="images/emoticons/devil.png"/>',
		"3:-D"   => '<img src="images/emoticons/devil.png"/>',
		"8)"   => '<img src="images/emoticons/glasses.png"/>',
		"8-)"   => '<img src="images/emoticons/glasses.png"/>',
		">:("   => '<img src="images/emoticons/grumpy.png"/>',
		">:-("   => '<img src="images/emoticons/grumpy.png"/>',
		"<3"   => '<img src="images/emoticons/heart.png"/>',
		"^_^"   => '<img src="images/emoticons/kiki.png"/>',
		"^-^"   => '<img src="images/emoticons/kiki.png"/>',
		":*"   => '<img src="images/emoticons/kiss.png"/>',
		":-*"   => '<img src="images/emoticons/kiss.png"/>',
		":v"   => '<img src="images/emoticons/packman.png"/>',
		":V"   => '<img src="images/emoticons/packman.png"/>',
		"B|"   => '<img src="images/emoticons/sunglasses.png"/>',
		"B-|"   => '<img src="images/emoticons/sunglasses.png"/>',
		":/"   => '<img src="images/emoticons/unsure.png"/>',
		":\'"   => '<img src="images/emoticons/unsure.png"/>',
		":-/"   => '<img src="images/emoticons/unsure.png"/>',
		":-\'"   => '<img src="images/emoticons/unsure.png"/>',
		);


                //foreach($array as $emoticon => $graphic) {
				//$text   = preg_replace('#(^|\W)('.preg_quote($emoticon,'#').')($|\W)#', "$1<img src='$graphic' alt='$emoticon' />$3", $text);
                //}

	return strtr($text,$array);
}

function show_name($conn, $username) {
	$sql = mysqli_query($conn, "SELECT fname, lname FROM user WHERE username='$username'");
	$row = mysqli_fetch_array($sql);
	return "<a href='profile.php?profile_name=".$username."'>". ucwords($row['fname']." ".$row['lname']) ."</a>";
}

?>