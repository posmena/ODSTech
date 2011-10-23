<?php

class core_signup extends core_default
{
	private $template = 'signup.tpl.html';
	private $assignments;
	
	public function __construct($db, $qs) {
		$this->assignments['page']['title'] = 'Signup';
		$this->assignments['error'] = array();
		$this->assignments['signup_complete'] = false;
		$signup = true;
		if (true === isset($_POST['create'])) {
			// compare passwords
			
			$user = $_POST['user'];
			unset($_POST['password']);
			unset($_POST['password2']);
			$this->assignments['post'] = $user;
						
			if ($user['password'] != '') {
				if ($user['password'] != $user['password2']) {
					$this->assignments['error'][] = 'Passwords do not match';
					$signup = false;
				}
			} else {
				$this->assignments['error'][] = 'You must provide a password';
				$signup = false;			
			}

			if ($user['email'] == '') {
				$this->assignments['error'][] = 'You must provide a valid email address';
				$signup = false;	
			}

			if (isset($user['agree']) === false || $user['agree'] != 'on') {
				$this->assignments['error'][] = 'You must accept the terms and conditions';
				$signup = false;	
			}

			if ($signup === true) {
				$conn = new Mongo('localhost');
				$mdb  = $conn->odstech;
				$collection = $mdb->ot_users;

				$userExists = (bool) $mdb->ot_users->find(array('username' => $user['email']))->count();

				if (true === $userExists) {
					$this->assignments['error'][] = 'Username already exists. ';	
					$signup = false;	
				} else {
					$u['username']  = $user['email'];
					$u['firstname'] = $user['fname'];
					$u['lastname']  = $user['sname'];
					$u['password']  = md5($user['password']);
					
					$collection->save($u);
					if (false === isset($u['_id'])) {
						$signup = false;
					}
				}

				if ($signup == true) {
					$this->assignments['signup_complete'] = true;	
				} else {
					$this->assignments['error'][] = 'Apologies, there was an error - please try again.';	
				}
			}
		}

	}
	
	public function getTemplate() {
		return $this->template;
	}
	public function getAssignments() {
		return $this->assignments;
	}
}