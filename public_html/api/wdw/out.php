<?php

//drop coookie and redirect

$guid = $_GET['guid'];
$url = $_GET['p'];

setcookie("bnb_guid", $guid, time() + 86400 * 365 );

header( "Location: $url" ) ;
?>