<?php

include 'classes/feed_processing/class.feed_processor.php';
include 'classes/class.errors.php';
include 'classes/class.configuration.php';
include 'classes/class.database.php';

// network files
include 'classes/feed_processing/class.network.php';
include 'classes/feed_processing/class.network_base.php';
include 'classes/feed_processing/class.network_webgains.php';
//include 'classes/feed_processing/class.network_affiliate_window.php';

// custom feeds
include 'classes/feed_processing/class.custom_easyjet_feed.php';
include 'classes/feed_processing/class.custom_forthillhome_feed.php';
include 'classes/feed_processing/class.custom_jtspas_feed.php';
include 'classes/feed_processing/class.custom_easylife_feed.php';
include 'classes/feed_processing/class.custom_octopus_feed.php';
include 'classes/feed_processing/class.webgains_feed.php';
include 'classes/feed_processing/class.goldenfeeds_feed.php';

class ODSTech_FeedManager extends feed_processor
{
	public function process($feed_id = null, $full = false, $search = null) {
		global $db;
		
		//if ($feed_id === null || $feed_id < 1 || ctype_digit($feed_id) === false) {
		//	throw new Exception ('Usage: processFeed.php feed_id full verbose');
		//}
		return self::process_feed($feed_id, $full, $search);
	}
	
	public function pre_process($feed_id = null, $full = false) {
		global $db;
		
	
		return self::pre_process_feed($feed_id, $full);
	}
	
	public function post_process($feed_id = null, $full = false) {
		global $db;
		
	
		return self::post_process_feed($feed_id, $full);
	}

}