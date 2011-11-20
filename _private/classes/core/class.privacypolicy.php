<?php

class core_privacypolicy extends core_default
{
	private $template = 'privacypolicy.tpl.html';
	private $assignments;
	
	public function __construct($db, $qs) {

		$this->assignments['page']['title'] = 'Privacy Policy';
		$this->assignments['error'] = array();

	}
	
	public function getTemplate() {
		return $this->template;
	}
	public function getAssignments() {
		return $this->assignments;
	}
}