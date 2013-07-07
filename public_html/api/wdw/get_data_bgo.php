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
	
	$regs = $registrations->find(array('customer' => $row['customer'], 'guid' => array('$ne') ));
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
			$reg['last_ftd'] = $reg['date'] + 1;
			}
		
		$registrations->save($reg);
		
		}
	}
	
	
	$ftds = $registrations->find(array('ftds' => array('$gte' => 1 ) ) );
	echo('<table><tr><td>Date FTD</td><td>Date Reg</td><td>GUID</td></tr>');
	foreach($ftds as $ftd)
		{
		echo("<tr><td>" . $ftd['date'] . "</td><td>" . $ftd['ftd'] . "</td><td>" . $ftd['guid'] . "</td></tr>");
		}
	echo("</table>");

 
?>