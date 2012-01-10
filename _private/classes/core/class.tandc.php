<?php

class core_tandc extends core_default
{
	private $template = 'tandc.tpl.html';
	private $assignments;
	
	public function __construct($db, $qs) {

		$this->assignments['page']['title'] = 'Terms &amp; Conditions';
		$this->assignments['error'] = array();

	}
	
	public function getTemplate() {
		return $this->template;
	}
	public function getAssignments() {
		return $this->assignments;
	}
}