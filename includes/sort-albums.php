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
