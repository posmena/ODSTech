<?php

class core_forgottenpassword extends core_default
{
	private $template = 'forgottenpassword.tpl.html';
	private $assignments;
	
	public function __construct($db, $qs) {
		$this->assignments['page']['title'] = 'Forgotten Password';
		$this->assignments['page']['showform'] = true;	
		
		if (true === array_key_exists('sendemail', $qs)) {
			
			$username = $qs['email'];
			$check = crypt($username,"kajhsgd737tgyus");
			
			$message = "You are receiving this email becuase a password reset was requested for this email address\n\n";
			$message .= "To reset your password please go to http://odst.co.uk/resetpassword.html?e=$username&chk=$check";
			$message .= "\n\nThank you\n\nODST Support";
			
			$headers .= 'From: ODST Support <support@odst.co.uk>'.$eol;
			mail($username, 'ODST Password Reset', $message, $headers);
			
			$this->assignments['page']['feedback'] = 'An email has been sent with a link to reset your password';
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