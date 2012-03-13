<?php

include 'classes/class.errors.php';
include 'classes/class.configuration.php';

include 'classes/feed_processing/class.sitescraper.php';

echo("Starting scraper");

sitescraper::scrape($argv[1], $argv[2], $argv[3]);
echo("End scraper");