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