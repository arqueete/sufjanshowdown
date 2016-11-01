<?php 
	include '../includes/dbconnect.php';

	function db_query($query) {
		$connection = db_connect();
		// Query the database
		$result = mysqli_query($connection,$query);

		return $result;
	}
	
	$songsQuery = db_query("SELECT * FROM songs WHERE active=1 AND (`album`=7 OR `album`=8 OR `album`=9 OR `album`=10 OR `album`=11 OR `album`=14 OR `album`=15 OR `album`=16 OR `album`=17 OR `album`=18) ORDER BY xmasrating DESC");
	$gamesQuery = db_query("SELECT SUM(xmasgames) AS games_total FROM songs WHERE active=1 AND (`album`=7 OR `album`=8 OR `album`=9 OR `album`=10 OR `album`=11 OR `album`=14 OR `album`=15 OR `album`=16 OR `album`=17 OR `album`=18)");
	$gamesTotal = mysqli_fetch_assoc($gamesQuery);
	$matchups = ($gamesTotal['games_total'] / 2);
	
	$albumsQuery = db_query("SELECT name,id,boxset,bandcamp,year FROM albums WHERE (`id`=7 OR `id`=8 OR `id`=9 OR `id`=10 OR `id`=11 OR `id`=14 OR `id`=15 OR `id`=16 OR `id`=17 OR `id`=18)");
	$albums = array();

		while ($albumsInfo = mysqli_fetch_assoc($albumsQuery)) 
	{
		$thisAlbum = array();
		$id = $albumsInfo['id'];
		$queryAverageRating = db_query("SELECT AVG(xmasrating) AS songs_average FROM songs WHERE active=1 AND album=$id");
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
		<link rel="canonical" href="http://www.sufjanshowdown.com/leaderboard.php" />
		<link href='https://fonts.googleapis.com/css?family=Roboto:900' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="../normalize.css" />
		<link rel="stylesheet" href="../styles.css" />
	</head>
	<body class="xmas">
		<div class="copy">
			<div class="copy__inner">
				<nav class="nav"><a href="http://www.sufjanshowdown.com" class="nav__item">Regular Voting</a> <a href="index.php" class="nav__item">Christmas Voting</a> <a href="leaderboard.php" class="nav__item nav__item--active">Christmas Leaderboard</a></nav>
				<h1>Christmas Leaderboard</h1>
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
							//sort the albums by rating
							$sortedAlbums = $albums;
							$albumsSorting = array();
							foreach ($sortedAlbums as $key => $row) {
								$albumsSorting[$key]  = $row['rating'];
							}
							array_multisort($albumsSorting, SORT_DESC, $sortedAlbums);
							$totalAlbums = (count($albums) - 1);
						?>
						<table class="leaderboard">
						<thead><th class="leaderboard__header leaderboard__header--rating">Rank</th><th class="leaderboard__header">Album</th><th class="leaderboard__header leaderboard__header--rating">Average Song Rating</th></thead>
						<?php
							$albumRank = 0;
							for ($i = 0; $i <= $totalAlbums; $i++)
							{
								echo "<tr>";
								echo "<td class='leaderboard__cell leaderboard__cell--rank'>";
								if (isset($sortedAlbums[$i - 1]['rating']) && $sortedAlbums[$i - 1]['rating'] == $sortedAlbums[$i]['rating']) {
									echo '&nbsp;';
								} else {
									$albumRank++;
									echo $albumRank;
								}
								echo "</td>";
								echo "<td class='leaderboard__cell'>";
								echo "<i><a href='" . $sortedAlbums[$i]['url'] . "' target='_blank'>" . $sortedAlbums[$i]['name'] . "</a></i> (" . $sortedAlbums[$i]['year'] .")";
								echo "</td>";
								echo "<td class='leaderboard__cell leaderboard__cell--rating'>";
								echo $sortedAlbums[$i]['rating'];
								echo "</td>";
								echo "</td>";
								echo "</tr>";
							}
						?>
						</table>
						
						
						<h2>Top Songs</h2>
						<table class="leaderboard">
						<thead><th class="leaderboard__header leaderboard__header--rating">Rank</th><th class="leaderboard__header">Song</th><th class="leaderboard__header leaderboard__header--rating">Score</th><th class="leaderboard__header leaderboard__header--rating">% Won</th></thead>
						<?php
							$songRank = 0;
							$thisSong = 0;
							while ($row = mysqli_fetch_assoc($songsQuery)) 
							{
								$thisAlbum = $row['album'];
								echo "<tr>";
								echo "<td class='leaderboard__cell leaderboard__cell--rank'>";
								$data[$thisSong] = $row;
								if (isset($data[$thisSong - 1]) && $data[$thisSong - 1]['xmasrating'] == $row['xmasrating']) {
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
								echo $row['xmasrating'];
								echo "</td>";
								echo "</td>";
								echo "<td class='leaderboard__cell leaderboard__cell--rating'>";
								echo (ceil($row['xmaswins'] / $row['xmasgames'] * 100)) . "%";
								echo "</td>";
								echo "</tr>";
								$thisSong++;
							}
						?>
						</table>
						
					</div>
				</div>
			</div>
		</div>
		
		<?php include "../includes/footer.html"; ?>
	</body>
</html>