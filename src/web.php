<!DOCTYPE html>
<html>
<head></head>
<body>
<form action="web.php" method="GET">
<input type="text" name="tag" placeholder="Clan TAG" value="VASS" />
<input type="button" value="Search !" />
</form>

<hr/>

<?php

require_once('scrap.php');

function render($clanSkirmishTank) {
	$clr_red = "\033[101;1;30;5m";
	$clr_gld = "\033[103;30m";
	$clr_blu = "\033[94m";
	$clr_rst = "\033[0m";
	$bonobo = ['M6', 'T-34-85M', 'Cromwell B'];
	echo "<table>";
	foreach($clanSkirmishTank as $playerName => $tankData) {
		if( sizeof($tankData) > 0 ) {
			echo "<tr>";
			$playerSkirmishCount = array_reduce($tankData, function($carry, $tank) {
				return $carry + $tank["battles_count"]; }, 0);
			$sortedTankData = $tankData;
			usort( $sortedTankData , function($tankA, $tankB) { return $tankB["battles_count"] - $tankA["battles_count"]; } );
			echo sprintf('<th> %s (%d) </th>', $playerName, $playerSkirmishCount) ;
			for($i = 0; $i < min(sizeof($sortedTankData), 8) ; $i++) {
				$tank = $sortedTankData[$i];
				echo "<td>";
				if( in_array( $tank["name"], $bonobo ) ) {
					echo '<b style="color:red;">';
				} else if( $tank["premium"] == 1 ) {
					echo '<b style="color:gold;">';
				} else if ( $tank["collector_vehicle"] == 1 ) {
					echo '<i style="color:blue;">';
				}
				echo sprintf(" %s ", $tank["name"]);
				echo sprintf(" %3d%% ", 100 * $tank["battles_count"] / $playerSkirmishCount);
				if( in_array( $tank["name"], $bonobo ) ) {
					echo "</b>";
				} else if( $tank["premium"] == 1 ) {
					echo "</b>";
				} else if ( $tank["collector_vehicle"] == 1 ) {
					echo "</i>";
				}
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
</body>
</html>
