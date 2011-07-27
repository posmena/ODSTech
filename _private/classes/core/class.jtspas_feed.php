<?php

class core_jtspas_feed
{
	private $template = 'n/a';
	private $assignments;
	private $file = '';
	private $isHome = false;
	
	public function __construct($db, $qs) {
		
		if (false === array_key_exists('type', $qs)) {
			print 'Type must be supplied';
			exit;
		}

		$this->file = configuration::APPROOT . '_private/files/compressedfeeds/jtspas/'.strtolower($qs['type']) . '.csv.zip';
		
		if (true === file_exists($this->file)) {
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=\"jtspas-" .$qs['type']. ".csv.zip\"");
			header("Content-type: application/force-download"); 
		    //header("Content-length: ".filesize($this->file)); 
		    readfile($this->file);
			exit; 
		} else {
			print 'File does not exist';
			exit;
		}

	}

	
	
	public function getTemplate() {
		return $this->template;
	}
	public function getAssignments() {
		return $this->assignments;
	}
	
	public function isHome() {
		return $this->isHome;
	}
}
