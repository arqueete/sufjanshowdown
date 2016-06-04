<?php 
	include 'dbconnect.php';

	function db_query($query) {
		$connection = db_connect();
		// Query the database
		$result = mysqli_query($connection,$query);

		return $result;
	}
	
	$songsQuery = db_query("SELECT * FROM songs WHERE active=1 AND games!=0 ORDER BY rating DESC");
	$gamesQuery = db_query("SELECT SUM(games) AS games_total FROM songs WHERE active=1");
	$gamesTotal = mysqli_fetch_assoc($gamesQuery);
	$matchups = ($gamesTotal['games_total'] / 2);
	
	$albumsQuery = db_query("SELECT name,id,boxset,bandcamp,year FROM albums");
	$albums = array();
	while ($albumsInfo = mysqli_fetch_assoc($albumsQuery)) 
	{
		$thisAlbum = array();
		$id = $albumsInfo['id'];
		$queryAverageRating = db_query("SELECT AVG(rating) AS songs_average FROM songs WHERE active=1 AND games!=0 AND album=$id");
		$songsAverageRating = mysqli_fetch_assoc($queryAverageRating);
		$bandcamp = $albumsInfo['bandcamp'];
		if ($albumsInfo['boxset']) {
			$thisAlbum['name'] = $albumsInfo['boxset'];
		} else {
			$thisAlbum['name'] = $albumsInfo['name'];
		}
		$thisAlbum['url'] = $bandcamp;
		$thisAlbum['year'] = $albumsInfo['year'];
		$thisAlbum['rating'] = ceil($songsAverageRating['songs_average']);
		$albums[$id] = $thisAlbum;
	}

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
				<h1>Showdown Leaderboard</h1>
				<span class="subtitle">(Or, Let Us Look Upon Popular Opinion!)</span>
				<p>All songs start with a score of 1600 which rises and falls as they win and lose matchups. Only songs that have had the opportunity to be voted upon appear in the leaderboard. </p>
				<p><a href="/sufjanshowdown/">Back to voting!</a></p>
			</div>
		</div>
		<div class="voting">
			<div class="voting__inner">
				<div class="copy">
					<div class="copy__inner">
						<p><strong><?php echo $matchups; ?></strong> matchups have been voted on.</p>
						<h2>Top Songs</h2>
						<table class="leaderboard">
						<thead><th class="leaderboard__header leaderboard__header--rating">Rank</th><th class="leaderboard__header">Song</th><th class="leaderboard__header leaderboard__header--rating">Score</th><th class="leaderboard__header leaderboard__header--rating">% Won</th></thead>
						<?php
							$songRank = 0;
							$thisSong = 0;
							while (($row = mysqli_fetch_assoc($songsQuery)) && $songRank < 20) 
							{
								$thisAlbum = $row['album'];
								echo "<tr>";
								echo "<td class='leaderboard__cell leaderboard__cell--rank'>";
								$data[$thisSong] = $row;
								if (isset($data[$thisSong - 1]) && $data[$thisSong - 1]['rating'] == $row['rating']) {
									echo '&nbsp;';
								} else {
									$songRank++;
									echo $songRank;
								}
								echo "</td>";
								echo "<td class='leaderboard__cell'>";
								echo "&ldquo;" . $row['title'] . "&rdquo;";
								echo "<br />";
								echo "<span class='leaderboard__album'>from <i><a href='" . $albums[$thisAlbum]['url'] ."' target='_blank'>" . $albums[$thisAlbum]['name'] ."</a></i></span>";
								echo "</td>";
								echo "<td class='leaderboard__cell leaderboard__cell--rating'>";
								echo $row['rating'];
								echo "</td>";
								echo "</td>";
								echo "<td class='leaderboard__cell leaderboard__cell--rating'>";
								echo (ceil($row['wins'] / $row['games'] * 100)) . "%";
								echo "</td>";
								echo "</tr>";
								$thisSong++;
							} 
						?>
						</table>
						<h2>Album Ratings</h2>
						<table class="leaderboard">
						<thead><th class="leaderboard__header leaderboard__header--rating">Year</th><th class="leaderboard__header">Album</th><th class="leaderboard__header leaderboard__header--rating">Average Song Rating</th></thead>
						<?php
							foreach ($albums as $album)
							{
								echo "<tr>";
								echo "<td class='leaderboard__cell leaderboard__cell--rating'>";
								echo $album['year'];
								echo "</td>";
								echo "<td class='leaderboard__cell'>";
								echo "<i><a href='" . $album['url'] . "' target='_blank'>" . $album['name'] . "</a></i>";
								echo "</td>";
								echo "<td class='leaderboard__cell leaderboard__cell--rating'>";
								echo $album['rating'];
								echo "</td>";
								echo "</td>";
								echo "</tr>";
							} 
						?>
						</table>
					</div>
				</div>
			</div>
		</div>
		
		<footer class="copy">
			<div class="copy__inner">
				<nav class="copy__links">
					<a href="mailto:jessica@jessicagleason.com" class="copy__link">Report a problem</a>
				</nav>
			</div>
		</footer>
		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

		  ga('create', 'UA-78646106-2', 'auto');
		  ga('send', 'pageview');

		</script>
	</body>
</html>