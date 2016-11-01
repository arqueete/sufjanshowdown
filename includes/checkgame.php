<?php
	//get submitted values
	$gameID = $_POST['gameID'];
	$vote = $_POST['song'];
	
	$error = false;
	

	//get the ids of all games
	$games = db_query("SELECT `id` FROM `games`");
	$ids = array();
	while ($row = mysqli_fetch_assoc($games)) 
	{
		$ids[] = $row['id'];
	}
	
	//do checks to make sure the game and vote are valid

	$validGame = false;
	//check that gameID is a game that exists
	if (in_array($gameID,$ids)) {
		
		$validGame = true;
		
	} else {
	
		$error = true;
		
	}
?>