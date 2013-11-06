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

		
	$ftds = $registrations->find(array('email' => array('$in' => $emails ), 'site' => 'Booty' ) );
	echo('<table><td>utm_source</td><td>utm_medium</td><td>utm_term</td><td>utm_content</td><td>match_type</td></tr>');
	foreach($ftds as $ftd)
		{
		if($ftd['utm_medium'] == "cpc" )
			{
			echo("<tr><td>" . $ftd['utm_source'] . "</td><td>" . $ftd['utm_medium'] . "</td><td>" . $ftd['utm_term'] . "</td><td>" . $ftd['utm_content'] . "</td><td>" . $ftd['matchtype'] . "</td></tr>");
			}
	}
	echo("</table>");
	
 
?>