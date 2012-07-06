<?php

class core_p20_client extends core_default
{
	private $template = 'p20/client.tpl.html';
	private $template_ajax = 'p20/client_ajax.tpl.html';
	private $assignments;
	
	public function __construct($db, $qs, $ajax) {
		$this->assignments['page']['title'] = ucwords($qs['client']) . ' P20 Tools';
		$this->ajax = $ajax;
		
		
		if( false == $ajax )
			{
			$conn = new Mongo('localhost');
			$mdb  = $conn->odstech;
			$collection = $mdb->ot_feeds;
			
			$feed = $collection->findOne( array("client" => $qs['client']) );
			
			foreach($feed['fields'] as $field)
				{
				if( $field['searchable'] == true )
					{
					$feed['searchoptions'][] = $field;
					}
				}
				
			}
			$this->assignments['feed'] = $feed;
		if (true === array_key_exists('login', $qs)) {
			
			$username = $qs['email'];
			$password = $qs['password'];
			$user = new user($db, $username, $password);
			if ($user->login() === false) {
				
				$this->assignments['page']['feedback'] = 'Incorrect user name or password.';
				return false;
				}
				
				util::setSession('user', $user);
				util::redirect($redirect);	
				return true;
			}
			
	}
	
	public function getTemplate() {
		if( $this->ajax ) {
					return $this->template_ajax;
				}
		return $this->template;
	}
	public function getAssignments() {
		return $this->assignments;
	}
}