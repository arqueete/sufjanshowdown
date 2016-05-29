<!doctype html>
<html>
<head><title>Results</title></head>
<body>
<?php 
	include 'dbconnect.php';
	function db_query($query) {
		// Connect to the database
		$connection = db_connect();

		// Query the database
		$result = mysqli_query($connection,$query);

		return $result;
	}
	$vote = $_POST['song'];
	$song = db_query("SELECT * from songs WHERE id='$vote'" );
	$songInfo = mysqli_fetch_assoc($song);
	echo $songInfo[title];
	echo "<br />";
	echo $songInfo[rating];
?>
</body>
</html>