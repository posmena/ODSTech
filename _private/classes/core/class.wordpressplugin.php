<?php

class core_wordpressplugin extends core_default
{
	private $template = 'wordpresspugin.tpl.html';
	private $assignments;
	
	public function __construct($db, $qs) {
		$this->assignments['page']['title'] = 'Wordpress Plugin';
	}
	
	public function getTemplate() {
		return $this->template;
	}
	public function getAssignments() {
		return $this->assignments;
	}
}