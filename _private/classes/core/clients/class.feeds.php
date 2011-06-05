<?php

class core_clients_feeds extends core_default
{
	private $isHome = false;
	private $template = 'clients/feeds.tpl.html';
	private $assignments;
	private $db;
	
	
	public function __construct($db, $qs) {
		$this->db = $db;
				
		$this->assignments['page']['title'] = 'Feeds';
		if (false === array_key_exists('feedid', $qs)) {
			$feed = false;
		} else {
			$tools = new core_admin_tools($db, $qs);
			$client = $tools->getClient($qs['clientid']);
			$feed = $client->getFeed($qs['feedid']);
		}
		
		if (false !== $feed) {
			$nav = array ('clients_feeds' => array('name' => 'view', 'url' => '/clients/'.$client->getId().'/feeds/'.$feed->id),
			          'clients_feeds_configure' => array('name' => 'configure', 'url' => '/clients/'.$client->getId().'/feedsettings/'.$feed->id));
		
			$this->assignments['navigation'] = $nav;
		}
		
	
		$this->assignments['feed'] = $feed;
		
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