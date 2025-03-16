<?php

include "env.php";

class WargamingException extends Exception {};
class InstallationException extends Exception {};

/** PUBLIC API
 * Search a clan and get it's id based on it's tag
 *
 * Return the id of the clan with the closest match based on the given tag
 *
 * @param string $clanTag
 * @return int | NULL
 */
function getClanID($clanTag) {
	// offset and limit are set to find the first match, timeframe is mandatory, battle type is also mandatory
	// and we choose random game as all players must have at least one of those.
	$req = curl_init("https://eu.wargaming.net/clans/wot/search/api/clans/?search=$clanTag&offset=0&limit=1&timeframe=all&battle_type=random");

	// capture of stdout is required for curl
	ob_start();
	curl_exec($req);
	if( curl_error($req) ) {
		$errorMessage = curl_error($req);
		ob_end_clean();
		curl_close($req);
		throw new InstallationException($errorMessage);
	}
	if( curl_getinfo($req, CURLINFO_HTTP_CODE) != 200 ) {
		$exitCode = curl_getinfo($req, CURLINFO_HTTP_CODE);
		ob_end_clean();
		curl_close($req);
		throw new WargamingException($exitCode);
	}
	$res = json_decode(ob_get_contents());
	ob_end_clean();
	curl_close($req);

	// In this API entry point there's no status, however all query's aren't sure to find an answer
	if( ! isset($res->_meta_->total) ) {
		throw new WargamingException();
	}
	if( $res->_meta_->total != 1 ) {
		return NULL;
	}
	if( ! isset($res->clans[0]->id) ) {
		throw new WargamingException();
	}
	return $res->clans[0]->id;
}

/** PUBLIC API
 * Search a player and get it's id based on it's nickname
 *
 * Return the id of the player with the closest match based on the given nick
 *
 * @param string $playerNick
 * @return int | NULL
 */
function getPlayerID($playerNick) {
	// offset and limit are set to find the first match, timeframe is mandatory, battle type is also mandatory
	// and we choose random game as all players must have at least one of those.
	$req = curl_init("https://worldoftanks.eu/fr/community/accounts/search/?name=$playerNick&name_gt=");
	
	// the following http headers are mandatory here, are the API will refuse us
	curl_setopt($req, CURLOPT_HTTPHEADER, array(
		'Accept: application/json; q=0.01',
		'X-Requested-With: XMLHttpRequest'));

	// capture of stdout is required for curl
	ob_start();
	curl_exec($req);
	if( curl_error($req) ) {
		$errorMessage = curl_error($req);
		ob_end_clean();
		curl_close($req);
		throw new InstallationException($errorMessage);
	}
	if( curl_getinfo($req, CURLINFO_HTTP_CODE) != 200 ) {
		$exitCode = curl_getinfo($req, CURLINFO_HTTP_CODE);
		ob_end_clean();
		curl_close($req);
		throw new WargamingException($exitCode);
	}
	$res = json_decode(ob_get_contents());
	ob_end_clean();
	curl_close($req);

	// In this API entry point there's no status, however all query's aren't sure to find an answer
	if( sizeof($res->response) == 0 ) {
		return NULL;
	}
	if( ! isset($res->response[0]->account_id) ) {
		throw new WargamingException();
	}
	return intval($res->response[0]->account_id);
}

/** PUBLIC API
 * Query all player of the clan
 *
 * For a given clan id return the first 25 users informations, the content isn't managed and could evolve, for any structure dump the output
 *
 * @param int $clanId
 * @return array Full type : array[ int<index> => StdClass( string<key> => mixed<informations> ) ]
 */
function getClanPlayers($clanId) {
	// offset, limit, order, timeframe, battle_type are all mandatory values, as for now some temporary values are put
	// they are inspired from what the website is using by default, this will evolve in the future
	$req = curl_init("https://eu.wargaming.net/clans/wot/$clanId/api/players/?offset=0&limit=25&order=-role&timeframe=all&battle_type=default");
	// the following http headers are mandatory here, are the API will refuse us
	curl_setopt($req, CURLOPT_HTTPHEADER, array(
		'Accept: application/json; q=0.01',
		'X-Requested-With: XMLHttpRequest'));

	// capture required for curl
	ob_start();
	curl_exec($req);
	if( curl_error($req) ) {
		$errorMessage = curl_error($req);
		ob_end_clean();
		curl_close($req);
		throw new InstallationException($errorMessage);
	}
	if( curl_getinfo($req, CURLINFO_HTTP_CODE) != 200 ) {
		$exitCode = curl_getinfo($req, CURLINFO_HTTP_CODE);
		ob_end_clean();
		curl_close($req);
		throw new WargamingException($exitCode);
	}

	$res = json_decode(ob_get_contents());

	ob_end_clean();
	curl_close($req);
	if( $res->status != "ok" ) {
		throw new WargamingException($exitCode);
	}
	if( ! isset($res->items) ) {
		throw new WargamingException($exitCode);
	}
	return $res->items;
}

/** PUBLIC API
 * Query all tank used in skirmish by a given player
 *
 * By giving a player id, this retrieve all tanks used by the player in skirmish in Tier 6, content information may vary !
 *
 * @param int $playerId
 * @param int $skirmishTier
 * @return array Full type : array[ int<index> => array[ string<key> => mixed<statistics> ]<tanks> ]
 */
function getPlayerSkirmishTank($playerId, $skirmishTier) {
	$req = curl_init("https://worldoftanks.eu/wotup/profile/vehicles/list/");
	curl_setopt($req, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($req, CURLOPT_POSTFIELDS, json_encode(array(
		"battle_type" => "fort_sorties",
                "only_in_garage" => true,
                "spa_id" => $playerId,
		"premium" => array(0,1),
		"collector_vehicle" => array(0,1),
		"nation" => array(), "role" => array(), "type" => array(),
		"tier" => array($skirmishTier),
		"language" => "fr" ) ) );
	// The following HTTP headers are mandatory otherwise the API will deny our request
	curl_setopt($req, CURLOPT_HTTPHEADER, array(
		'Accept: application/json; q=0.01',
		'X-Requested-With: XMLHttpRequest') );

	// Capture of stdout require, due to the wat curl is working
	ob_start();
	curl_exec($req);
	if( curl_error($req) ) {
		$errorMessage = curl_error($req);
		ob_end_clean();
		curl_close($req);
		throw new InstallationException($errorMessage);
	}
	if( curl_getinfo($req, CURLINFO_HTTP_CODE) != 200 ) {
		$exitCode = curl_getinfo($req, CURLINFO_HTTP_CODE);
		ob_end_clean();
		curl_close($req);
		throw new WargamingException($exitCode);
	}

	$res = json_decode(ob_get_contents());
	ob_end_clean();

	curl_close($req);
	if( $res->status != "ok" ) {
		throw new WargamingException();
	}
	// Data re-mapping required for easier manipulation later on
	if( !isset($res->data->data)) {
		throw new WargamingException();
	}
	if( !isset($res->data->parameters)) {
		throw new WargamingException();
	}
	$tankDataList = $res->data->data;
	$tankDataKeys = $res->data->parameters;
	$tankDataMapList = array_map( function($tankData) use($tankDataKeys) { return array_combine($tankDataKeys, $tankData); } , $tankDataList );

	return $tankDataMapList;
}

/** PUBLIC & PRIVATE API
 * Query the API for every tank of the clan in Skirmish T6
 *
 * @param string $clanTag
 * @return array Full type : array[ string<playerName> => array<tank>[ int<index> => array<information>[ string<key> => mixed<statistics> ] ] ]
 */
function getClanSkirmishData($clanTag, $skirmishTier) {
	$clanId = getClanId($clanTag);
	$clanPlayers = getClanPlayers($clanId);
	$clanSkirmishTank = array_combine(
		array_map( function($player) { return $player->name; } , $clanPlayers ),
		array_map( function($player) use ($skirmishTier) { global $WARGAMING;
			return array(
				"tank" => getPlayerSkirmishTank($player->id, $skirmishTier),
				"player" => array(
					"last_battle_time" => getPlayerLastBattleTimestamp( $WARGAMING, $player->id),
					"rank" => NULL
				)
			); } , $clanPlayers )
	);
	return $clanSkirmishTank;
}

/** PUBLIC & PRIVATE API
 * Query the API for every tank of the clan in Skirmish T6
 *
 * @param string $clanTag
 * @return array Full type : array[ string<playerName> => array<tank>[ int<index> => array<information>[ string<key> => mixed<statistics> ] ] ]
 */
function getPlayerSkirmishData($playerNick, $skirmishTier) {
	$playerId = getPlayerId($playerNick);
	$playerTank = getPlayerSkirmishTank($playerId, $skirmishTier);
	return $playerTank;
}



/** PRIVATE API
 * Query the API for last battle time no matter game mode
 *
 * @param string $application_id
 * @param string $account_id
 * @return int Unix timestamp
 */
function getPlayerLastBattleTimestamp($application_id, $account_id) {
	$req = curl_init("https://api.worldoftanks.eu/wot/account/info/?application_id=$application_id&fields=last_battle_time&account_id=$account_id");
	// The following HTTP headers aren't mandatory, the API is just faster with them
	curl_setopt($req, CURLOPT_HTTPHEADER, array(
		'Accept: application/json; q=0.01',
		'X-Requested-With: XMLHttpRequest') );

	// Capture of stdout require, due to the wat curl is working
	ob_start();
	curl_exec($req);
	if( curl_error($req) ) {
		$errorMessage = curl_error($req);
		ob_end_clean();
		curl_close($req);
		throw new InstallationException($errorMessage);
	}
	if( curl_getinfo($req, CURLINFO_HTTP_CODE) != 200 ) {
		$exitCode = curl_getinfo($req, CURLINFO_HTTP_CODE);
		ob_end_clean();
		curl_close($req);
		throw new WargamingException($exitCode);
	}

	$res = json_decode(ob_get_contents());
	ob_end_clean();
	curl_close($req);
	if( $res->status != "ok" ) {
		throw new WargamingException();
	}
	if( !isset($res->data->$account_id->last_battle_time)) {
		throw new WargamingException();
	}

	return $res->data->$account_id->last_battle_time;
}

/** UTILITY
 * Convert a timestamp to a message with the time delay in french.
 *
 * @param int $timestamp
 * @return string
 */
function timestampToDelayString($timestamp) {
	$seconds_ago = time() - $timestamp;
	if ($seconds_ago >= 31536000) {
		return "Il y a " . intval($seconds_ago / 31536000) . " an(s).";
	} elseif ($seconds_ago >= 2419200) {
		return "Il y a " . intval($seconds_ago / 2419200) . " mois.";
	} elseif ($seconds_ago >= 86400) {
		return "Il y a " . intval($seconds_ago / 86400) . " jour(s).";
	} elseif ($seconds_ago >= 3600) {
		return "Il y a " . intval($seconds_ago / 3600) . " heure(s).";
	} elseif ($seconds_ago >= 120) {
		return "Il y a " . intval($seconds_ago / 60) . " minute(s).";
	} elseif ($seconds_ago >= 60) {
		return "Il y a une minute.";
	} else {
		return "Il y a moins d'une minute.";
	}
}
?>
