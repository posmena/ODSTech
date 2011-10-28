<?php

class core_jtspas_feed extends core_feed_downloader
{
	public function __construct($db, $qs) {
		parent::__construct($db, $qs, 'jtspas');
	}
	
}