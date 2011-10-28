<?php

include 'classes/feed_processing/class.cron_feed_manager.php';

if (true === in_array('verbose', $argv)) {
	define('VERBOSE', true);
}

$feed_id = 0;
if (false === array_key_exists(1, $argv)) {
	if (defined('VERBOSE')) {
		print "Must provide feed id";	
	}
	
	exit;
}

$feed_id = $argv[1];
$full    = false;
if (true === in_array('full', $argv)) {
	if (defined('VERBOSE')) {
		print "WARNING: Dropping Entire " . $feed_id . " Table.\n";
	}
	$full = true;
}

if (defined('VERBOSE')) {
	print 'Feed ID: ' . $feed_id."\n";
	print 'Full?';
	var_dump($full);
}
$conn = new Mongo('localhost');
// access database

try {
	$db = $conn->odstech;
	ODSTech_FeedManager::process($feed_id, $full);
} catch (Exception $ex) {
	print $ex->getMessage()."\n";
}
//$db->disconnect();