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
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="normalize.css" />
		<link rel="stylesheet" href="styles.css" />
	</head>
	<body>
		<form method="post" action="vote.php">
			<fieldset class="voting">
				<input type="radio" value="<?php echo $left[id]; ?>" name="song" class="game__input" id="left" />
				<label class="game" for="left"><span class="game__song">"<?php echo $left[title]; ?>"</span> <span class="game__from">from</a> <span class="game__album"><?php echo $leftAlbumInfo[name]; ?> <span class="game__year">(<?php echo $leftAlbumInfo[year]; ?>)</span></span>
				<br />
				<?php
					if ($leftAlbumInfo[boxset]) {
						echo "<span class='game__boxset'>" . $leftAlbumInfo[boxset] . "</span>";
					}
				?>
				<?php
					if ($left[url] && $leftAlbumInfo[url]) {
						echo "<iframe style='border: 0; width: 100%; height: 42px;' src='http://bandcamp.com/EmbeddedPlayer/album=". $leftAlbumInfo[url] ."/size=small/bgcol=ffffff/linkcol=333333/track=" . $left[url] . "/transparent=true/' seamless><a href='http://music.sufjan.com/album/carrie-lowell'>" . $left[title] . "by Sufjan Stevens</a></iframe>"; 
					} else if ($right[url] && $rightAlbumInfo[url] == 0) {
						echo "<iframe style='border: 0; width: 100%; height: 42px;' src='http://bandcamp.com/EmbeddedPlayer/track=". $right[url] ."/size=small/bgcol=ffffff/linkcol=0687f5/transparent=true/' seamless><a href='http://music.sufjan.com/album/carrie-lowell'>" . $right[title] . "by Sufjan Stevens</a></iframe>";
					};
				?>
				<?php 
					if ($left[comment]) {
						echo "<span class='game__comment'>" . $left[comment] . "</span>";
					} else {
						echo "<span class='game__comment'>" . $leftAlbumInfo[comment] . "</span>";
					}
				?>
				</label>
				<br />
				<input type="radio" name="song" value="skip" id="skip" class="game__input" />
				<label class="game game--skip" for="skip">
					 Skip this game
				</label>
				<br />
				<input type="radio" value="<?php echo $right[id]; ?>" name="song" class="game__input" id="right">
				<label class="game" for="right">
				<span class="game__song">"<?php echo $right[title]; ?>"</span> <span class="game__from">from</span> <span class="game__album"><?php echo $rightAlbumInfo[name]; ?> <span class="game__year">(<?php echo $rightAlbumInfo[year]; ?>)</span></span>
				<?php
					if ($rightAlbumInfo[boxset]) {
						echo "<span class='game__boxset'>" . $rightAlbumInfo[boxset] . "</span>";
					}
				?>
				
				<?php
					if ($right[url] && $rightAlbumInfo[url] > 0) {
						echo "<iframe style='border: 0; width: 100%; height: 42px;' src='http://bandcamp.com/EmbeddedPlayer/album=". $rightAlbumInfo[url] ."/size=small/bgcol=ffffff/linkcol=333333/track=" . $right[url] . "/transparent=true/' seamless><a href='http://music.sufjan.com/album/carrie-lowell'>" . $right[title] . "by Sufjan Stevens</a></iframe>";
					} else if ($right[url] && $rightAlbumInfo[url] == 0) {
						echo "<iframe style='border: 0; width: 100%; height: 42px;' src='http://bandcamp.com/EmbeddedPlayer/track=". $right[url] ."/size=small/bgcol=ffffff/linkcol=0687f5/transparent=true/' seamless><a href='http://music.sufjan.com/album/carrie-lowell'>" . $right[title] . "by Sufjan Stevens</a></iframe>";
					};
				?>
				<?php 
					if ($right[comment]) {
						echo "<span class='game__comment'>" . $right[comment] . "</span>";
					} else {
						echo "<span class='game__comment'>" . $rightAlbumInfo[comment] . "</span>";
					}
				?>
				</label>
			</fieldset>
			<input type="hidden" name="gameID" value="<?php echo $gameID; ?>" />
			<input type="submit" name="vote" value="Vote" class="voting__submit" /> 
		</form>
	</body>
</html>