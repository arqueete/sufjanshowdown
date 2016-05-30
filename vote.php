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
			$thisSong[games] = $thisSongRow[games];
			$thisSong[wins] = $thisSongRow[wins];
			$thisGameSongs[] = $thisSong;
		}
		
		//time to do some math and figure out the new scores
			
			/*WinProbability = 1/(10^(( Opponent’s Current Rating–Player’s Current Rating)/400) + 1)
			ScoringPt = 1 point if they win the match, 0 if they lose, and 0.5 for a draw.
			Player’s New Rating = Player’s Old Rating + (K-Value * (ScoringPt–Player’s Win Probability))*/
		
		function updateSong($player,$opponent) {
			$playerID = $player[id];
			$winProbability = 1 / (10 ** (($opponent[rating] - $player[rating])/400) + 1);
			$point = $player[winning];
			$newRating = $player[rating] + (20 * ($point - $winProbability));
			echo "<br />";
			echo $player[title];
			echo "<br />";
			echo $player[id];
			echo "<br />";
			echo $newRating;
			echo "<br />";
			
			$newGames = $player[games] + 1;
			if ($player[winning] == 1) {
				$newWins = $player[wins] + 1;
			} else {
				$newWins = $player[wins];
			}
			echo "games: " . $newGames;
			echo "<br />";
			echo "wins: " . $newWins;
			$updateSongRating = db_query("UPDATE `songs` SET games='$newGames',wins='$newWins',rating='$newRating' WHERE id=$playerID");
			echo "<br />";
			
		}
		
		updateSong($thisGameSongs[0],$thisGameSongs[1]);
		updateSong($thisGameSongs[1],$thisGameSongs[0]);
		
		//print_r(array_values($thisGameSongs));
		
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
		
		$deleteGame = db_query("DELETE FROM `games` WHERE id=$gameID");
			echo "<br />";
			echo $deleteGame;
	
	}

?>
</body>
</html>