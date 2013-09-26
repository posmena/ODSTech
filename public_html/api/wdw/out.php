<?php

//drop coookie and redirect

$guid = $_GET['guid'];
$url = $_GET['p'];

setcookie("bnb_guid", $guid, time() + 86400 * 365, "/", "odst.co.uk" );
setcookie("wdw_guid", $guid, time() + 86400 * 365, "/", "odst.co.uk" );

header( "Location: $url" ) ;
?>