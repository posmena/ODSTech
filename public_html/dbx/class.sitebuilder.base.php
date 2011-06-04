<?php

class sitebuilder
{
	private $db;
	private $template = 'home.tpl.html';
	private $ms;
	private $qs;
	private $user;
	private $personal_info;
	private $save_result = array('message', false);
	private $step;
	private $selected_site;

	public function __construct()
	{
		$this->db = new database;
		$this->ms = new membership($this->db);
		$this->fn = new sitebuilder_functions($this->db);
		$this->qs = $_GET;
		foreach($_POST as $key => $value)
		{
			$this->qs[$key] = $value;
		}

		$this->db->connection("chops_compare");
		$this->tplBase = new Template('sitebuilder');
	}

	public function direct()
	{
		switch($this->qs['loc'])
		{
			case "logout":
			{
				Session_Destroy();
				header("Location: /");
				break;
			}
			case "login":
			{
				$this->template = 'login.tpl.html';
				if($this->qs['login'])
				{
					if($this->ms->login($this->qs['email'], $this->qs['password']))
					{
						header("Location: /manage");
					}
					else
					{
						echo "Not Auth'd";
					}
				}

				break;
			}
			case "manage":
			{
				$this->template = 'manage-home.tpl.html';
				$this->user = $this->ms->authorise();
				if(!$this->user)
				{
					header("Location: /login");
					break;
				}

				// get sites
				$this->sites = $this->fn->get_sites($this->user);

				$this->step = $this->qs['step'];				
				
								
				if(!$_SESSION['selected_site_id'])
				{
					if($this->step == 'websites' || $this->step == '') 
					{
						//$this->selected_site = $this->sites[0];
					}
					else
					{
						header("Location: /manage/websites");
					}
				}
				else
				{
					$this->selected_site = $this->sites[$_SESSION['selected_site_id']];
				}


				switch($this->step)
				{
					case "personal":
					{
						if($this->qs['save_personal_settings'])
						{
							if($this->fn->save_user_info($this->qs['personal_info'], $this->user))
							{
								$this->save_result['message'] = 'Success - Your personal settings have been saved.';
								$this->save_result['success'] = true;
							}
							else
							{
								$this->save_result['message'] = 'Sorry - we were unable to save at this time.';
								$this->save_result['success'] = false;
							}
						}
						$this->template = 'personal-settings.tpl.html';
						$this->personal_info = $this->fn->get_user_info($this->user);
						break;
					}
					case "websites":
					{
						if($this->qs['act'] == 'select' && ctype_digit($this->qs['site_id']))
						{
							$_SESSION['selected_site_id'] = $this->qs['site_id'];
							$this->selected_site = $this->sites[$_SESSION['selected_site_id']];
							header("Location: /manage/websites");
						}
						$this->template = 'website-list.tpl.html';
						
						break;
					}
					case "ebay":
					{
						
						if($this->qs['func'] == 'save_ebay_settings')
						{
							if($this->fn->save_ebay_settings($this->qs['ebay_sort'], $this->selected_site['id']))
							{
								$this->save_result['message'] = 'Success - Your eBay settings have been saved.';
								$this->save_result['success'] = true;
							}
							else
							{
								$this->save_result['message'] = 'Sorry - we were unable to save at this time.';
								$this->save_result['success'] = false;
							}
						}

						if($this->qs['type'] == 'ajax')
						{
							die();
						}
						$this->step = 'ebay';
						$this->template = 'ebay-settings.tpl.html';
						break;
					}
					default:
					{
						$this->step = 'home';
						$this->template = 'manage-home.tpl.html';
					}
				}
				break;
			}
			default:
			{
				$this->template = 'home.tpl.html';
			}
		}
	}

	public function draw($ajax = false)
	{
		$this->tplBase->assign('selected_site', $this->selected_site);
		$this->tplBase->assign('sitelist', $this->sites);
		$this->tplBase->assign('step', $this->step);
		$this->tplBase->assign('save_result', $this->save_result);
		$this->tplBase->assign('user', $this->user);
		$this->tplBase->assign('personal_info', $this->personal_info);
		$this->tplBase->assign('content', $this->template);
		$this->tplBase->assign('domain', '');
		$this->tplBase->assign('current_url', '');
						

		if($ajax)
		{
			$this->tplBase->display($this->template);
		}
		else
		{
			$this->tplBase->display('sitebuilder.tpl.html');
		}
	}
}

?>