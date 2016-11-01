<?php 
$songRank = 0;
$thisSong = 0;
while ($row = mysqli_fetch_assoc($songsQuery)) 
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