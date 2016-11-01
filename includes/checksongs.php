<?php
	//get songs IDs for that game
	$theseSongs = db_query("SELECT `left`,`right` FROM `games` where `id`=$gameID");
	$theseSongsInfo = mysqli_fetch_assoc($theseSongs);


	//check that the voted song is a part of this game
	$validSong = false;

	if (in_array($vote,$theseSongsInfo)) {

		$validSong = true;
		
	} else {
		
		$error = true;
		
	}

?>