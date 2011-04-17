<?php

function __autoload($class_name) 
{
	if (false !== strpos($class_name, 'core')) {
		$class_name = str_replace('core_', '', $class_name);
		$include_file = '../_private/classes/core/class.' . $class_name . '.php';
	} else {
		$include_file = '../_private/classes/class.' . $class_name . '.php';
	}
	if (file_exists($include_file) === true)
	{
    	include $include_file;
	} else {
		echo 'There was an error importing the ' . $include_file . ' class.';	
	}
}
Session_Start();
$site = new core_odstech_base;
$site->direct();
$site->draw();