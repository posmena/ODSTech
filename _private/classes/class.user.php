<?php

class user {
	private $db;
	private $username;
	private $password;
	private $email;
	private $userclass;
	private $clientid;
	
	function __construct($db, $username, $password, $user = null, $id = null) {
		
		$this->db = $db;		
		
		if ($user !== null) {
			$this->setUser($user);
		}
		
		if ( $id !== null )
			{
			$this->load_from_id($id);
			}
		
		$this->password = $password;
	}
	
	
	
	function load_from_id($id)
		{
		
		$obj = $this->db->ot_users->findOne(array('_id' => $id));
		
		if( $obj == null )
			{
			return false;
			}
		
		$this->setUser($obj);
		}
		
	function login() {
	
	/*
		$sql = sprintf('SELECT u.email, u.userclass, cu.clientid
                        FROM ot_users u
                        LEFT JOIN ot_client_users cu ON cu.userid = u.id
                        WHERE username=%s
				        AND password=%s', $this->db->queryParameter($this->username),
				                          $this->db->queryParameter(md5($this->password)));
		$result = $this->db->getQuery($sql);

		if (is_array($result) === true && count($result) === 0) {
			return false;
		}
		
		$obj = array_to_object($result[0]);
		*/
		$obj = $this->db->ot_users->findOne(array('username' => $this->username, 'password' => md5($this->password)));
		
		if( $obj == null )
			{
			return false;
			}
		
		$this->setUser($obj);
		
		return true;
	}
	
	function setUser($user) {
		if(isset($user['email'])) $this->email = $user['email'];
		if(isset($user['_id'])) $this->user_id = (string) $user['_id'];
		if(isset($user['firstname'])) $this->firstname = $user['firstname'];
		if(isset($user['username'])) $this->username = $user['username'];
		//$this->userclass = $user->userclass;
		//$this->clientid = $user->clientid;
	}
	
	function getEmail() {
		return $this->email;
	}
	
	function getUserclass() {
		return $this->userclass;
	}
	
	function getClientId() {
		return $this->clientid;
	}
}

function array_to_object($array = array()) {
    if (!empty($array)) {
        $data = false;
        foreach ($array as $akey => $aval) {
            $data -> {$akey} = $aval;
        }
        return $data;
    }
    return false;
}

?>
