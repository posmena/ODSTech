<?php

// script to do anything that needs to be run after all feeds have been generated

$conn = new Mongo('localhost');
$mdb  = $conn->odstech;

$active_set = $mdb->settings->findOne(array('_id' => 'active_set'));

switch ((int) $active_set['val']) {
	case 1:
		echo 'Current Set 1 = changing to 2'."\n";
		$mdb->settings->save(array('_id' => 'active_set', 'val' => 2));
		break;
	case 2:
		echo 'Current Set 2 = changing to 1'."\n";
		$mdb->settings->save(array('_id' => 'active_set', 'val' => 1));
		break;
}