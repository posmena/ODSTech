<?php

$conn = new Mongo('localhost');
// access database
$mdb = $conn->ubud;
// access collection
$collection = $mdb->ubud_tracking_reg;

$realtime = date("Y-m-d H:i:s");
$mongotime = New Mongodate(strtotime($realtime));

$guid = $_COOKIE['bnb_guid'];

$collection->insert(array('IP' => $_SERVER['REMOTE_ADDR'], 'date' => $mongotime, 'guid' => $guid, 'site' => $_GET['site'], 'customer' => $_GET['id']));

header( 'Location: blank.gif' ) ;
 
?>