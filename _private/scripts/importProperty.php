<?php
// dirty hacky scrpt just to import the Property table into Mongo. Probably a use once and discard script.

include 'classes/feed_processing/class.cron_feed_manager.php';

$db = new database;
$db->connection('odstech');

$conn = new Mongo('localhost');
$mdb = $conn->odstech;
$collection = $mdb->properties_raw;
$collection->drop();

$sql = 'SELECT * FROM Property LIMIT 0,1';
$otFieldData = $db->getFields($sql);

$sql2 =  'SELECT * FROM Property';
$result = $db->getQuery($sql2);
$db->disconnect();

foreach ($result as $row) {
	echo 'foo';
	$item = array();
	foreach($otFieldData as $extra_field) {
		$item[strtolower($extra_field)] = $row[$extra_field];
	}

	$collection->save($item);
}
	
