<?php

class core_p20_client extends core_default
{
	private $template = 'p20/client.tpl.html';
	private $template_ajax = 'p20/client_ajax.tpl.html';
	private $assignments;
	
	public function __construct($db, $qs, $ajax) {
		$this->ajax = $ajax;
		
		
		if( false == $ajax )
			{
			$conn = new Mongo('localhost');
			$mdb  = $conn->odstech;
			$collection = $mdb->ot_feeds;
			
			$feed = $collection->findOne( array("client" => strtolower($qs['client'])) );
			$this->assignments['page']['title'] = $feed['feedname'] . ' P20 Tools';
			
			if( isset($qs['skin']) )
				{
				// using a skin - the publisher id is 
				$skin = $qs['skin'];
				$publisher_id = $qs['publisher_id'];
				
				if( isset($feed['network']) )
					{
					$network = $feed['network'];
					}
				else
					{
					$network = "webgains";
					}
				$collection_name = 'ot_users_' . $qs['skin'];
				$users = $mdb->$collection_name;
				
				if( $users != null )
					{
					$user = $users->findOne(array('publisher_id' => $publisher_id));
					}
					
				if( $user )
					{
					$affiliate_id = $user['ot_users_id'];
					}
				else
					{
					// create an ot_user and insert into mapping trable
					$newuser = array('skin' => $qs['skin'],$network=>$publisher_id);
					
					$mdb->ot_users->insert($newuser);
					$mdb->$collection_name->insert(array('publisher_id' => $publisher_id, 'ot_users_id' => $newuser['_id']));
					$affiliate_id = $newuser['_id'];
					}
			
				}
			else
				{
				// get from logged in user
				$affiliate_id = '7234678368';
				}
								
			$this->assignments['p20']['affiliate_id'] = $affiliate_id;
		
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