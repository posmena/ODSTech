<?php
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

$collection->insert(array('date' => mongotime, 'alias' => $_GET['alias'], 'amount' => $amt, 'old_balance' => $startBal, 'new_balance' => $newBal, 'site' => $_GET['site']));

header( 'Location: blank.gif' ) ;
 
?>