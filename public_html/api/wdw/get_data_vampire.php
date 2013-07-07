<?php

$conn = new Mongo('localhost');
// access database
$mdb = $conn->ubud;
// access collection
$collection = $mdb->ubud_tracking_dep;
$registrations = $mdb->ubud_tracking_reg;

$realtime = date("Y-m-d H:i:s");
$mongotime = New Mongodate(strtotime($realtime));
$emails = explode(",", $_POST['emails']);

print_r($emails);
die();

if($_POST['pwd'] != "kjsdhf898sdhnjkh")
	{
	die();
	}

		
	$ftds = $registrations->find(array('email' => array('$in' => $emails ) ) );
	echo('<table><tr><td>Email</td><td>Date Reg</td><td>GUID</td></tr>');
	foreach($ftds as $ftd)
		{
		echo("<tr><td>" . $ftd['email'] . "</td><td>" . $ftd['date'] . "</td><td>" . $ftd['utm_id'] . "</td></tr>");
		}
	echo("</table>");
	
 
?>