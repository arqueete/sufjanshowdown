<?php 
	include 'dbconnect.php';
	
	
	function db_query($query) {
		$connection = db_connect();
		// Query the database
		$result = mysqli_query($connection,$query);

		return $result;
	}

	/* Get IDs of all active songs */
	function getSongs() {
		$songs = db_query("SELECT id from songs WHERE active=1");

		$ids = array();
		$row = mysqli_fetch_assoc($songs);
		$ids = array();
		while ($row = mysqli_fetch_assoc($songs)) 
		{
			$ids[] = $row[id];
		}
		
		return $ids;
	}
	
	//TO DO: check for duplicates
	
	function getRandomSong($ids) {
		$idscount = count($ids) - 1;
		//echo "<br />";
		//echo "idscount : " . $idscount;
		$number = rand(0,$idscount);
		$randomID = $ids[$number];
		//echo "<br />";
		//echo "songID: " . $randomID;
		//echo "<br />";
		
		//get the title of the song at that ID
		$song = db_query("SELECT * from songs WHERE id='$randomID'" );
		$songInfo = mysqli_fetch_assoc($song);
		//$songTitle = $songInfo;
		
		return $songInfo;
	}
	
	function getRandomSongs() {
		$ids = getSongs();
		$left = getRandomSong($ids);
		$right = getRandomSong($ids);
		
		$checkCounter = 0;
		function checkSongs($left,$right,$checkCounter) {
			echo "counter " . $checkCounter;
			if ($checkCounter < 3) {
				if ($left[id] == $right[id]) {
					echo "they're the same song";
					echo "<br />";
					$checkCounter++;
					$right = getRandomSong($ids);
					checkSongs($left, $right);
				} else {
					echo "<br />";
					echo  "they're different songs";
					echo "<br />";
				}
			}
			return $right;
		}
		$right = checkSongs($left,$right,$checkCounter);
		$randomSongs = array();
		$randomSongs[] = $left;
		$randomSongs[] = $right;

		return $randomSongs;
	}
	
	$randomSongs = getRandomSongs();
	echo "<br />";
	$left = $randomSongs[0];
	$right = $randomSongs[1];
	$createGame = db_query("INSERT INTO `games` (`id`,`left`,`right`) VALUES ('','$left[id]','$right[id]')");
	$connection = db_connect();
	$gameID = mysqli_insert_id($connection);
	echo "gameID:" . $gameID;
	//echo $left[title];
	//echo "<br />";
	//echo $right[title];
	
	//print_r array_values($randomSongs);
	
	
?>

<!doctype html>
<html>
	<head>
		<title>Sufjan Showdown</title>
	</head>
	<body>
		<form method="post" action="vote.php">
			<fieldset>
				<label><input type="radio" value="<?php echo $left[id]; ?>" name="song"><?php echo $left[title]; ?></label>
				<label><input type="radio" value="<?php echo $right[id]; ?>" name="song"><?php echo $right[title]; ?></label>
			</fieldset>
			<input type="hidden" name="gameID" value="<?php echo $gameID; ?>" />
			<input type="submit" name="vote" value="Vote" /> 
		</form>
	</body>
</html>