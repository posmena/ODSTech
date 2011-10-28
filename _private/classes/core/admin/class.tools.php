<?php

class core_admin_tools
{
	private $db;
	private $qs;
	
	public function __construct($db, $qs) {
		$this->db = $db;
	}
	
	public function getClients() {
		//$sql = sprintf('SELECT id, name
		//				FROM ot_clients');
		//$results = $this->db->getQuery($sql);
		$clients = array();
		$collection = $this->db->ot_clients;
		$results = $collection->find();
		if (count($results) > 0) {
			foreach ($results as $client) {
				print_r($client->_id);
				//$clients[$client['_id']] = new client($this->db, $client);
			}
		}
		
		return $clients;
	}
	
	public function getClient($id) {
		$clients = $this->getClients();
		if (false === array_key_exists($id, $clients)) {
			util::redirect('/');
		}
		
		return $clients[$id];
	}
}
