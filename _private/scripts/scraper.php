<?php

include 'classes/class.errors.php';
include 'classes/class.configuration.php';
include 'classes/class.database.php';

include 'classes/feed_processing/class.sitescraper.php';

$db = new database;
$db->connection('odstech');

sitescraper::scrape($argv[1], $argv[2], $argv[3]);