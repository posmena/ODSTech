<?php

class core_resetpassword extends core_default
{
	private $template = 'forgottenpassword.tpl.html';
	private $assignments;
	
	public function __construct($db, $qs) {
		$this->assignments['page']['title'] = 'Reset Password';
		$this->assignments['page']['showform'] = true;	
		$redirect = '/';
		
		if (false === isset($qs['e']) || false === isset($qs['chk'])) {
			//shouldn't be here
			util::redirect($redirect);	
			return;
		}
		
		$username = $qs['email'];
		$chk = crypt($username,"kajhsgd737tgyus");
		
		if ($chk != $qs['chk']) {
			//shouldn't be here
			util::redirect($redirect);	
			return;
		}
		
		// check email exists
		$conn = new Mongo('localhost');
		$mdb  = $conn->odstech;
		$collection = $mdb->ot_users;

		$userExists = (bool) $mdb->ot_users->find(array('username' => $qs['e']))->count();
		
		if( false == $userExists )
			{
			$this->assignments['page']['feedback'] = 'No user has been found with this email address.  Please <a href="/signup.html">register</a> with ODST';
			$this->assignments['page']['showform'] = false;	
			return;
			}
		
		if (true === array_key_exists('changepassword', $qs)) {
			if (false === isset($qs['password']) || false === isset($qs['password2']) )
				{
					$this->assignments['page']['feedback'] = 'Enter a new password.';
					return;
				}
			
			if ($qs['password'] != $qs['password2'] )
				{
					$this->assignments['page']['feedback'] = 'Your passwords do not match';
					return;
				}
					
			// update password in db
			$u = $mdb->ot_users->findOne(array('username' => $qs['e']));
			$u['password']  = md5($qs['password']);
			$collection->save($u);
			
			$this->assignments['page']['feedback'] = 'Your password has been changed.';
			$this->assignments['page']['showform'] = false;		
		}
	}
	
	public function getTemplate() {
		return $this->template;
	}
	public function getAssignments() {
		return $this->assignments;
	}
}