<?php 
include("../includes/dbconn.php");

$file_courses = 'JTCC_COURSES.TXT';

if (file_exists($file_courses)) {
	$sql1 = "delete from courses";
	mysql_query($sql1);
	
	$sql2 = "LOAD DATA INFILE '/srv/sites/jtcc.edu/web/schedule/JTCC_COURSES.TXT' INTO TABLE `courses` FIELDS TERMINATED BY ',' ENCLOSED BY '\"' ESCAPED BY '\\\\' LINES TERMINATED BY '\\r\\n'"; 
		 
	mysql_query($sql2);
	
	if(mysql_error()) {
	
	 echo mysql_error() ."<br>\n";
	} 
	
	mysql_close($conn);
	
	echo 'Done';
}
?>