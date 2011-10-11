<?php

class core_chesca_feed extends core_feed_downloader
{
	public function __construct($db, $qs) {
		parent::__construct($db, $qs, 'damsel');
	}
	
}