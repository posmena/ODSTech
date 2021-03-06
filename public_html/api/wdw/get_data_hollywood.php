<?php

$conn = new Mongo('localhost');
// access database
$mdb = $conn->ubud;
// access collection
$collection = $mdb->ubud_tracking_deposit;
$registrations = $mdb->ubud_tracking_reg;


$realtime = date("Y-m-d H:i:s");
$mongotime = New Mongodate(strtotime($realtime));

$rows = $collection->find(array('processed' => array('$exists' => false), 'site' => 'HOLLYWOOD'))->limit(500);
$rows->timeout(100000);

try
{
foreach($rows as $row)
	{
	$row['processed'] = true;
	$collection->save($row);
	
	$regs = $registrations->find(array('customer' => $row['customer']));
	$regs->timeout(100000);
	
	if( $regs->count() == 0 )
		{
		// not been added yet so add now
		$registrations->insert(array( 'customer' => $row['customer'], 'site' => 'HOLLYWOOD', 'guid' => $row['guid'], 'date' => $row['date']));
		}

//	$regs = $registrations->find(array('customer' => $row['customer']));
		
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
} 
catch(Exception $e) 
{
}
	
	$ftds = $registrations->find(array('ftds' => array('$gte' => 1 ), 'guid' => array('$ne' => null), 'site' => 'HOLLYWOOD' ) )->sort( array( 'date' => -1 ) );
	$ftds->timeout(100000);
	echo('<table><td>Date</td><td>GUID</td></tr>');
	foreach($ftds as $ftd)
		{
		echo("<tr><td>" . date('Y-m-d H:i:s', $ftd['date']->sec) . "</td><td>" . $ftd['guid'] . "</td></tr>");
		}
	echo("</table>");

 
?>