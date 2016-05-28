<!doctype html>
<html>
	<head>
		<title>Sufjan Showdown</title>
	</head>
	<body>
		<?php 
			include 'dbconnect.php';
			
			function db_query($query) {
				// Connect to the database
				$connection = db_connect();

				// Query the database
				$result = mysqli_query($connection,$query);

				return $result;
			}

			/* Get IDs of all active songs */
			function getSongs() {
				$songs = db_query("SELECT id from songs WHERE active=1");

				$ids = array();
				$row = mysqli_fetch_assoc($songs);
				$ids = array();
				while ($row = mysqli_fetch_assoc($songs)) 
				{
					$ids[] = $row[id];
				}
				
				return $ids;
			}
			
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
				$song = db_query("SELECT * from songs WHERE id='$randomID'" );
				$songInfo = mysqli_fetch_assoc($song);
				//$songTitle = $songInfo;
				
				return $songInfo;
			}
			
			$check = 0;
			function getRandomSongs() {
				$ids = getSongs();
				$left = getRandomSong($ids);
				$right = getRandomSong($ids);
				$randomSongs = array();
				$randomSongs[] = $left;
				$randomSongs[] = $right;
				
				return $randomSongs;
			}
			
			$randomSongs = getRandomSongs();
			echo "<br />";
			$left = $randomSongs[0];
			$right = $randomSongs[1];
			
			echo $left[title];
			echo "<br />";
			echo $right[title];
			
			//print_r array_values($randomSongs);
			
			
		?>
	</body>
</html>