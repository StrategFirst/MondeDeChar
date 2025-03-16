<?php

$cid=500149863;

// curl 'https://eu.wargaming.net/clans/wot/search/api/clans/500149863/?game=wot' -H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:128.0) Gecko/20100101 Firefox/128.0' -H 'Accept: application/json, text/javascript, */*; q=0.01' -H 'Accept-Language: fr,fr-FR;q=0.8,en-US;q=0.5,en;q=0.3' -H 'Accept-Encoding: gzip, deflate, br, zstd' -H 'X-CSRFToken: U8jpBVRdh0Xore7Fx0apLfdWmNbRHdjW' -H 'X-Requested-With: XMLHttpRequest' -H 'Connection: keep-alive' -H 'Referer: https://eu.wargaming.net/clans/wot/search/' -H 'Cookie: csrftoken=U8jpBVRdh0Xore7Fx0apLfdWmNbRHdjW; cm.internal.bs_id=14bc3925-80f7-4298-9338-c614c73ed8f1; wgn_realm=eu; OptanonConsent=isGpcEnabled=0&datestamp=Sun+Mar+16+2025+22%3A24%3A03+GMT%2B0100+(heure+normale+d%E2%80%99Europe+centrale)&version=202502.1.0&browserGpcFlag=0&isIABGlobal=false&hosts=&consentId=c855628e-bf2a-4d6b-aa83-a061a5f2ac85&interactionCount=1&isAnonUser=1&landingPath=NotLandingPage&groups=C0001%3A1%2CC0003%3A0%2CC0002%3A0%2CC0004%3A0%2CC0005%3A0&AwaitingReconsent=false; django_language=fr' -H 'Sec-Fetch-Dest: empty' -H 'Sec-Fetch-Mode: cors' -H 'Sec-Fetch-Site: same-origin' -H 'TE: trailers'

$req = curl_init("https://eu.wargaming.net/clans/wot/search/api/clans/$cid/?game=wot");

ob_start();
curl_exec( $req );
$res = json_decode(ob_get_contents());
ob_end_clean();
curl_close( $req );

var_dump( $res->clancard->elo_rating_sh[2]->value );

?>
