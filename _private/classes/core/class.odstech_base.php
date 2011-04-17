<?php

class core_odstech_base
{
	private $assignments = '';
	
	public function __construct()
	{
		$this->db = new database;
		//$this->ms = new membership($this->db);
		//$this->fn = new sitebuilder_functions($this->db);
		$this->qs = $_GET;
		foreach($_POST as $key => $value)
		{
			$this->qs[$key] = $value;
		}

		$this->db->connection("odstech");
		$this->tplBase = new template('odstech');
		//$this->global_minified_js = Minify_groupUri('sitebuilderjs');
		//$this->global_minified_css = Minify_groupUri('sitebuildercss');
		
	}
	
	public function direct() {
		$this->template = 'home.tpl.html';
		if (array_key_exists('loc', $this->qs) === true) {
			$classname      = 'core_'.$this->qs['loc'];
			if(class_exists($classname) === true) {
				$loc               = new $classname($this->db);
				$this->template    = $loc->getTemplate();
				$this->assignments = $loc->getAssignments();
			}
		}
	}
	
	public function draw($ajax = false)
	{
		if (is_array($this->assignments) === true && count($this->assignments) > 0) {
			foreach ($this->assignments as $key => $assignment) {
				$this->tplBase->assign($key, $assignment);	
			}
		}
		
		$this->tplBase->assign('content', $this->template);
		$this->tplBase->assign('domain', '');
		$this->tplBase->assign('current_url', '');


		if($ajax)
		{
			$this->tplBase->display($this->template);
		}
		else
		{
			//$this->tplBase->display('sitebuilder.tpl.html');
			//$mhtml = new Minify_HTML($this->tplBase->fetch('sitebuilder.tpl.html'));
			//$output = $mhtml->minify($this->tplBase->fetch('sitebuilder.tpl.html'));
			//$this->tplBase->assign('output', $output);
			//$this->tplBase->display('minifiedhtml.tpl.html');
			$this->tplBase->display('odstech.tpl.html');
		}
	}
}

?>