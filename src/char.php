<!DOCTYPE html>
<html lang="fr-FR" dir="ltr">

<head>
	<title> Monde de Char </title>
	<meta charset="utf-8" />
	<link href="css/all.css" rel="stylesheet" />
	<link href="css/char.css" rel="stylesheet" />
</head>

<body>
    <form action="char.php" method="GET">
        <select name="tier">
            <option value="1"> T1 </option>
            <option value="2"> T2 </option>
            <option value="3"> T3 </option>
            <option value="4"> T4 </option>
            <option value="5"> T5 </option>
            <option value="6"> T6 </option>
            <option value="7"> T7 </option>
            <option value="8"> T8 </option>
            <option value="9"> T9 </option>
            <option value="10"> T10 </option>
		</select>
        <input type="submit" value="Recherche" />
    </form>

    <main>
<?php

include "env.php";

function render($tier) {
    global $WARGAMING;
    $url = "https://api.worldoftanks.eu" .
            "/wot/encyclopedia/vehicles/" .
            "?application_id=$WARGAMING" .
            "&fields=is_premium%2Cshort_name%2Cimages.small_icon" .
            "&language=fr" .
            "&tier=$tier" ;
    $req = curl_init($url);
    ob_start();
    curl_exec($req);
    if( curl_error($req) ) {
        $errorMessage = curl_error($req);
        ob_end_clean();
        curl_close($req);
        return;
    }
    if( curl_getinfo($req, CURLINFO_HTTP_CODE) != 200 ) {
        $exitCode = curl_getinfo($req, CURLINFO_HTTP_CODE);
        ob_end_clean();
        curl_close($req);
        return;
    }
    $res = json_decode(ob_get_contents());
    ob_end_clean();
    curl_close($req);
    if( ! isset($res->status) ) {
        return;
    }
    if( $res->status != 'ok') {
        return;
    }
    foreach($res->data as $char) {

        echo '<article' . (($char->is_premium) ? ' class="prem"' : '') .'>';
        echo '<img src="' . ($char->images->small_icon) . '" width="124" height="31" loading="lazy"/>';
        echo ('<span>' . ($char->short_name) . '</span>');
        echo '</article>';
    }
}

if( isset($_GET['tier']) ) {
    render($_GET['tier']);
}
?>
</main>
</body>

</html>