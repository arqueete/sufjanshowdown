<!doctype html>
<html>
	<head>
		<title>Sufjan Showdown</title>
	</head>
	<body>
		<?php 
			include 'dbconnect.php';
			
			/*function db_query($query) {
				
				$result = mysqli_query($mysqli,$query);
				
				return $result;
			}
			
			
			$songs = db_query("SELECT `id` from `songs` WHERE active=1");*/
			
			function db_query($query) {
				// Connect to the database
				$connection = db_connect();

				// Query the database
				$result = mysqli_query($connection,$query);

				return $result;
			}
			
			//$connection = db_connect();
			//$songs = mysqli_query($connection,"SELECT `id` from `songs` WHERE active=1");
			
			/* Get IDs of all active songs */
			function getSongs() {
				$songs = db_query("SELECT `id` from `songs` WHERE active=1");

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
				$song = db_query("SELECT `title` from `songs` WHERE id='$randomID'" );
				$songInfo = mysqli_fetch_assoc($song);
				//$songTitle = $songInfo;
				
				return $songInfo;
			}
			
			$check = 0;
			function getRandomSongs() {
				$ids = getSongs();
				$left = getRandomSong($ids);
				$right = getRandomSong($ids);
				if ($check < 3) {
					if ($right[title] === $left[title]) {
						echo "oops same song try again";
						echo "<br />";
						$check++;
						getRandomSongs();
						//getTheSongs($check);
					} else {
						echo "good to go";
						echo "<br />";
						echo $right[title];
						echo "<br />";
						echo $left[title];
					}
				} else {
					echo "tried too many times";
				}
			}
			
			getRandomSongs();
				
				
			
			/*$check = 0;
			function getTheSongs($check) {
				$left = getRandomSong($ids);
				echo "<br />";
				echo $left;
				echo "<br />";
				$right = getRandomSong($ids);
				echo "<br />";
				echo $right;
				echo "<br />";
				if ($check > 3) {
					if ($right === $left) {
						echo "oops same song try again";
						$check++;
						//getTheSongs($check);
					} else {
						echo "good to go";
					}
				} else {
					echo "tried too many times";
				}
			
			}
			
			getTheSongs();*/
			
		?>
	</body>
</html>