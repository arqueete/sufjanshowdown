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
	$thisGame = db_query("SELECT id FROM games");
	$ids = array();
	//$row = mysqli_fetch_assoc($thisGame);
	$ids = array();
	while ($row = mysqli_fetch_assoc($thisGame)) 
	{
		$ids[] = $row[id];
	}
	//echo $gameID;
	//echo "<br />";
	if (in_array($gameID,$ids,$vote)) {
		//now check that song chosen is part of that game
		$theseSongs = db_query("SELECT `left`,`right` FROM `games` where id=$gameID");
		$theseSongsInfo = mysqli_fetch_assoc($theseSongs);
		//echo "<br />";
		//print_r (array_values($theseSongsInfo));
		if (in_array($vote,$theseSongsInfo)) {
			//song and game are valid
		} else {
			echo "song error";
		}
		//$row = mysqli_fetch_assoc($thisSong);
	} else {
		echo "something wrong";
	}
	echo "<br />";
	//print_r (array_values($ids));
	
	$song = db_query("SELECT * from songs WHERE id='$vote'" );
	$songInfo = mysqli_fetch_assoc($song);
	echo $songInfo[title];
	echo "<br />";
	echo $songInfo[rating];
?>
</body>
</html>