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
		return $_SESSION[$key];
	}
}
