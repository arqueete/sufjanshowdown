<?php 
	include 'dbconnect.php';

	function db_query($query) {
		$connection = db_connect();
		// Query the database
		$result = mysqli_query($connection,$query);

		return $result;
	}
	
	//If they're coming from submitting a vote, process that
	if (!empty($_POST)) {
	
		//get submitted values
		$gameID = $_POST['gameID'];
		$vote = $_POST['song'];
		
		$error = false;
		

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
		
			$error = true;
			
		}
		
		if ($validGame == true && $vote == 'skip') {
		
			//If they didn't vote, just delete the game
			$deleteGame = db_query("DELETE FROM `games` WHERE id=$gameID");
			//echo "Game skipped";
		
		
		} else {
		
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
				
				$error = true;
				
			}


			
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
				
				function updateSong($player,$opponent) {
					$playerID = $player[id];
					$winProbability = 1 / (10 ** (($opponent[rating] - $player[rating])/400) + 1);
					$point = $player[winning];
					$newRating = $player[rating] + (20 * ($point - $winProbability));
					
					$newGames = $player[games] + 1;
					if ($player[winning] == 1) {
						$newWins = $player[wins] + 1;
					} else {
						$newWins = $player[wins];
					}

					$updateSongRating = db_query("UPDATE `songs` SET games='$newGames',wins='$newWins',rating='$newRating' WHERE id=$playerID");

					
				}
				
				updateSong($thisGameSongs[0],$thisGameSongs[1]);
				updateSong($thisGameSongs[1],$thisGameSongs[0]);

				
				$deleteGame = db_query("DELETE FROM `games` WHERE id=$gameID");

			}
		}
	
	}

	/* Get IDs of all active songs */
	function getSongs() {
		$songs = db_query("SELECT id FROM songs WHERE active=1");

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
		$song = db_query("SELECT * FROM songs WHERE id='$randomID'" );
		$songInfo = mysqli_fetch_assoc($song);
		//$songTitle = $songInfo;
		
		return $songInfo;
	}
	
	function getRandomSongs() {
		$ids = getSongs();
		$left = getRandomSong($ids);
		$right = getRandomSong($ids);
		
		$checkCounter = 0;
		function checkSongs($left,$right,$checkCounter,$ids) {
			//if the same song is randomly picked more than three times in a row, there's probably something wrong
			//echo $left[id] . " vs " . $right[id];
			if ($checkCounter < 3) {
				if ($left[id] == $right[id]) {
					//they're the same song, get a new right song
					$checkCounter++;
					$right = getRandomSong($ids);
					$right = checkSongs($left, $right, $checkCounter,$ids);
				} else {
					//they're different songs
				}
			} else {
				echo "Something went wrong.";
			}
			return $right;
		}
		$right = checkSongs($left,$right,$checkCounter,$ids);
		$randomSongs = array();
		$randomSongs[] = $left;
		$randomSongs[] = $right;

		return $randomSongs;
	}
	
	$randomSongs = getRandomSongs();
	$left = $randomSongs[0];
	$right = $randomSongs[1];
	$createGame = db_query("INSERT INTO `games` (`id`,`left`,`right`) VALUES ('','$left[id]','$right[id]')");
	$connection = db_connect();
	$gameID = mysqli_insert_id($connection);
	//echo "gameID:" . $gameID;
	
	function getAlbum($albumID) {
		$album = db_query("SELECT * FROM `albums` WHERE id='$albumID'" );
		$albumInfo = mysqli_fetch_assoc($album);
		return $albumInfo;
	}

	$leftAlbumInfo = getAlbum($left[album]);
	$rightAlbumInfo = getAlbum($right[album]);

?>

<!doctype html>
<html>
	<head>
		<title>Sufjan Showdown</title>
		<meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<link href='https://fonts.googleapis.com/css?family=Roboto:900' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="normalize.css" />
		<link rel="stylesheet" href="styles.css" />
	</head>
	<body>
		<div class="copy">
			<div class="copy__inner">
				<h1>Sufjan Showdown</h1>
				<span class="subtitle">(Or, Consider a New Way of Voting On Favorite Songs!)</span>
				<p>We'll show you two songs by indie artist Sufjan Stevens. You pick which one you like better. Together, we'll determine the most beloved songs in the singer-songwriter's catalog.</p>
			</div>
		</div>
		<?php if (!empty($_POST)) { ?>
			<div class="message">
				<?php 
					if ($error == true) {
						echo "<p>Oops! There was a problem processing your last vote.</p>";
					} else {
						if ($vote == 'skip') {
							echo "<p>Last matchup was skipped.</p>";
						} else {
							echo "<p>Your vote was successfully cast!</p>";
						}
					}
				?>
			</div>
		<?php } ?>
		<form method="post" action="index.php">
			<div class="voting">
				<div class="voting__inner">
					<input type="radio" value="<?php echo $left[id]; ?>" name="song" class="game__input" id="left" />
					<label class="game" for="left">
						<span class="game__song" title="<?php echo $left[title]; ?>">"<?php echo $left[title]; ?>"</span> 
						<span class="game__from">from</span> 
						<span class="game__album">
							<?php if ($leftAlbumInfo[bandcamp]) { ?>
							<a href="<?php echo $leftAlbumInfo[bandcamp]; ?>" target="_blank">
							<?php } ?>
								<?php echo $leftAlbumInfo[name]; ?>
							<?php if ($leftAlbumInfo[bandcamp]) { ?>
							</a> 
							<?php } ?>
							<?php ?>
							<span class="game__year"> (<?php echo $leftAlbumInfo[year]; ?>)</span>
						</span>
						<?php
							if ($leftAlbumInfo[boxset]) {
								echo "<span class='game__boxset'>" . $leftAlbumInfo[boxset] . "</span>";
							}
						?>
						<div class="game__embed">
						<?php
							if ($left[url] && $leftAlbumInfo[url]) {
								echo "<iframe style='border: 0; width: 100%; height: 42px;' src='http://bandcamp.com/EmbeddedPlayer/album=". $leftAlbumInfo[url] ."/size=small/bgcol=ffffff/linkcol=333333/track=" . $left[url] . "/transparent=true/' seamless><a href='http://music.sufjan.com/album/carrie-lowell'>" . $left[title] . "by Sufjan Stevens</a></iframe>"; 
							} else if ($right[url] && $rightAlbumInfo[url] == 0) {
								echo "<iframe style='border: 0; width: 100%; height: 42px;' src='http://bandcamp.com/EmbeddedPlayer/track=". $right[url] ."/size=small/bgcol=ffffff/linkcol=0687f5/transparent=true/' seamless><a href='http://music.sufjan.com/album/carrie-lowell'>" . $right[title] . "by Sufjan Stevens</a></iframe>";
							} else {
								echo "Song embed not available.";
							};
						?>
						</div>
						<?php 
							if ($left[comment]) {
								echo "<span class='game__comment'>" . $left[comment] . "</span>";
							} else {
								echo "<span class='game__comment'>" . $leftAlbumInfo[comment] . "</span>";
							}
						?>
						<span class="game__select">Pick me!</button>
					</label>
					
					<span class="vs">VS.</span>
					
					<input type="radio" value="<?php echo $right[id]; ?>" name="song" class="game__input" id="right">
					<label class="game" for="right">
						<span class="game__song" title="<?php echo $right[title]; ?>">"<?php echo $right[title]; ?>"</span> 
						<span class="game__from">from</span> 
						<span class="game__album">
							<?php if ($rightAlbumInfo[bandcamp]) { ?>
								<a href="<?php echo $rightAlbumInfo[bandcamp]; ?>" target="_blank">
							<?php } ?>
								<?php echo $rightAlbumInfo[name]; ?>
							<?php if ($rightAlbumInfo[bandcamp]) { ?>
							</a> 
							<?php } ?>
							<?php ?>
						
							<span class="game__year"> (<?php echo $rightAlbumInfo[year]; ?>)</span>
						</span>
						<?php
							if ($rightAlbumInfo[boxset]) {
								echo "<span class='game__boxset'>" . $rightAlbumInfo[boxset] . "</span>";
							}
						?>
						<div class="game__embed">
						<?php
							if ($right[url] && $rightAlbumInfo[url] > 0) {
								echo "<iframe style='border: 0; width: 100%; height: 42px;' src='http://bandcamp.com/EmbeddedPlayer/album=". $rightAlbumInfo[url] ."/size=small/bgcol=ffffff/linkcol=333333/track=" . $right[url] . "/transparent=true/' seamless><a href='http://music.sufjan.com/album/carrie-lowell'>" . $right[title] . "by Sufjan Stevens</a></iframe>";
							} else if ($right[url] && $rightAlbumInfo[url] == 0) {
								echo "<iframe style='border: 0; width: 100%; height: 42px;' src='http://bandcamp.com/EmbeddedPlayer/track=". $right[url] ."/size=small/bgcol=ffffff/linkcol=0687f5/transparent=true/' seamless><a href='http://music.sufjan.com/album/carrie-lowell'>" . $right[title] . "by Sufjan Stevens</a></iframe>";
							} else {
								echo "Song embed not available.";
							};
						?>
						</div>
						<?php 
							if ($right[comment]) {
								echo "<span class='game__comment'>" . $right[comment] . "</span>";
							} else {
								echo "<span class='game__comment'>" . $rightAlbumInfo[comment] . "</span>";
							}
						?>
						<span class="game__select">Pick me!</button>
					</label>
					</div>
				
				<input type="radio" name="song" value="skip" id="skip" class="game__input" />
				<label class="game game--skip" for="skip">
					 <span class="game__select">Skip this matchup</button>
				</label>
			
				<input type="hidden" name="gameID" value="<?php echo $gameID; ?>" />
				<input type="submit" name="vote" value="Vote" class="voting__submit" />
				<a href="leaderboard.php" class="voting__leaderboard">View the Leaderboard</a>
			</div>
		</form>
		<footer class="copy">
			<div class="copy__inner">
				<nav class="copy__links">
					<a href="mailto:jessica@jessicagleason.com" class="copy__link">Report a problem</a>
				</nav>
			</div>
		</footer>
	</body>
</html>