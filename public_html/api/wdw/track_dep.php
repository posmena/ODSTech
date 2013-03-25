<?php
/*
$conn = new Mongo('localhost');
// access database
$mdb = $conn->ubud;
// access collection
$collection = $mdb->deposits;

$realtime = date("Y-m-d H:i:s");
$mongotime = New Mongodate(strtotime($realtime));

$startBal =  htmlentities(utf8_decode ($_GET['oldbal']));
$newBal =  htmlentities(utf8_decode ($_GET['newbal']));

$amt = str_replace('&pound;','',$newBal) - str_replace('&pound;','',$startBal);

$collection->insert(array('Agent' => $_SERVER['HTTP_USER_AGENT'], 'IP' => $_SERVER['REMOTE_ADDR'], 'date' => $mongotime, 'alias' => $_GET['alias'], 'guid' => $_GET['guid'], 'amount' => $amt, 'old_balance' => $startBal, 'new_balance' => $newBal, 'site' => $_GET['site'], 'utm_source' => $_GET['utm_source'], 'utm_medium' => $_GET['utm_medium'], 'utm_term' => $_GET['utm_term'], 'utm_content' => $_GET['utm_content'], 'utm_id' => $_GET['utm_id']));
*/
header( 'Location: blank.gif' ) ;
 
?>