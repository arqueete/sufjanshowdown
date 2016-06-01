<?php

	include 'dbconnect.php';

	function db_query($query) {
		$connection = db_connect();
		// Query the database
		$result = mysqli_query($connection,$query);

		return $result;
	}
	
	db_query("DELETE FROM `games` WHERE date < DATE_SUB(NOW() , INTERVAL 1 DAY)");
	/*$toBeDeleted = db_query("SELECT id FROM `games` WHERE date < DATE_SUB(NOW() , INTERVAL 1 DAY)");
	while ($row = mysqli_fetch_assoc($toBeDeleted)) 
	{
		echo $row[id];
		echo "<br />";
	}*/

?>