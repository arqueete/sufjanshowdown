<?php
function getRandomSong($ids) {
	$idscount = count($ids) - 1;

	$number = rand(0,$idscount);
	$randomID = $ids[$number];

	
	//get the title of the song at that ID
	$song = db_query("SELECT * FROM `songs` WHERE `id`='$randomID'" );
	$songInfo = mysqli_fetch_assoc($song);
	
	return $songInfo;
}

function getRandomSongs() {
	$ids = getSongs();
	$left = getRandomSong($ids);
	$right = getRandomSong($ids);
	
	$checkCounter = 0;
	function checkSongs($left,$right,$checkCounter,$ids) {
		//if the same song is randomly picked more than three times in a row, there's probably something wrong
		if ($checkCounter < 3) {
			if ($left['id'] == $right['id']) {
				//they're the same song, get a new right song
				$checkCounter++;
				$right = getRandomSong($ids);
				$right = checkSongs($left, $right, $checkCounter,$ids);
			} else {
				//they're different songs
			}
		} else {
			$right = null;
		}
		return $right;
	}
	$right = checkSongs($left,$right,$checkCounter,$ids);
	if ($right == null) {
		$randomSongs = null;
	} else {
		$randomSongs = array();
		$randomSongs[] = $left;
		$randomSongs[] = $right;
	}

	return $randomSongs;
}

$randomSongs = getRandomSongs();
if ($randomSongs) {
	$left = $randomSongs[0];
	$right = $randomSongs[1];
	$createGame = db_query("INSERT INTO `games` (`id`,`left`,`right`) VALUES ('','$left[id]','$right[id]')");
	$connection = db_connect();
	$gameID = mysqli_insert_id($connection);
	
	function getAlbum($albumID) {
		$album = db_query("SELECT * FROM `albums` WHERE `id`='$albumID'" );
		$albumInfo = mysqli_fetch_assoc($album);
		return $albumInfo;
	}

	$leftAlbumInfo = getAlbum($left['album']);
	$rightAlbumInfo = getAlbum($right['album']);

}
?>