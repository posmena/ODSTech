<?php

class client {
	private $db;
	private $id;
	private $name;
	private $feeds;
	
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
	}
	
	function setFeeds() {
		$sql = sprintf("SELECT id, name FROM ot_feeds WHERE client_id=%d", $this->db->queryParameter($this->getId(), true));
		$result = $this->db->getQuery($sql);
		$feeds = array();
		foreach ($result as $feed) {
			$feeds[$feed['id']] = util::array_to_object($feed);
		}
		$this->feeds = $feeds;
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
