<?php

class core_login extends core_default
{
	private $template = 'login.tpl.html';
	private $assignments;
	
	public function __construct($db, $qs) {
		$this->assignments['page']['title'] = 'Login';
		if (util::getSession('user')) {
			util::redirect('/dashboard.html');
		}
		
		if (true === array_key_exists('login', $qs)) {
			$username = $qs['email'];
			$password = $qs['password'];
			$user = new user($db, $username, $password);
			if ($user->login() === false) {
				$this->assignments['page']['feedback'] = 'Sorry, we cannot log you in at this time.';
				return false;
			}
			
			util::setSession('user', $user);
			util::redirect('/dashboard.html');			
		}
	}
	
	public function getTemplate() {
		return $this->template;
	}
	public function getAssignments() {
		return $this->assignments;
	}
}