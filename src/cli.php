<?php

require_once('scrap.php');

if( $argc <= 1 ) {
	exit(63);
} else {
	for($i=1 ; $i<$argc ; $i++) {
		printf( "%18.18s |\n-------------------|\n" , $argv[$i]);
		render(getClanSkirmishData($argv[$i]));
	}
}

function render($clanSkirmishTank) {
	$clr_red = "\033[101;1;30;5m";
	$clr_gld = "\033[103;30m";
	$clr_blu = "\033[94m";
	$clr_rst = "\033[0m";
	$bonobo = array('M6', 'T-34-85M', 'Cromwell B');
	foreach($clanSkirmishTank as $playerName => $playerData) {
		$tankData = $playerData['tank'];
		if( sizeof($tankData) > 0 ) {
			$playerSkirmishCount = array_reduce($tankData, function($carry, $tank) {
				return $carry + $tank["battles_count"]; }, 0);
			$sortedTankData = $tankData;
			usort( $sortedTankData , function($tankA, $tankB) { return $tankB["battles_count"] - $tankA["battles_count"]; } );
			echo sprintf(' %12.12s (%4d)|', $playerName, $playerSkirmishCount) ;
			for($i = 0; $i < min(sizeof($sortedTankData), 8) ; $i++) {
				$tank = $sortedTankData[$i];
				$needReset = true;
				if( in_array( $tank["name"], $bonobo ) ) {
					echo $clr_red;
				} else if( $tank["premium"] == 1 ) {
					echo $clr_gld;
					} else if ( $tank["collector_vehicle"] == 1 ) {
					echo $clr_blu;
				} else {
					$needReset = false;
				}
				echo sprintf(" %12.12s ", $tank["name"]);
				echo sprintf(" %3d%% ", 100 * $tank["battles_count"] / $playerSkirmishCount);
				if( $needReset ) {
					echo $clr_rst;
				}
				echo "|";
			}
			echo "\n";
		}
	}

	echo "\n";
}


?>
