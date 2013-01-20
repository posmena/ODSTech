<?php

class core_p20_client extends core_default
{
	private $template = 'p20/client.tpl.html';
	private $template_ajax = 'p20/client_ajax.tpl.html';
	private $assignments;
	
	public function __construct($db, $qs, $ajax) {
		$this->ajax = $ajax;
		$conn = new Mongo('localhost');
		$mdb  = $conn->odstech;
		$affiliate_id = "";
		
		if( false == $ajax )
			{
			
			$collection = $mdb->ot_feeds;
			
			$feed = $collection->findOne( array("client" => strtolower($qs['client'])) );
			
			$this->assignments['page']['title'] = $feed['feedname'] . ' P20 Tools';
			
			if( isset($qs['skin']) )
				{
				// using a skin - the publisher id is 
				$skin = $qs['skin'];
				$publisher_id = $qs['publisher_id'];
				if( isset($qs['campaign_id']) )
					{
					$campaign_id = $qs['campaign_id'];
					}
					
				if( isset($qs['customer_id']) )
					{
					$network = $qs['customer_id'];
					}
				else
					{
					if( isset($feed['network']) )
						{
						$network = $feed['network'];
						}
					else
						{
						$network = "webgains";
						}
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
					// if the network is new then add the data
						
					
					$mdb->ot_users->update(array('_id' => $user['ot_users_id']),array('$unset' => array("KK" => 1)));
					$mdb->ot_users->update(array('_id' => $user['ot_users_id']),array('$unset' => array("TD" => 1)));
					$mdb->ot_users->update(array('_id' => $user['ot_users_id']),array('$unset' => array("AWIN" => 1)));
														
					$mdb->ot_users->update(array('_id' => $user['ot_users_id']),array('$set' => array($network => $publisher_id)));
					
					}
				else
					{
					// create an ot_user and insert into mapping trable
					$newuser = array('skin' => $qs['skin'],$network=>$publisher_id);
					if( isset($campaign_id) )
						{
						$newuser['campaign_id'] = $campaign_id;
						}
					
					$mdb->ot_users->insert($newuser);
					$mdb->$collection_name->insert(array('publisher_id' => $publisher_id, 'ot_users_id' => $newuser['_id']));
					$affiliate_id = $newuser['_id'];
					}
					
				// store the affiliateid in the session for saving units	
				$user = new user($db,'','',null,$affiliate_id);
			
			    util::setSession('user',$user);
				}
			else
				{
				// get from logged in user
				$user = util::getSession('user');	
				if( $user )
					{
					$affiliate_id = $user->user_id;
					}
			
				}
								
			$this->assignments['p20']['affiliate_id'] = $affiliate_id;
		
			foreach($feed['fields'] as $field)
				{
				if( $field['searchable'] == true )
					{
					$field['displayname']  = preg_replace("/[^a-zA-Z0-9\s]/", " ", $field['fieldname']);
					$field['displayname'] = ucwords($field['displayname']);
					
					$feed['searchoptions'][] = $field;
					}
				}
				
			$this->assignments['feed'] = $feed;
				
			}
			
			else if( isset($_POST['action']) )
			{
			// ajax call
			// are we saving the unit?
			
			$action = $_POST['action'];
			switch( $action ) 
				{
				case 'load':
				    // load from DB where unitID=xx and affiliate_id=$affiliate_id
					//send data back as ajax return data
					$user = util::getSession('user');				
					$affiliate_id = $user->user_id;
					$unitid = $_POST['unitid'];
					$coll = $mdb->p20_contentunits;
					$itm = $coll->findOne(array('_id' => $unitid . '_' . $affiliate_id));
					echo(http_build_query($itm['values']));
					die();
					break;
				
				case 'save':
					// store in DB unit=xx and affiliate_id=$affiliate_id
					$user = util::getSession('user');				
					$affiliate_id = $user->user_id;
					$coll = $mdb->p20_contentunits;
					$unitid = $_POST['unitid'];
					$itm = array('_id' => $unitid . '_' . $affiliate_id, 'affiliateid' => $affiliate_id, 'unitid' => $unitid, 'values' => $_POST);
					$coll->save($itm);					
					die();
					break;
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