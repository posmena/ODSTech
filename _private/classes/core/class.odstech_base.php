<?php

class core_odstech_base
{
	private $assignments = '';
	private $action = 'home';
	private $user = null;
	private $base = 'odstech_temp.tpl.html';
	
	public function __construct()
	{
		//$this->db = new database;
		$conn = new Mongo('localhost');
		// access database
		$this->db = $conn->odstech;
		// access collection
  		//$collection = $this->db->odstbase;
		
		//$this->ms = new membership($this->db);
		//$this->fn = new sitebuilder_functions($this->db);
		$this->qs = $_GET;
		foreach($_POST as $key => $value)
		{
			$this->qs[$key] = $value;
		}

		//$this->db->connection("odstech");
		$this->tplBase = new template('odstech');
		$this->user = util::getSession('user');
		
		//$this->global_minified_js = Minify_groupUri('sitebuilderjs');
		//$this->global_minified_css = Minify_groupUri('sitebuildercss');
		
	}
	
	public function direct() {
		$this->template = 'home.tpl.html';
		if (array_key_exists('loc', $this->qs) === true) {
			$classname      = 'core_'.$this->qs['loc'];
			$this->action   = $this->qs['loc'];
			if(class_exists($classname) === true) {
				$loc               = new $classname($this->db, $this->qs);
				$this->template    = $loc->getTemplate();
				$this->assignments = $loc->getAssignments();
				if (util::getSession('user') !== false) {
					$this->user = util::getSession('user');
					if ($loc->isHome() === false) {
						$this->base = 'odstech_loggedin.tpl.html';
						$tools = new core_admin_tools($this->db, $this->qs);
						if ($this->user->getUserclass() == 'adminuser') {
							// Stuff if logged in as admin user
							$this->assignments['clients'] = $tools->getClients();				
						} elseif (true === array_key_exists('usertype', $this->qs) && $this->qs['usertype'] == 'admin') {
							util::redirect('/');
						}
						
						if (true === array_key_exists('clientid', $this->qs)) {
							// do some auth check here...
							$client = $tools->getClient($this->qs['clientid']);
							$this->assignments['client'] = $client; 
							$this->assignments['feeds'] = $client->getFeeds();
							
						}
					}
				} elseif($loc->isHome() === false) {
					util::redirect('/');
				}
			}
		}
		
		$images = new core_admin_images($this->db, $this->qs);
		$this->assignments['coverimages'] = $images->getImages(); 
	}
	
	public function draw($ajax = false)
	{
		if (is_array($this->assignments) === true && count($this->assignments) > 0) {
			foreach ($this->assignments as $key => $assignment) {
				$this->tplBase->assign($key, $assignment);
			}
			if (true === isset($this->assignments['action'])) {
				$this->action = $this->assignments['action'];
			}
		}
		
		/*
		$sql = "SELECT 
		CONCAT('http://holidays.easyjet.com/dl.aspx?mode=FlightPlusHotel&depdate=' , DAY(raw.departure_date) , '/' , MONTH(raw.departure_date) , '/', YEAR(raw.departure_date) , '&nights=', raw.duration, '&adults=2&airport=', out_departure_airport_code, '&resort=', p.ResortID) as 'url',
		p.*, raw.*  FROM pm_custom_products_easyjet raw
		INNER JOIN pm_scrape_property_lookup lookup ON lookup.scrape_id=raw.property_id
		INNER JOIN Property p ON p.PropertyID=lookup.odst_id
		LIMIT 0,10";
		
		$result = $this->db->getQuery($sql);

		$this->tplBase->assign('example', $result);
		*/
		
		$this->tplBase->assign('content', $this->template);
		$this->tplBase->assign('domain', '');
		$this->tplBase->assign('current_url', '');
		$this->tplBase->assign('action', $this->action);
		echo('here');
		echo($this->user == null);
		
		$this->tplBase->assign('user', $this->user);

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
			if ($this->template == 'signup.tpl.html') {
				$this->base = $this->template;
			}
			
			$this->tplBase->display($this->base);
		}
	}
}

?>