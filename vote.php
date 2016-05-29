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
	$gameID = $_POST['gameID'];
	$vote = $_POST['song'];
	$thisGame = db_query("SELECT id from games");
	$ids = array();
	$row = mysqli_fetch_assoc($thisGame);
	$ids = array();
	while ($row = mysqli_fetch_assoc($thisGame)) 
	{
		$ids[] = $row[id];
	}
	echo $gameID;
	echo "<br />";
	if (in_array($gameID,$ids)) {
		echo "good to go";
	} else {
		echo "something wrong";
	}
	echo "<br />";
	print_r (array_values($ids));
	
	$song = db_query("SELECT * from songs WHERE id='$vote'" );
	$songInfo = mysqli_fetch_assoc($song);
	echo $songInfo[title];
	echo "<br />";
	echo $songInfo[rating];
?>
</body>
</html>