<?php

class client {
	private $db;
	private $id;
	private $name;
	private $feeds;
	private $mfeeds;
	
	function __construct($db, $client = null) {
		$this->db = $db;
		if ($client !== null) {
			$this->setClient(util::array_to_object($client));
		}
	}
	
	function setClient($client) {
		$this->id = $client->id;
		$this->name = $client->name;
		$this->setFeeds();
		$this->setMasterFeeds();
	}
	
	function setFeeds() {
		$sql = sprintf("SELECT id, name, master_feed_id FROM ot_feeds WHERE client_id=%d", $this->db->queryParameter($this->getId(), true));
		$result = $this->db->getQuery($sql);
		$feeds = array();
		if (count($result) > 0) {
			foreach ($result as $feed) {
				$feeds[$feed['id']]['data'] = util::array_to_object($feed);
				
				// Fields
				$sql = sprintf("SELECT fieldname, name FROM ot_feeds_fields WHERE ot_feed_id=%d", $this->db->queryParameter($feed['id'], true));
				$result = $this->db->getQuery($sql);
				$feeds[$feed['id']]['fields'] = util::array_to_object($result);
				
				// Filters
				$sql = sprintf("SELECT fieldname, operand, criteria FROM ot_feeds_filters WHERE ot_feed_id=%d", $this->db->queryParameter($feed['id'], true));
				$result = $this->db->getQuery($sql);
				$feeds[$feed['id']]['filters'] = util::array_to_object($result);
			}
		}
		
		$this->feeds = $feeds;
	}
	
	function setMasterFeeds() {
		$sql = sprintf("SELECT id, name, ot_table FROM ot_client_master_feeds WHERE client_id=%d", $this->db->queryParameter($this->getId(), true));
		$result = $this->db->getQuery($sql);
		$feeds = array();
		foreach ($result as $feed) {
			$feeds[$feed['id']]['data'] = util::array_to_object($feed);
			
			// Load client feed
			$sql = 'SELECT * FROM ' . $feed['ot_table'] . ' LIMIT 0,1';
			$clientFieldData = $this->db->getFields($sql);
			
			// Load ODST Property Table (and more if required);
			$sql = 'SELECT * FROM Property LIMIT 0,1';
			$otFieldData = $this->db->getFields($sql);
			
			foreach ($clientFieldData as $field) {
				$fieldData['raw.'.$field] = $field;
			}
			
			foreach ($otFieldData as $field) {
				$fieldData['p.'.$field] = $field;
			} 
			
			$feeds[$feed['id']]['fields'] = util::array_to_object($fieldData);
		}
		
		$this->mfeeds = $feeds;
	}

	function getNavigation() {
		$navigation = array('Algarve','Amsterdam','Barcelona','Corfu','Crete','Cyprus','Egypt','Fuerteventura','Halkidiki','Ibiza','Kos','Lanzarote','Lombardy','Madeira','Madrid','Majorca','Malta','Menorca','Morocco','Mykonos','Paris','Prague','Rhodes','Rome','Santorini','Tenerife','Turkey','Venice','Zante');
		return $navigation;
	}
	
	function getId() {
		return $this->id;
	}
	
	function getName() {
		return $this->name;
	}
	
	function getFeeds() {
		return $this->feeds;
	}
	
	function getMasterFeeds() {
		return $this->mfeeds;
	}
	
	function getFeed($id = null) {
		if ($id === null) {
			return false;
		}
		
		$feeds = $this->getFeeds();
		if (array_key_exists($id, $feeds) === false) {
			return false;
		}
		
		return $feeds[$id];
	}
}
