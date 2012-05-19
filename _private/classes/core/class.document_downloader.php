<?php

class core_document_downloader
{
	private $template = 'n/a';
	private $assignments;
	private $file = '';
	private $isHome = false;
	private $documentName = 'blank';
	
	public function __construct($db, $qs, $documentName = 'blank', $secure = true) {
		
		$user = util::getSession('user');
		if( $secure && $user == null )
			{
			util::redirect('/login.html?url=/downloads/' . $documentName  );	
			}
			
		$this->documentName = $documentName;
		
				$this->file = configuration::APPROOT . '_private/files/downloads/'. $this->documentName;

				if (true === file_exists($this->file)) {
					header("Content-type: application/octet-stream");
					header("Content-Disposition: attachment; filename=\"" . $this->documentName . "\"");
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
