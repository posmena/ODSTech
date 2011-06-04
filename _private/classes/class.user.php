<?php

class user {
	private $db;
	private $username;
	private $password;
	private $email;
	
	function __construct($db, $username, $password, $user = null) {
		if ($user !== null) {
			$this->setUser($user);
		}
		
		$this->db = $db;
		$this->username = $username;
		$this->password = $password;
	}
	
	function login() {
		$sql = sprintf('SELECT email
				FROM ot_users
				WHERE username=%s
				AND password=%s', $this->db->queryParameter($this->username), $this->db->queryParameter(md5($this->password)));
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
	}
	
	function getEmail() {
		return $this->email;
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
