<?php

include 'classes/feed_processing/class.cron_feed_manager.php';

$feed_id = 0;
if (array_key_exists(1, $argv)) {
        $feed_id = $argv[1];
}

$db = new database;
try {
    $db->connection('odstech');
    ODSTech_FeedManager::add_feed($feed_id);

} catch (Exception $ex) {
    print $ex->getMessage()."\n";
}
$db->disconnect();