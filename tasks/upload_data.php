<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>John Tyler Community College Course Import</title>


</head>

<body>

<?php
/*
require("jtcc_custom_apps.php");
$client_flags = 128; 
$connection = mysql_connect($courses_host, $courses_username, $courses_password) or die ("Unable to connect to server");
$db = mysql_select_db("$courses_database", $connection) or die ("Unable to select database");
*/


include("../includes/courses_conn.php");

$file_spr = 'JTWEBCRSECAT_SPRING.CSV';
$file_sum = 'JTWEBCRSECAT_SUMMER.CSV';
$file_fall = 'JTWEBCRSECAT_FALL.CSV';


//echo $file_fall;
//echo file_exists($file_fall);


// Fall table
if (file_exists($file_fall)) {
	$sql1 = "delete from courses_fall";
	mysqli_query($conn, $sql1);
	
	$sql2 = "LOAD DATA LOCAL INFILE '/srv/sites/dev.jtcc.edu/web/tasks/JTWEBCRSECAT_FALL.CSV' INTO TABLE `courses_fall` FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\n'"; 
	//$sql2 = "LOAD DATA LOCAL INFILE '/srv/sites/dev.jtcc.edu/web/tasks/JTWEBCRSECAT_FALL.CSV' INTO TABLE `courses_fall` FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\r\n'"; 	  
		 
	mysqli_query($conn, $sql2);

	if (!mysqli_query($conn, $sql2)) {
  		echo("Error description: " . mysqli_error($conn));
  	}

}

// Summer table	
if (file_exists($file_sum)) {
	$sql3 = "delete from courses_summer";
	mysqli_query($conn, $sql3);
	
	$sql4 = "LOAD DATA LOCAL INFILE '/srv/sites/dev.jtcc.edu/web/tasks/JTWEBCRSECAT_SUMMER.CSV' INTO TABLE `courses_summer` FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\n'"; 
		 
	mysqli_query($conn, $sql4);

	
			/*if(mysql_error()) {
	
			 echo mysql_error() ."<br>\n";
			} */	
}

// Spring table	
if (file_exists($file_spr)) {
	$sql5 = "delete from courses_spring";
	mysqli_query($conn, $sql5);
	
	$sql6 = "LOAD DATA LOCAL INFILE '/srv/sites/dev.jtcc.edu/web/tasks/JTWEBCRSECAT_SPRING.CSV' INTO TABLE `courses_spring` FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\n'"; 	  
		 
	mysqli_query($conn, $sql6);

	if (!mysqli_query($conn, $sql6)) {
  		echo("Error description: " . mysqli_error($conn));
  	}

}
/*if (file_exists($file_spr)) {
	$mysqli = new mysqli("localhost", "jtcc-dev", "VdleL7N_krUS", "courses");
	$sql5 = "delete from courses_spring;";
	$query = $sql5;
	
	//$sql6 = "LOAD DATA INFILE '/srv/sites/dev.jtcc.edu/web/tasks/JTWEBCRSECAT_SPRING.CSV' INTO TABLE `courses_spring` FIELDS TERMINATED BY ',' ENCLOSED BY '\"' ESCAPED BY '\\\\' LINES TERMINATED BY '\\r\\n'"; 
		 
	$sql6 = "LOAD DATA LOCAL INFILE '/srv/sites/dev.jtcc.edu/web/tasks/JTWEBCRSECAT_SPRING.CSV' IGNORE INTO TABLE `courses_spring` FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\r\n'"; 	 
	$query .=  $sql6 ;
	
	$query .= "drop table if exists tmp_tbl;";
	$query .= "create temporary table tmp_tbl select * from courses_spring limit 0;";
	$query .= "insert into tmp_tbl select distinct * from courses_spring;";
	$query .= "delete from courses_spring;";
	$query .= "insert into courses_spring select distinct * from tmp_tbl;";
	
	
	if ($mysqli->multi_query($query)) {
    do {
        if ($result = $mysqli->store_result()) {
            while ($row = $result->fetch_row()) {
                printf("%s\n", $row[0]);
            }
            $result->free();
        }
        if ($mysqli->more_results()) {
            printf("------\n");
        }
    } while ($mysqli->next_result());
}
	
	

	if (!mysqli_query($conn, $sql6)) {
  		echo("Error description: " . mysqli_error($conn));
  	}
		
}*/


// append files to one table
/*$sql1 = "delete from courses_test";
mysql_query($sql1);

$sql2 = "LOAD DATA INFILE 'D:/inetpub/joomla/customapps/schedule/JTWEBCRSECAT_FALL.csv' INTO TABLE `courses_test` FIELDS TERMINATED BY ',' ENCLOSED BY '\"' ESCAPED BY '\\\\' LINES TERMINATED BY '\\r\\n'"; 
   	 
mysql_query($sql2);

      	
      	if(mysql_error()) {

         echo mysql_error() ."<br>\n";
      	} 

$sql3 = "LOAD DATA INFILE 'D:/inetpub/joomla/customapps/schedule/JTWEBCRSECAT_SUM.csv' INTO TABLE `courses_test` FIELDS TERMINATED BY ',' ENCLOSED BY '\"' ESCAPED BY '\\\\' LINES TERMINATED BY '\\r\\n'"; 
   	 
mysql_query($sql3);

      	
      	if(mysql_error()) {

         echo mysql_error() ."<br>\n";
      	} 		
*/

mysqli_close($conn);
	
echo "Import Complete"
?>

</body>

</html>