<?php

include 'classes/feed_processing/class.feed_processor.php';
include 'classes/class.errors.php';
include 'classes/class.config.php';
include 'classes/class.database.php';

// network files
include 'classes/feed_processing/class.network.php';
include 'classes/feed_processing/class.network_base.php';
include 'classes/feed_processing/class.network_webgains.php';
include 'classes/feed_processing/class.network_affiliate_window.php';


class ODSTech_FeedManager extends feed_processor
{
	public function process($feed_id = null) {
		global $db;
		
		if ($feed_id === null || $feed_id < 1 || ctype_digit($feed_id) === false) {
			throw new Exception ('Usage: processFeed.php feed_id');
		}
		
		self::process_feed($feed_id);
	}
	
	public function add_feed($feed_id = null) {
		global $db;
		
		if ($feed_id === null || $feed_id < 1 || ctype_digit($feed_id) === false) {
			throw new Exception ('Usage: addFeed.php feed_id');
		}
		
		$sql    = 'SELECT p.*, n.class_name FROM pm_programs p INNER JOIN pm_networks n ON n.id=p.network_id WHERE p.id='.$feed_id;
		
		$result  = $db->getQuery($sql);
		$feed    = (object) $result[0];
		$network = new $feed->class_name;
		
		$network->addFeed($feed);
	}
}