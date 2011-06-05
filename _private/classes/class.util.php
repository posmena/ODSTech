<?php

class util
{
	function redirect($location) {
		header('Location: '.$location);
	}
	
	function setSession($key, $val) {
		$_SESSION[$key] = $val;
	}
	
	function getSession($key) {
		if (false === array_key_exists($key, $_SESSION)) {
			return false;
		}
		
		return $_SESSION[$key];
	}
	
	function logout() {
		session_unset();
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
}
