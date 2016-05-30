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
	echo "<br />";
	$left = $randomSongs[0];
	$right = $randomSongs[1];
	$createGame = db_query("INSERT INTO `games` (`id`,`left`,`right`) VALUES ('','$left[id]','$right[id]')");
	$connection = db_connect();
	$gameID = mysqli_insert_id($connection);
	echo "gameID:" . $gameID;
	
	function getAlbum($albumID) {
		$album = db_query("SELECT * FROM `albums` WHERE id='$albumID'" );
		$albumInfo = mysqli_fetch_assoc($album);
		return $albumInfo;
	}
	echo "<br />";

	$leftAlbumInfo = getAlbum($left[album]);
	$rightAlbumInfo = getAlbum($right[album]);

?>

<!doctype html>
<html>
	<head>
		<title>Sufjan Showdown</title>
	</head>
	<body>
		<form method="post" action="vote.php">
			<fieldset>
				<label><input type="radio" value="<?php echo $left[id]; ?>" name="song">"<?php echo $left[title]; ?>" from <?php echo $leftAlbumInfo[name]; ?> (<?php echo $leftAlbumInfo[year]; ?>)
				<br />
				<?php
					if ($leftAlbumInfo[boxset]) {
						echo $leftAlbumInfo[boxset];
						echo "<br />";
					}
				?>
				<?php
					if ($left[url] && $leftAlbumInfo[url]) {
						echo "<iframe style='border: 0; width: 100%; height: 42px;' src='http://bandcamp.com/EmbeddedPlayer/album=". $leftAlbumInfo[url] ."/size=small/bgcol=ffffff/linkcol=333333/track=" . $left[url] . "/transparent=true/' seamless><a href='http://music.sufjan.com/album/carrie-lowell'>" . $left[title] . "by Sufjan Stevens</a></iframe>";
					};
				?>
				<?php 
					if ($left[comment]) {
						echo $left[comment];
					} else {
						echo $leftAlbumInfo[comment];
					}
				?>
				</label>
				<br />
				<label><input type="radio" value="<?php echo $right[id]; ?>" name="song">"<?php echo $right[title]; ?>" from <?php echo $rightAlbumInfo[name]; ?> (<?php echo $rightAlbumInfo[year]; ?>)<br />
				<?php
					if ($rightAlbumInfo[boxset]) {
						echo $rightAlbumInfo[boxset];
						echo "<br />";
					}
				?>
				
				<?php
					if ($right[url] && $rightAlbumInfo[url]) {
						echo "<iframe style='border: 0; width: 100%; height: 42px;' src='http://bandcamp.com/EmbeddedPlayer/album=". $rightAlbumInfo[url] ."/size=small/bgcol=ffffff/linkcol=333333/track=" . $right[url] . "/transparent=true/' seamless><a href='http://music.sufjan.com/album/carrie-lowell'>" . $right[title] . "by Sufjan Stevens</a></iframe>";
					};
				?>
				<?php 
					if ($right[comment]) {
						echo $right[comment];
					} else {
						echo $rightAlbumInfo[comment];
					}
				?>
				</label>
			</fieldset>
			<input type="hidden" name="gameID" value="<?php echo $gameID; ?>" />
			<input type="submit" name="vote" value="Vote" /> 
		</form>
	</body>
</html>