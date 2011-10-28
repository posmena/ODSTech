<?php

interface network
{
	public function __construct($local_file = null, $full = false);
	public function getFields();
	public function getName();
	public function getPrefix();
	public function updateFeedList($network_id);
	public function addFeed($feed);
	public function parse_xml($file, $feed_id);
	public function insert_products($products);
}
