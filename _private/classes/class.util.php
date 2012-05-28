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
	
	function unzip($file)  {
		//the basic unzip operation
		$zip = new ZipArchive;
		 $res = $zip->open($file);
		 if ($res === TRUE) {
			for($i = 0; $i < $zip->numFiles; $i++) {
             $entry = $zip->getNameIndex($i);
			 $zip->extractTo(dirname($file).'/');
			 $zip->close();
			 return dirname($file).'/'. $entry;
			 echo($entry);
			 }
		 } else {
			 echo "failed to unzip" . $file;
			 return false;
		 }
	}
}
