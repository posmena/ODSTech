<?php

class core_clients_dashboard extends core_default
{
	private $isHome = false;
	private $template = 'clients/dashboard.tpl.html';
	private $assignments;
	private $db;
	
	
	public function __construct($db, $qs) {
		$this->db = $db;
				
		$this->assignments['page']['title'] = 'Dashboard';
		
		
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