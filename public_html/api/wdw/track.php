<?php
$conn = new Mongo('localhost');
// access database
$mdb = $conn->ubud;
// access collection
$collection = $mdb->tracking;

$realtime = date("Y-m-d H:i:s");
$mongotime = New Mongodate(strtotime($realtime));

$obj = array('page' => $_GET['page'], 'Agent' => $_SERVER['HTTP_USER_AGENT'], 'IP' => $_SERVER['REMOTE_ADDR'], 'date' => $mongotime, 'alias' => $_GET['alias'], 'guid' => $_GET['guid'],'email' => $_GET['email'],'fname' => $_GET['fname'],'lname' => $_GET['lname'], 'site' => $_GET['site'], 'utm_source' => $_GET['utm_source'], 'utm_medium' => $_GET['utm_medium'], 'utm_term' => $_GET['utm_term'], 'utm_content' => $_GET['utm_content'], 'utm_id' => $_GET['utm_id']);
$collection->save($obj);
					
header( 'Location: blank.gif' ) ;
?>