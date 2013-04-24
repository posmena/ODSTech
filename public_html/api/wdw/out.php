<?php

//drop coookie and redirect

$guid = $_GET['guid'];
$url = $_GET['p'];

setcookie("bnb_guid", $guid,365);

header( "Location: $p" ) ;
?>