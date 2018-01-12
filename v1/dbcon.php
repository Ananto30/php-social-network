<?php
define('DB_HOST', 'localhost');
define('DB_PORT', '');
define('DB_USER', 'id4257406_root');
define('DB_PASS', 'root123');
define('DB_NAME', 'id4257406_torko');

$dbhost = constant("DB_HOST"); // Host name 
$dbport = constant("DB_PORT"); // Host port
$dbusername = constant("DB_USER"); // Mysql username 
$dbpassword = constant("DB_PASS"); // Mysql password 
$db_name = constant("DB_NAME"); // Database name 

// Create connection


$conn = new mysqli($dbhost, $dbusername, $dbpassword, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    }

?>