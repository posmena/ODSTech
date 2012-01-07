<?php

class custom_octopus_feed extends network_base
{
	private $name   = 'Custom - Octopus';
	public  $feedid = 'octopus';
	private $fields = array();
	private $prefix;
	public  $custom = array();
	
	public function __construct($local_file = null, $full = false)
	{
		global $db;

		$this->csv_call($local_file, $full);
	}
}