<?php
$conn = new Mongo('localhost');
// access database
$mdb = $conn->ubud;
// access collection
$collection = $mdb->duplicates;

$realtime = date("Y-m-d H:i:s");
$mongotime = New Mongodate(strtotime($realtime));

$obj = array('Agent' => $_SERVER['HTTP_USER_AGENT'], 'IP' => $_SERVER['REMOTE_ADDR'], 'date' => $mongotime, 'email' => $_GET['email'], 'alias' => $_GET['alias'], 'guid' => $_GET['guid'], 'site' => $_GET['site'], 'utm_source' => $_GET['utm_source'], 'utm_medium' => $_GET['utm_medium'], 'utm_term' => $_GET['utm_term'], 'utm_content' => $_GET['utm_content'], 'utm_id' => $_GET['utm_id']);
$collection->save($obj);
					
header( 'Location: blank.gif' ) ;
?>