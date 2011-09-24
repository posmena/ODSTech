<?php

include 'classes/class.configuration.php';
include 'classes/feed_processing/class.xmlgenerator.php';

if (array_key_exists(1, $argv)) {
	$feed_name = $argv[1];
}

if (array_key_exists(2, $argv)) {
	$filter_type = $argv[2];
}

try {
	ODSTech_xmlgenerator::generate($feed_name, $filter_type);
} catch (Exception $ex) {
	errors::logException($ex->getMessage());
}