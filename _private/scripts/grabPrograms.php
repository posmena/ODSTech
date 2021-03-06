<?php

/*
 * Load List of Programs that can have feeds
 */
 
include 'classes/class.errors.php';
include 'classes/class.configuration.php';

// network files
include 'classes/feed_processing/class.network.php';
include 'classes/feed_processing/class.network_base.php';
include 'classes/feed_processing/class.network_webgains.php';
include 'classes/feed_processing/class.network_affiliate_window.php';

global $config;
$config = new configuration;
global $db;
$conn = new Mongo('localhost');
$db = $conn->odstech;
 
// This could be neater by loading the 'network' classes, but this will do for now.

// Load Webgains
$webgains = new network_webgains;
$webgains->updateFeedList(2);

// Load Affiliate Window
//$webgains = new network_affiliate_window;
//$webgains->updateFeedList(1);