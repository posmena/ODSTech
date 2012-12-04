<?php
$conn = new Mongo('localhost');
// access database
$mdb = $conn->ubud;
// access collection
$collection = $mdb->registrations;

$realtime = date("Y-m-d H:i:s");
$mongotime = New Mongodate(strtotime($realtime));

$obj = array('date' => $mongotime, 'alias' => $_GET['alias'],'email' => $_GET['email'],'fname' => $_GET['fname'],'lname' => $_GET['lname'], 'site' => $_GET['site'], 'utm_source' => $_GET['utm_source'], 'utm_medium' => $_GET['utm_medium'], 'utm_term' => $_GET['utm_term'], 'utm_content' => $_GET['utm_content'], 'utm_id' => $_GET['utm_id']);
$collection->save($obj);
					
header( 'Location: blank.gif' ) ;
?>