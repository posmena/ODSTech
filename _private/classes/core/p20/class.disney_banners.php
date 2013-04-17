<?php

class core_p20_disney_banners extends core_default
{
	private $template = 'p20/disney_banners.tpl.html';
	private $assignments;
	
	public function __construct($db, $qs, $ajax) {
		
		$affiliate_id = $qs['publisher_id'];
				
		$this->assignments['p20']['affiliate_id'] = $affiliate_id;
			
	}
	
	public function getTemplate() {
	
		return $this->template;
	}
	public function getAssignments() {
		return $this->assignments;
	}
}