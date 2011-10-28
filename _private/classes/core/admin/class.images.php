<?php

class core_admin_images extends core_default
{
	private $isHome = false;
	private $template = 'admin/images.tpl.html';
	private $assignments;
	private $db;
	
	
	public function __construct($db, $qs) {
		$this->db = $db;
		
		if (true === array_key_exists('save_images', $qs)) {
			if (true === array_key_exists('caption', $qs) && is_array($qs['caption']) === true) {
				foreach ($qs['caption'] as $imageid => $caption) {
					$sql = sprintf('UPDATE ot_images SET alt=%s, href=%s WHERE id=%d', $this->db->queryParameter($caption),
					                                                                   $this->db->queryParameter($qs['href'][$imageid]),
					                                                                   $this->db->queryParameter($imageid, true));
					$this->db->changeQuery($sql); 
				}
			}
			
			$this->assignments['page']['feedback'] = 'Your changes have been saved.';
		}
				
		$this->assignments['page']['title'] = 'Images';
		
		$nav = array ('admin_images' => array('name' => 'list', 'url' => '/admin/images.html'),
			          'admin_image_upload' => array('name' => 'upload', 'url' => '/admin/image_upload.html'));
		
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
		//$sql = 'SELECT id, url, alt, href FROM ot_images ORDER BY sort';
		//$result = $this->db->getQuery($sql);
		$result = '';
		return $result;
	}
	
	public function isHome() {
		return $this->isHome;
	}
}