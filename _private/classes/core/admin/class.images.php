<?php

class core_admin_images extends core_default
{
	private $isHome = false;
	private $template = 'admin/images.tpl.html';
	private $assignments;
	private $db;
	
	
	public function __construct($db, $qs) {
		$this->db = $db;
		
		$dir = configuration::APPROOT.'uploads/images/';
		
		
		
		
		$this->assignments['page']['title'] = 'Images';
		
		$nav = array ('admin_images' => array('name' => 'list', 'url' => '/admin/images.html'),
			          'admin_image_upload' => array('name' => 'upload', 'url' => '/admin/image-upload.html'));
		
		$this->assignments['navigation'] = $nav;
		
		$this->assignments['images'] = $this->getImages();
		
		
	}
	
	public function getTemplate() {
		return $this->template;
	}
	public function getAssignments() {
		return $this->assignments;
	}
	
	public function getImages() {
		$sql = 'SELECT id, url, alt FROM ot_images ORDER BY sort';
		$result = $this->db->getQuery($sql);
		return $result;
	}
	
	public function isHome() {
		return $this->isHome;
	}
}