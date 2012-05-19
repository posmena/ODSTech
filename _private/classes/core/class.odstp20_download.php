<?php

class core_odstp20_download extends core_document_downloader
{
	public function __construct($db, $qs) {
		parent::__construct($db, $qs, 'ODSTP20.zip');
	}
	
}