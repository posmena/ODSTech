<?php

$conn = new Mongo('localhost');
// access database
$mdb = $conn->ubud;
// access collection
$collection = $mdb->ubud_tracking_deposit;
$registrations = $mdb->ubud_tracking_reg;


$realtime = date("Y-m-d H:i:s");
$mongotime = New Mongodate(strtotime($realtime));

$rows = $collection->find(array('processed' => array('$exists' => false)));

foreach($rows as $row)
	{
	$row['processed'] = true;
	$collection->save($row);
	
	$regs = $registrations->find(array('customer' => $row['customer']));
	foreach($regs as $reg)
		{
		if( !isset($reg['ftd']) )
			{
			$reg['ftd'] = $row['date'];
			$reg['ftds'] = 1;
			}
		else
			{
			$reg['ftds'] = $reg['ftds'] + 1;
			$reg['last_ftd'] = $row['date'];
			}
		
		$registrations->save($reg);
		
		}
	}
	
	
	$ftds = $registrations->find(array('ftds' => array('$gte' => 1 ), array('guid' => array('$exists' => true) )  ) );
	echo('<table><td>GUID</td></tr>');
	foreach($ftds as $ftd)
		{
		echo("<tr><td>" . $ftd['guid'] . "</td></tr>");
		}
	echo("</table>");

 
?>