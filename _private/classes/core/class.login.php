<?php

class core_login extends core_default
{
	private $template = 'login.tpl.html';
	private $assignments;
	
	public function __construct($db, $qs) {
		$this->assignments['page']['title'] = 'Login';
		$redirect = '/admin/dashboard.html';
		/*
		if ($user = util::getSession('user')) {
			if (true === ctype_digit($user->getClientId())) {
				$redirect = '/clients/'.$user->getClientId().'/dashboard';
			}
			
			util::redirect($redirect);
		}
		*/
		if (true === array_key_exists('login', $qs)) {
			/*
			$username = $qs['email'];
			$password = $qs['password'];
			$user = new user($db, $username, $password);
			if ($user->login() === false) {
				*/
				$this->assignments['page']['feedback'] = 'You credentials are not authorised to login here.';
				return false;

			//}
			
			if (true === ctype_digit($user->getClientId())) {
				$redirect = '/clients/'.$user->getClientId().'/dashboard';
			}
			
			util::setSession('user', $user);
			util::redirect($redirect);			
		}
	}
	
	public function getTemplate() {
		return $this->template;
	}
	public function getAssignments() {
		return $this->assignments;
	}
}