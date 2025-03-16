<?php
require_once('scrap.php');
?>

<!DOCTYPE html>
<html lang="fr-FR" dir="ltr">

<head>
	<title> Monde de Char </title>
	<meta charset="utf-8" />
	<link href="css/all.css" rel="stylesheet" />
	<link href="css/clan.css" rel="stylesheet" />
</head>

<body>

<form action="joueur.php" method="GET">
	<input type="text" name="nick" placeholder="Nick, ex: Vassili3" />
	<select name="tier">
		<option value="6"> T6 </option>
		<option value="8"> T8 </option>
		<option value="10"> T10 </option>
	</select>
	<input type="submit" value="Recherche" />
</form>

<?php

function render($playerSkirmishTank) {
	$bonobo = array('M6', 'T-34-85M', 'Cromwell B');
	echo "<table>";
	$tankData = $playerSkirmishTank;
	if( sizeof($tankData) > 0 ) {
		echo "<tr>";
		$playerSkirmishCount = array_reduce($tankData, function($carry, $tank) { return $carry + $tank["battles_count"]; }, 0);
		$sortedTankData = $tankData;
		usort( $sortedTankData , function($tankA, $tankB) { return $tankB["battles_count"] - $tankA["battles_count"]; } );
		printf('<td> %s </td> <th> %s </th> <td> (%d) </td>', '-' /*timestampToDelayString($playerData['player']['last_battle_time'])*/, '-' /*$playerName*/, $playerSkirmishCount) ;
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
	echo "</table>";
}

if( isset($_GET['nick']) and isset($_GET['tier']) ) {
	render(getPlayerSkirmishData($_GET['nick'], intval($_GET['tier'])));
}
?>


</body>
</html>
