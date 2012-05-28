<?php

class core_p20_home extends core_default
{
	private $template = 'p20/home.tpl.html';
	private $template_ajax = 'p20/home_ajax.tpl.html';
	private $assignments;
	
	public function __construct($db, $qs, $ajax) {
		$this->assignments['page']['title'] = 'P20 Content Units';
		$this->ajax = $ajax;
		
		
		if( false == $ajax )
			{
			$conn = new Mongo('localhost');
			$mdb  = $conn->odstech;
			$collection = $mdb->ot_feeds;
			
			$activefeeds = $collection->find( array("active" => true) );
			$activefeeds->sort(array('client' => 1));
			
			foreach($activefeeds as $feed)
				{
				if( isset($feed['short_description']) )
					{
					$pos = strpos($feed['short_description'], '.');
					if( $pos !== false )
						{
						$feed['short_description'] = substr($feed['short_description'], 0, $pos+1);
						}
					}
				$this->assignments['feeds'][] = $feed;
				}
			}
			
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