<?php

class core_p20_hilton_banners extends core_default
{
	private $template = 'p20/hilton_banners.tpl.html';
	private $assignments;
	
	public function __construct($db, $qs, $ajax) {
		
		$affiliate_id = $qs['affiliate_id'];
		$campaign_id = $qs['campaign_id'];
		
		$this->assignments['p20']['affiliate_id'] = $affiliate_id;
		$this->assignments['p20']['campaign_id'] = $campaign_id;
			
	}
	
	public function getTemplate() {
	
		return $this->template;
	}
	public function getAssignments() {
		return $this->assignments;
	}
}