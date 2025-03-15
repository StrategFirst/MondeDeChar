<?php


function getClanID($clanTag) {
	ob_start();
	$req = curl_init("https://eu.wargaming.net/clans/wot/search/api/clans/?search=$clanTag&offset=0&limit=1&timeframe=all&battle_type=random");
	curl_exec($req);
	if( curl_error($req) ) {
		ob_end_clean();
		exit(1);
	}
	if( curl_getinfo($req, CURLINFO_HTTP_CODE) != 200 ) {
		ob_end_clean();
		exit(2);
	}
	$res = json_decode(ob_get_contents());
	ob_end_clean();
	curl_close($req);
	if( $res->_meta_->total != 1 ) {
		exit(3);
	}
	return $res->clans[0]->id;
}

function getClanPlayers($clanId) {
	ob_start();
	$req = curl_init("https://eu.wargaming.net/clans/wot/$clanId/api/players/?offset=0&limit=25&order=-role&timeframe=all&battle_type=default");
	curl_setopt($req, CURLOPT_HTTPHEADER, [
		'Accept: application/json; q=0.01',
		'X-Requested-With: XMLHttpRequest' ] );
	curl_exec($req);
	if( curl_error($req) ) {
		ob_end_clean();
		exit(4);
	}
	if( curl_getinfo($req, CURLINFO_HTTP_CODE) != 200 ) {
		ob_end_clean();
		exit(5);
	}
	$res = json_decode(ob_get_contents());
	ob_end_clean();
	curl_close($req);
	if( $res->status != "ok" ) {
		exit(6);
	}
	return $res->items;
}

function getPlayerSkirmishTank($playerId) {
	$req = curl_init("https://worldoftanks.eu/wotup/profile/vehicles/list/");
	curl_setopt($req, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($req, CURLOPT_POSTFIELDS, json_encode( [
		"battle_type" => "fort_sorties",
                "only_in_garage" => true,
                "spa_id" => $playerId,
		"premium" => [0,1],
		"collector_vehicle" => [0,1],
		"nation" => [], "role" => [], "type" => [],
		"tier" => [6],
		"language" => "fr" ] ) );
	curl_setopt($req, CURLOPT_HTTPHEADER, [
		'Accept: application/json; q=0.01',
		'X-Requested-With: XMLHttpRequest' ] );
	ob_start();
	curl_exec($req);
	if( curl_error($req) ) {
		ob_end_clean();
		exit(7);
	}
	if( curl_getinfo($req, CURLINFO_HTTP_CODE) != 200 ) {
		ob_end_clean();
		exit(8);
	}
	$res = json_decode(ob_get_contents());
	ob_end_clean();
	curl_close($req);
	if( $res->status != "ok" ) {
		exit(9);
	}
	// Data re-mapping required for easier manipulation later on
	$tankDataList = $res->data->data;
	$tankDataKeys = $res->data->parameters;
	$tankDataMapList = array_map( function($tankData) use($tankDataKeys) { return array_combine($tankDataKeys, $tankData); } , $tankDataList );

	return $tankDataMapList;
}

function getClanSkirmishData($clanTag) {
	$clanId = getClanId($clanTag);
	$clanPlayers = getClanPlayers($clanId);
	$clanSkirmishTank = array_combine(
		array_map( function($player) { return $player->name; } , $clanPlayers ),
		array_map( function($player) { return getPlayerSkirmishTank($player->id); } , $clanPlayers )
	);
	return $clanSkirmishTank;
}

?>
