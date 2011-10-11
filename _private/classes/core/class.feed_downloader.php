<?php

class core_feed_downloader
{
	private $template = 'n/a';
	private $assignments;
	private $file = '';
	private $isHome = false;
	private $feedName = 'blank';
	
	public function __construct($db, $qs, $feedName = 'blank') {
		
		$this->feedName = $feedName;
		if (false === array_key_exists('type', $qs)) {
			print 'Type must be supplied';
			exit;
		}

		if (false === array_key_exists('format', $qs)) {
			$format = 'csv';
		} else {
			$format = $qs['format'];
		}

		switch ($format) {
			case 'csv':
			{
				$ext = '.csv.zip';
				$this->file = configuration::APPROOT . '_private/files/compressedfeeds/'. $this->feedName .'/'.strtolower($qs['type']) . '.csv.zip';

				if (isset($qs['unzipped'])){
					$ext = '.csv';
					$expandedPath = configuration::APPROOT . '_private/files/inflated/' . $feedName . '/';

					if (false === file_exists($this->file)) {
						throw new Exception ('File: ' . $filepath . ' does not exist');
					}

					shell_exec('unzip -jo ' . $this->file . ' -d ' . $expandedPath);

					$this->file = $expandedPath . '/' . strtolower($qs['type']) . '.csv';
				}

				if (true === file_exists($this->file)) {
					header("Content-type: application/octet-stream");
					header("Content-Disposition: attachment; filename=\"" . $this->feedName . "-" .$qs['type']. "" . $ext . "\"");
					header("Content-type: application/force-download"); 
				    //header("Content-length: ".filesize($this->file)); 
				    readfile($this->file);
					exit; 
				} else {
					print 'File does not exist';
					exit;
				}	

				break;
			}
			case 'froogle':
			case 'xml':
			{
				$this->file = configuration::APPROOT . '_private/files/hostedfeeds/' . $this->feedName. '/'.strtolower($qs['type']) . '.xml';

				if (true === file_exists($this->file)) {
					header("Content-type: application/octet-stream");
					header("Content-Disposition: attachment; filename=\"" . $this->feedName . "-" .$qs['type']. ".xml\"");
					header("Content-type: application/force-download"); 
				    //header("Content-length: ".filesize($this->file)); 
				    readfile($this->file);
					exit; 
				} else {
					print 'File does not exist';
					exit;
				}	

				break;
				
			}
			default:
			{
				print 'Invalid format';
				exit;
			}

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
