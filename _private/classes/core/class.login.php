<?php

class core_login
{
	private $template = 'login.tpl.html';
	private $assignments;
	
	public function __construct() {
		$this->assignments['page']['title'] = 'Login';
	}
	
	
	public function getTemplate() {
		return $this->template;
	}
	public function getAssignments() {
		return $this->assignments;
	}
}