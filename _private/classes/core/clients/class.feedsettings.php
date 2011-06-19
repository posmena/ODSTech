<?php

class core_clients_feedsettings extends core_default
{
	private $isHome = false;
	private $template = 'clients/feed_configure.tpl.html';
	private $assignments;
	private $db;
	
	
	public function __construct($db, $qs) {
		$this->db = $db;
		
		// has there been postage?
		if(isset($qs['savesettings'])) {
			print_r($qs);
		}
				
		$this->assignments['page']['title'] = 'Configure Feed';
		if (false === array_key_exists('feedid', $qs)) {
			$feed = false;
		} else {
			$tools = new core_admin_tools($db, $qs);
			$client = $tools->getClient($qs['clientid']);
			$feed = $client->getFeed($qs['feedid']);
		}
		
		// grab master feeds for this client
		$mFeeds = $client->getMasterFeeds();
		
		if (false !== $feed) {
			
			// grab fields for the currently selected masterfeed
			//$feedFields = $client->getFeedFields($feed);			
			
			$nav = array ('clients_feeds' => array('name' => 'view', 'url' => '/clients/'.$client->getId().'/feeds/'.$feed['data']->id),
			          'clients_feedsettings' => array('name' => 'configure', 'url' => '/clients/'.$client->getId().'/feedsettings/'.$feed['data']->id));
		
			$this->assignments['navigation'] = $nav;
		}
	
		$this->assignments['feed'] = $feed;
		$this->assignments['mfeeds'] = $mFeeds;
		
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