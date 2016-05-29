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

	//check that gameID is a game that exists
	if (in_array($gameID,$ids)) {
		//now check that song chosen is part of that game
		$theseSongs = db_query("SELECT `left`,`right` FROM `games` where id=$gameID");
		$theseSongsInfo = mysqli_fetch_assoc($theseSongs);

		if (in_array($vote,$theseSongsInfo)) {
			//song and game are valid
			$song = db_query("SELECT * from songs WHERE id='$vote'" );
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
					echo "<br />";
				} else {
					echo "this is the losing song";
					echo "<br />";
				}
			}
			
			$winnerSet = db_query("UPDATE `games` SET winner='$vote' WHERE id=$gameID");
			
			//time to do some math and figure out the new scores
			
			/*WinProbability = 1/(10^(( Opponent’s Current Rating–Player’s Current Rating)/400) + 1)
			ScoringPt = 1 point if they win the match, 0 if they lose, and 0.5 for a draw.
			Player’s New Rating = Player’s Old Rating + (K-Value * (ScoringPt–Player’s Win Probability))*/
			
			
		} else {
		
			echo "Song error.";
			
		}

	} else {
	
		echo "Game error.";
		
	}
	echo "<br />";

	
	
?>
</body>
</html>