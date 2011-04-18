<?php

class core_login
{
	private $template = 'login.tpl.html';
	private $assignments;
	
	public function __construct($db, $qs) {
		$this->assignments['page']['title'] = 'Login';
		if (true === array_key_exists('login', $qs)) {
			$username = $qs['email'];
			$password = $qs['password'];
			
			$this->assignments['page']['feedback'] = 'Sorry, we cannot log you in at this time.';
		}
	}
	
	public function getTemplate() {
		return $this->template;
	}
	public function getAssignments() {
		return $this->assignments;
	}
}