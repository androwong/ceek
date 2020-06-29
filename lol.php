<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$QUERY_STRING = isset($_GET['search']) ? $_GET['search'] : '';
$QUALITY = isset($_GET['quality']) ? $_GET['quality'] : '';
$USER_AGENT = "facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)";
$REFERER_URL = "http://facebook.com";

ini_set("user_agent",$USER_AGENT);


/* FUNCTION TO REQUEST DATA FROM GIVEN URL */
function REQUEST($URL) {

global $USER_AGENT;
global $REFERER_URL;

$CONNECTION = curl_init();
$TIMEOUT = 5;
curl_setopt($CONNECTION, CURLOPT_URL, $URL);
curl_setopt($CONNECTION, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($CONNECTION, CURLOPT_USERAGENT, $USER_AGENT);
curl_setopt($CONNECTION, CURLOPT_REFERER, $REFERER_URL);
curl_setopt($CONNECTION, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($CONNECTION, CURLOPT_CONNECTTIMEOUT, $TIMEOUT);
$RESPONCE = curl_exec($CONNECTION);
curl_close($CONNECTION);

return $RESPONCE;
}

/* CHECK TO MAKE SURE SEARCH QUERY IS NOT EMPTY IF EMPTY FAIL GRACEFULLY */
if ($QUERY_STRING !== ''){

echo $SEARCH_URL = 'https://www.youtube.com/results?search_query='.$QUERY_STRING;
$SEARCH_DATA = REQUEST($SEARCH_URL);var_dump($SEARCH_DATA);die();

/* PHP REGEX TO MATCH ALL LIVE STREAMS FOUND IN SEARCH DATA */
preg_match_all('/data-context-item-id="(.*?)"/',$SEARCH_DATA,$MATCHES, PREG_PATTERN_ORDER);

$VIDEO_ID = $MATCHES[1][0];

/* CHECK TO MAKE SURE WE GOT A VIDEO ID JUST IN CASE AND FAIL GRACEFULLY IF WE DIDNT */
if ($VIDEO_ID !== ''){

$VIDEO_URL = "https://www.youtube.com/watch?v=" . $VIDEO_ID;
$VIDEO_DATA = REQUEST($VIDEO_URL);

/* PHP REGEX TO MATCH ALL M3U STREAMS FOUND IN VIDEO DATA */
$M3U_LINK_REGEX = '/,\\\\"hlsManifestUrl\\\\":\\\\"(.*?)\\\\"/m';

preg_match_all($M3U_LINK_REGEX, $VIDEO_DATA, $M3U_MATCHES, PREG_SET_ORDER, 0);

$DECODED = urldecode($M3U_MATCHES[0][1]);
$M3U = str_replace("\/", "/", $DECODED);

/* CHECK TO MAKE SURE WE GOT A M3U URL JUST IN CASE AND FAIL GRACEFULLY IF WE DIDNT */
if ($M3U !== ''){

$QUALITY_DATA = REQUEST($M3U);

/* QUALITY OPTIONS IN SWITCH CASE TO FIND THE NEEDED QUALITY */
switch ($QUALITY) {
case '96': /* 96 = 1920x1080 */
$QUALITY_REGEX = '/(https:\/.*\/96\/.*index.m3u8)/U';
break;
case '95': /* 95 = 1280x720 */
$QUALITY_REGEX = '/(https:\/.*\/95\/.*index.m3u8)/U';
break;
case '94': /* 94 = 854x480 */
$QUALITY_REGEX = '/(https:\/.*\/94\/.*index.m3u8)/U';
break;
default: /* 93 = 640x360 */
$QUALITY_REGEX = '/(https:\/.*\/93\/.*index.m3u8)/U';
}

/* PHP REGEX TO MATCH ALL M3U QUALITY STREAMS FOUND IN QUALITY DATA */
preg_match_all($QUALITY_REGEX,$QUALITY_DATA,$QUALITY_MATCHES, PREG_PATTERN_ORDER);

$FINAL_LINK = $QUALITY_MATCHES[1][0];

header("Content-type: application/vnd.apple.mpegurl");
header("Location: ".$FINAL_LINK);


}else{

echo "Sorry No M3U url Found.";

}

}else{

echo "Sorry No Video ID Found.";

}

}else{

echo "Sorry No Search Query Found.";

}

?>
