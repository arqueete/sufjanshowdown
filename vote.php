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
	
	//get submitted values
	$gameID = $_POST['gameID'];
	$vote = $_POST['song'];

	//get the ids of all games
	$games = db_query("SELECT id FROM games");
	$ids = array();
	while ($row = mysqli_fetch_assoc($games)) 
	{
		$ids[] = $row[id];
	}
	
	//do checks to make sure the game and vote are valid

	$validGame = false;
	//check that gameID is a game that exists
	if (in_array($gameID,$ids)) {
		
		$validGame = true;
		
	} else {
	
		echo "Game error.";
		
	}
	
	if ($validGame == true) {
		//get songs IDs for that game
		$theseSongs = db_query("SELECT `left`,`right` FROM `games` where id=$gameID");
		$theseSongsInfo = mysqli_fetch_assoc($theseSongs);

	}
	
	//check that the voted song is a part of this game
	$validSong = false;
	
	if (in_array($vote,$theseSongsInfo)) {
	
		$validSong = true;
		
	} else {
		
		echo "Song error.";
		
	}
	
	echo "<br />";

	
	if ($validSong == true) {
		//array of songs in game and their winning status
		$thisGameSongs = array();
		
		foreach ($theseSongsInfo as $key=>$value) {
			$thisSong = array();
			$thisSong[id] = $value;
			if ($value == $vote) {
				$thisSong[winning] = 1;
			} else {
				$thisSong[winning] = 0;
			}
			$thisSongInfo = db_query("SELECT * FROM songs WHERE id='$thisSong[id]'" );
			$thisSongRow = mysqli_fetch_assoc($thisSongInfo);
			$thisSong[title] = $thisSongRow[title];
			$thisSong[rating] = $thisSongRow[rating];
			$thisGameSongs[] = $thisSong;
		}
		
		print_r(array_values($thisGameSongs));
		
		echo "<br />";
		
		//take song IDs and their winning status and do math to figure out their new rating
		/*foreach ($thisGameSongs as $key=>$value) {
		
			$thisSongID = $value[id];
			$thisSong = db_query("SELECT * FROM songs WHERE id='$thisSongID'" );
			$thisSongInfo = mysqli_fetch_assoc($thisSong);
			echo $thisSongInfo[title];
			//echo $thisSongID;
			echo "<br />";
			$value[title] = $thisSongInfo[title];
		}
		
		print_r(array_values($thisGameSongs));*/
		
		echo "<br />";
	
	}
		
			//song and game are valid
			/*$song = db_query("SELECT * from songs WHERE id='$vote'" );
			$songInfo = mysqli_fetch_assoc($song);
			echo $songInfo[title];
			echo "<br />";
			echo $songInfo[rating];
			echo "<br />";
			foreach ($theseSongsInfo as $key=>$value) {
				echo $value;
				echo "<br />";
				if ($value == $vote) {
					echo "this is the winning song";
					$score = 1;
					echo "<br />";
				} else {
					echo "this is the losing song";
					$losingSong = db_query("SELECT * from songs WHERE id='$value'" );
					echo "<br />";
					$score = 0;
				}
				echo "Score " . $score;
				echo "<br />";
			}*/
			
			$winnerSet = db_query("UPDATE `games` SET winner='$vote' WHERE id=$gameID");
			
			//time to do some math and figure out the new scores
			
			/*WinProbability = 1/(10^(( Opponent’s Current Rating–Player’s Current Rating)/400) + 1)
			ScoringPt = 1 point if they win the match, 0 if they lose, and 0.5 for a draw.
			Player’s New Rating = Player’s Old Rating + (K-Value * (ScoringPt–Player’s Win Probability))*/
			
			
		

	
	echo "<br />";

	
	
?>
</body>
</html>