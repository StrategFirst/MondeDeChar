<?php
require_once('scrap.php');
?>

<!DOCTYPE html>
<html lang="fr-FR" dir="ltr">

<head>
	<title> Monde de Char </title>
	<meta charset="utf-8" />
	<link href="web.css" rel="stylesheet" />
</head>

<body>

<form action="web.php" method="GET">
	<label for="tag"> Clan à chercher </label>
	<input type="text" name="tag" id="tag" placeholder="Clan TAG" value="VASS" />
	<input type="submit" value="Search !" />
</form>

<hr/>

<?php

function render($clanSkirmishTank) {
	$bonobo = array('M6', 'T-34-85M', 'Cromwell B');
	echo "<table>";
	uasort($clanSkirmishTank, function($playerA, $playerB) { return $playerB['player']['last_battle_time'] - $playerA['player']['last_battle_time'];});
	foreach($clanSkirmishTank as $playerName => $playerData) {
		$tankData = $playerData['tank'];
		if( sizeof($tankData) > 0 ) {
			echo "<tr>";
			$playerSkirmishCount = array_reduce($tankData, function($carry, $tank) { return $carry + $tank["battles_count"]; }, 0);
			$sortedTankData = $tankData;
			usort( $sortedTankData , function($tankA, $tankB) { return $tankB["battles_count"] - $tankA["battles_count"]; } );
			printf('<td> %s </td> <th> %s </th> <td> (%d) </td>', timestampToDelayString($playerData['player']['last_battle_time']), $playerName, $playerSkirmishCount) ;
			for($i = 0; $i < min(sizeof($sortedTankData), 8) ; $i++) {
				$tank = $sortedTankData[$i];
				if( in_array( $tank["name"], $bonobo ) ) {
					$_class = "bonobo";
				} else if( $tank["premium"] == 1 ) {
					$_class = "premium";
				} else if ( $tank["collector_vehicle"] == 1 ) {
					$_class = "collection";
				} else {
					$_class = "normal";
				}
				echo '<td class="char-' . $_class . '">';
				printf(" <b> %s </b> ", $tank["name"]);
				printf(" %3d%% ", 100 * $tank["battles_count"] / $playerSkirmishCount);
				echo "</td>";
			}
			echo "</tr>";
		}
	}

	echo "</table>";
}

if( isset($_GET['tag']) ) {
	render(getClanSkirmishData($_GET['tag']));
} else {
	echo 'Add GET parameter : "tag"';
}
?>

<hr/>

</body>
</html>
