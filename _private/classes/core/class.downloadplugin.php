<?php

class core_downloadplugin extends core_default
{
	private $template = 'downloadplugin.tpl.html';
	private $assignments;
	
	public function __construct($db, $qs) {
		$allowedClients = array ('easyjetholidays','octopustravel');

		if (false === in_array($qs['client'], $allowedClients)) {
			$this->assignments['clienttpl'] = 'plugindownload/notyet.tpl.html';
		} else {
			$this->assignments['clienttpl'] = 'plugindownload/'. $qs['client'] . '.tpl.html';
		}
		 
		$this->assignments['client'] = $qs['client'];
		
		$this->assignments['page']['title'] = 'Download Plugin';
			
			
		switch ($this->assignments['client']) {
			case 'easyjetholidays':
			{
				$this->assignments['page']['title'] = 'easyJet Holidays Wordpress plugin download';
				break;
			}
			case 'octopustravel'
			{
				$this->assignments['page']['title'] = 'Octopus Travel Wordpress plugin download';
				break;
			}
		
		}
		
		$this->assignments['error'] = array();
				
	}
	
	public function getTemplate() {
		return $this->template;
	}
	public function getAssignments() {
		return $this->assignments;
	}
}