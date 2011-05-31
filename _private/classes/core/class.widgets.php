<?php

class core_widgets
{
	private $template = 'widgets.tpl.html';
	private $assignments;
	
	public function __construct($db, $qs) {
		$this->assignments['page']['title'] = 'Widgets';
	}
	
	public function getTemplate() {
		return $this->template;
	}
	public function getAssignments() {
		return $this->assignments;
	}
}