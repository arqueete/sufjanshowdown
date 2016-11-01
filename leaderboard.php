<?php 
	include 'includes/dbconnect.php';

	function db_query($query) {
		$connection = db_connect();
		// Query the database
		$result = mysqli_query($connection,$query);

		return $result;
	}
	
	$songsQuery = db_query("SELECT * FROM songs WHERE active=1 AND games>10 ORDER BY rating DESC");
	$gamesQuery = db_query("SELECT SUM(games) AS games_total FROM songs WHERE active=1");
	$gamesTotal = mysqli_fetch_assoc($gamesQuery);
	$matchups = ($gamesTotal['games_total'] / 2);
	
	$albumsQuery = db_query("SELECT name,id,boxset,bandcamp,year FROM albums");
	$albums = array();
	
	include "includes/albums.php";
	

?>

<!doctype html>
<html>
	<head>
		<title>Sufjan Showdown</title>
		<meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="canonical" href="http://www.sufjanshowdown.com/leaderboard.php" />
		<link href='https://fonts.googleapis.com/css?family=Roboto:900' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="normalize.css" />
		<link rel="stylesheet" href="styles.css" />
	</head>
	<body>
		<div class="copy">
			<div class="copy__inner">
				<nav class="nav"><a href="index.php" class="nav__item">Vote</a> <a href="leaderboard.php" class="nav__item nav__item--active">Leaderboard</a></nav>
				<h1>Showdown Leaderboard</h1>
				<span class="subtitle">(Or, Let Us Look Upon Popular Opinion!)</span>
				<p>All songs start with a rating of 1600 which rises or falls according to how they fare against other songs in matchups. Only songs that have been in at least ten completed matchups appear in the leaderboard and factor into album averages.</p>
			</div>
		</div>
		<div class="voting">
			<div class="voting__inner">
				<div class="copy">
					<div class="copy__inner">
						<p class="copy__highlight"><strong><?php echo $matchups; ?></strong> matchups have been voted on so far. <a href="index.php">Every vote counts!</a></p>
						
						<h2>Average Song Rating by Album</h2>
						
						<?php
							include "includes/sort-albums.php";
						?>
						<table class="leaderboard">
						<thead><th class="leaderboard__header leaderboard__header--rating">Rank</th><th class="leaderboard__header">Album</th><th class="leaderboard__header leaderboard__header--rating">Average Song Rating</th></thead>
						<?php
							include "includes/albums-table.php";
						?>
						</table>
						
						
						<h2>Top Songs</h2>
						<table class="leaderboard">
						<thead><th class="leaderboard__header leaderboard__header--rating">Rank</th><th class="leaderboard__header">Song</th><th class="leaderboard__header leaderboard__header--rating">Score</th><th class="leaderboard__header leaderboard__header--rating">% Won</th></thead>
						<?php
							include "includes/songs-table.php";
						?>
						</table>
						
					</div>
				</div>
			</div>
		</div>
		
		<?php include "includes/footer.html"; ?>
	</body>
</html>