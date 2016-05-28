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
			
			$connection = db_connect();
			$songs = mysqli_query($connection,"SELECT `id` from `songs` WHERE active=1");
			
			
			$ids = array();
			$row = mysqli_fetch_assoc($songs);
			$ids = array();
			while ($row = mysqli_fetch_assoc($songs)) 
			{
				$ids[] = $row[id];
			}
			print_r( array_values($ids));
			$idscount = count($ids) - 1;
			echo "<br />";
			echo "idscount : " . $idscount;
			$left = rand(0,$idscount);
			echo "<br />";
			echo "left : " . $left;
			$leftsongid = $ids[$left];
			echo "<br />";
			echo "songid : " . $leftsongid;
			
			$leftsong = mysqli_query($mysqli, "SELECT `title` from `songs` WHERE id='$ids[$left]'" );
			$leftsongrow = mysqli_fetch_assoc($leftsong);
			$good = mysqli_num_rows($leftsong);
			echo "<br />";
			echo $good;
			echo "<br />";
			echo $leftsongrow[title];
			/*$titles = array();
			while ($leftsongrow = mysqli_fetch_assoc($leftsong)) 
			{
				$titles[] = $row[title];
			}
			echo "<br />";
			print_r( array_values($titles));*/
			/*$ids = array();
			foreach ($rows as $key => $value) {
				$ids[] = $value;
			}*/
			
			/*foreach ($ids as $key => $value) {
				echo $key;
				echo ": ";
				echo $value;
				echo ", ";
			}
			echo "<br />";
			$idscount = count($ids);
			echo "idscount : " . $idscount;
			echo "<br />";
			print_r( array_values($ids));*/
			/*$left = rand(1,$idscount);
			echo "<br />";
			echo "left : " . $left; 
			echo "<br />";
			echo gettype($left);
			echo "<br />";
			echo "id : " . $ids[0];*/
			/*$leftsong = mysqli_query($mysqli, "SELECT title from songs WHERE id=". $ids[$left] );
			$leftsongs = array();
			while ($leftsongs = mysqli_fetch_assoc($leftsong)) {
				$thissong[] = $leftsongs[0];
			}
			echo $thissong[0];*/

		?>
	</body>
</html>