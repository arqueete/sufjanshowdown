<!doctype html>
<html>
	<head>
		<title>Sufjan Showdown</title>
	</head>
	<body>
		<?php 
			include 'dbconnect.php';
			
			$songs = mysqli_query($mysqli, "SELECT id from songs WHERE active=1");
			$ids = array();
			while ($row = mysqli_fetch_assoc($songs)) {
				$ids[] = $row[1];
			}
			
			/*$ids = array();
			foreach ($rows as $key => $value) {
				$ids[] = $value;
			}*/
			
			foreach ($ids as $key => $value) {
				echo $key;
				echo ": ";
				echo $value;
				echo ", ";
			}
			echo "<br />";
			$idscount = count($ids);
			echo "idscount : " . $idscount;
			$left = rand(1,$idscount);
			echo "<br />";
			echo "left : " . $left; 
			echo "<br />";
			echo gettype($left);
			echo "<br />";
			echo "id : " . $ids[0];
			/*$leftsong = mysqli_query($mysqli, "SELECT title from songs WHERE id=". $ids[$left] );
			$leftsongs = array();
			while ($leftsongs = mysqli_fetch_assoc($leftsong)) {
				$thissong[] = $leftsongs[0];
			}
			echo $thissong[0];*/

		?>
	</body>
</html>