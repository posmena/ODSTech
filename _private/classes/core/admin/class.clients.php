<?php

class core_admin_clients extends core_default
{
	private $isHome = false;
	private $template = 'admin/clients.tpl.html';
	public $assignments;
	private $db;
	private $qs;
	
	public function __construct($db, $qs) {
		$this->db = $db;
		$this->qs = $qs;

				
		$this->assignments['page']['title'] = 'Create New Client';

		$nav = array ('admin_clients_new' => array('name' => 'new', 'url' => '/admin/clients.html?action=new'),
			          'admin_clients_list' => array('name' => 'list', 'url' => '/admin/clients.html?action=list'));
		
		$this->assignments['navigation'] = $nav;
		
		if (false === $this->direct($db, $qs)) {
			$this->admin_clients_list();
			// do stuff if no other action has been called
		}		
	}

	public function admin_clients_new() {
		$this->template = 'admin/clients_edit.tpl.html';
		if (true === isset($this->qs['client']['name']) && $this->qs['client']['name'] != '') {
			//echo 'okay lets save this bitch';

			$item['name'] = $this->qs['client']['name'];

			$collection = $this->db->ot_clients;
			$collection->save($item);
			print_r($item);
			die();
			header('Location: /admin/clients.html?action=list');	
		}	
	}

	public function admin_clients_list() {
		$this->template = 'admin/clients.tpl.html';
		$this->assignments['action'] = __FUNCTION__;
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