<?php
include 'classes/feed_processing/class.cron_feed_manager.php';

die()

$conn = new Mongo('localhost');
$db = $conn->odstech;
$feeds = $db->ot_feeds;

$feed = $feeds->findOne(array('client' => 'kelkoo'));
$conn_id = ftp_connect($feed['url']);
$login_result = ftp_login($conn_id, $feed['username'], $feed['password']);

$files = ftp_rawlist($conn_id, '-1t');

//$files = array_reverse($files);

foreach($files as $file)
{
if( strpos($file, 'efmaster') !== FALSE )
	{
	$feed['filename'] = $file;
	$feeds->save($feed);
	try
	{
	ODSTech_FeedManager::process('kelkoo');
	echo("Processed" . $file . "\r\n");
	}
	catch(Exception $e)
	{
	echo("Error processing" . $file . "\r\n". $e . "\r\n");
	}
	ftp_delete($conn_id, $file);
	}
}

?>