<?php 
	include 'dbconnect.php';

	function db_query($query) {
		$connection = db_connect();
		// Query the database
		$result = mysqli_query($connection,$query);

		return $result;
	}
	
	$songsQuery = db_query("SELECT id,title,rating,album FROM songs WHERE active=1 AND games!=0 ORDER BY rating DESC");
	
	$albumsQuery = db_query("SELECT name,id,boxset FROM albums");
	$albums = array();
	while ($albumsInfo = mysqli_fetch_assoc($albumsQuery)) 
	{
		$id = $albumsInfo[id];
		if ($albumsInfo[boxset]) {
			$albums[$id] = $albumsInfo[boxset];
		} else {
			$albums[$id] = $albumsInfo[name];
		}
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
				<a href="/sufjanshowdown/">Back to voting!</a>
				<h1>Showdown Leaderboard</h1>
				<span class="subtitle">(Or, Let Us Look Upon Popular Opinion!)</span>
				<p>All songs start with a score of 1600 which rises and falls as they win and lose matchups. Only songs that have had the opportunity to be voted upon appear in the leaderboard. </p>
			</div>
		</div>
		<div class="voting">
			<div class="voting__inner">
			<table class="leaderboard">
			<thead><th>Song</th><th>Album</th><th>Score</th></thead>
			<?php
				while ($row = mysqli_fetch_assoc($songsQuery)) 
				{
					echo "<tr>";
					echo "<td class='leaderboard__cell'>";
					echo $row[title];
					echo "</td>";
					echo "<td class='leaderboard__cell'>";
					$thisAlbum = $row[album];
					echo $albums[$thisAlbum];
					echo "</td>";
					echo "<td class='leaderboard__cell'>";
					echo $row[rating];
					echo "</td>";
					echo "</tr>";
				} 
			?>
			</table>
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