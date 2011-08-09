<?php

class core_clients_api extends core_default
{
	private $isHome = false;
	private $template = 'clients/api.tpl.html';
	private $assignments;
	private $db;
	
	
	public function __construct($db, $qs) {
		$this->db = $db;
				
		$this->assignments['page']['title'] = 'Api Demo';

		if (true === array_key_exists('region', $qs)) {
			$this->assignments['region'] = $qs['region'];
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