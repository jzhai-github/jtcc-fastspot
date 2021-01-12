<?php
//$host = "sbds001.sitevision.com"; //database location
$host = "localhost"; //database location
$user = "jtcc-dev"; //database username
$pass = "VdleL7N_krUS"; //database password
$db_name = "courses"; //database name

//database connection
$conn = mysqli_connect($host, $user, $pass, $db_name);
if (mysqli_connect_errno()){
	echo "Failed to connect to database.";
}
?>