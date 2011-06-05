<?php

class core_admin_cms extends core_default
{
	private $isHome = false;
	private $template = 'admin/cms.tpl.html';
	private $assignments;
	private $db;
	
	
	public function __construct($db, $qs) {
		$this->db = $db;
		
		$this->assignments['page']['title'] = 'CMS';
		
		$nav = array ('admin_cms' => array('name' => 'home', 'url' => '/admin/cms.html'),
			          'admin_cms_feeds' => array('name' => 'feeds', 'url' => '/admin/cms-feeds.html'),
			          'admin_cms_widgets' => array('name' => 'widgets', 'url' => '/admin/cms-widgets.html'),
			          'admin_cms_opm' => array('name' => 'opm', 'url' => '/admin/cms-opm.html'),
			          'admin_cms_banners' => array('name' => 'banners', 'url' => '/admin/cms-banners.html'),
					  );
		
		$this->assignments['navigation'] = $nav;
		
		
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