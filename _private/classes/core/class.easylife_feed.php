<?php

class core_easylife_feed extends core_feed_downloader
{
	public function __construct($db, $qs) {
		parent::__construct($db, $qs, 'easylife');
	}
	
}