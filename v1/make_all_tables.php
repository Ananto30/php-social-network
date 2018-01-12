<?php
include 'dbcon.php';

$conn->set_charset("utf8");

$frnd_req_table = "CREATE TABLE IF NOT EXISTS frndreq (
req_id int(11) NOT NULL AUTO_INCREMENT,
sender VARCHAR(30) NOT NULL,
receiver varchar(30) NOT NULL,
accept boolean NOT NULL,
primary key (req_id)
)";

if ($conn->query($frnd_req_table) === TRUE) {
}else {
	echo "Error: " . $frnd_req_table . "<br>" . $conn->error;
}



$post_table = "CREATE TABLE IF NOT EXISTS post (
post_id int(11) NOT NULL AUTO_INCREMENT,
posted_by VARCHAR(30) NOT NULL,
posted_in varchar(30) NOT NULL,
post varchar(300) NOT NULL,
post_time timestamp DEFAULT CURRENT_TIMESTAMP,
seen boolean NOT NULL,
primary key (post_id)
)";

if ($conn->query($post_table) === TRUE) {
}else {
	echo "Error: " . $post_table . "<br>" . $conn->error;
}


$user_table = "CREATE TABLE IF NOT EXISTS user (
ID int(11) NOT NULL AUTO_INCREMENT,
username varchar(30) NOT NULL,
password varchar(30) NOT NULL,
fname varchar(30) NOT NULL,
lname varchar(30) NOT NULL,
email VARCHAR(50) NOT NULL,
verify boolean not null,
hash varchar(32) not null,
PRIMARY KEY (ID)
)ENGINE=InnoDB";

if ($conn->query($user_table) === TRUE) {

} else {
	echo "Error: " . $user_table . "<br>" . $conn->error;
}


$status_table = "CREATE TABLE IF NOT EXISTS status (
status_id int(11) NOT NULL AUTO_INCREMENT,
status_by VARCHAR(30) NOT NULL,
status varchar(300) NOT NULL,
status_time timestamp DEFAULT CURRENT_TIMESTAMP,
primary key (status_id)
)ENGINE=InnoDB";
if ($conn->query($status_table) === TRUE) {

} else {
	echo "Error: " . $status_table . "<br>" . $conn->error;
}


$comment_table = "CREATE TABLE IF NOT EXISTS status_comments (
comment_id int(11) NOT NULL AUTO_INCREMENT,
status_id int(11) NOT NULL,
comment_by VARCHAR(30) NOT NULL,
comment_in VARCHAR(30) NOT NULL,
comment varchar(300) NOT NULL,
comment_time timestamp DEFAULT CURRENT_TIMESTAMP,
seen_by_owner boolean NOT NULL,
seen_by_commenter boolean not null,
primary key (comment_id),
constraint foreign key (status_id) REFERENCES status(status_id) ON DELETE CASCADE
)ENGINE=InnoDB";
if ($conn->query($comment_table) === TRUE) {

} else {
	echo "Error: " . $comment_table . "<br>" . $conn->error;
}

$info_table = "CREATE TABLE IF NOT EXISTS info(
ID int(11) NOT NULL ,
city varchar(100) NOT NULL,
gender varchar(10) NOT NULL,
reg_date timestamp DEFAULT CURRENT_TIMESTAMP,
last_login timestamp,
language varchar(30),
pro_pic_path varchar(255),
phone_number varchar(11),
professional_skill varchar(30),
nick_name varchar(30),
working_as varchar(50),
birth date NOT NULL,
primary key(ID),
constraint foreign key (ID) REFERENCES user(ID) ON DELETE CASCADE
)ENGINE=InnoDB";

if ($conn->query($info_table) === TRUE) {

} else {
	echo "Error: " . $info_table . "<br>" . $conn->error;
}


?>