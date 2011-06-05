<?php

class user {
	private $db;
	private $username;
	private $password;
	private $email;
	private $userclass;
	private $clientid;
	
	function __construct($db, $username, $password, $user = null) {
		if ($user !== null) {
			$this->setUser($user);
		}
		
		$this->db = $db;
		$this->username = $username;
		$this->password = $password;
	}
	
	function login() {
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
		
		$this->setUser($obj);
		
		return true;
	}
	
	function setUser($user) {
		$this->email = $user->email;
		$this->userclass = $user->userclass;
		$this->clientid = $user->clientid;
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
