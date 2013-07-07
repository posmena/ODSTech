<?php

$conn = new Mongo('localhost');
// access database
$mdb = $conn->ubud;
// access collection
$registrations = $mdb->registrations;

$realtime = date("Y-m-d H:i:s");
$mongotime = New Mongodate(strtotime($realtime));
$emails = explode(",", $_POST['emails']);


if($_POST['pwd'] != "kjsdhf898sdhnjkh")
	{
	die();
	}

		
	$ftds = $registrations->find(array('email' => array('$in' => $emails ) , 'guid' => array( '$ne' => null )  ) );
	echo('<table><td>GUID</td></tr>');
	foreach($ftds as $ftd)
		{
		echo("<tr><td>" . $ftd['utm_id'] . "</td></tr>");
		}
	echo("</table>");
	
 
?>