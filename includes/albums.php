<?php	
	while ($albumsInfo = mysqli_fetch_assoc($albumsQuery)) 
	{
		$thisAlbum = array();
		$id = $albumsInfo['id'];
		$queryAverageRating = db_query("SELECT AVG(rating) AS songs_average FROM songs WHERE active=1 AND games>10 AND album=$id");
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