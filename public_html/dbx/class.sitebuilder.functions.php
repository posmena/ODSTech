<?php

include '/home/chops/_private/class.amazon.php';

class sitebuilder_functions
{
	private $db;
	public $sites;

	public function __construct($db)
	{
		$this->db = $db;
		$this->amz = new amazon_api;
	}

	public function get_user_info($user)
	{
		$query = sprintf("SELECT su.id, su.email, su.password, su.forename, su.surname, sl.por_id, sl.webg_id, sl.afwin_id, sl.ebay, sl.amazon, sl.amazon_key, sl.amazon_api, sl.clickbank, su.merchant_id
							FROM stroke_users su
							INNER JOIN stroke_links sl ON sl.user_id = su.id
							WHERE su.id=%d", $user['id']);
		$result = $this->db->getQuery($query);
		return $result[0];
	}

	public function save_user_info($personal, $affiliate_ids, $user)
	{
		if(($personal['password'] != $personal['password_confirm']))
		{
			return false;
		}
		if($personal['password'] == '')
		{
			$personal['password'] = $user['password'];
		}
		$query = sprintf("UPDATE stroke_users SET email=%s, forename=%s, surname=%s, password=%s
							WHERE id=%d",
								$this->db->queryParameter($personal['email']),
								$this->db->queryParameter($personal['forename']),
								$this->db->queryParameter($personal['surname']),
								$this->db->queryParameter($personal['password']),
								$user['id']);
		if($this->db->changeQuery($query))
		{
			$sql = sprintf("UPDATE stroke_links SET ebay=%s, amazon=%s, amazon_key=%s, amazon_api=%s, clickbank=%s WHERE user_id=%d",
								$this->db->queryParameter($affiliate_ids['ebay']),
								$this->db->queryParameter($affiliate_ids['amazon']),
								$this->db->queryParameter($affiliate_ids['amazon_key']),
								$this->db->queryParameter($affiliate_ids['amazon_api']),
								$this->db->queryParameter($affiliate_ids['clickbank']),
								$user['id']);

			if($this->db->changeQuery($sql))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	public function get_sites($user)
	{
		$store_query = sprintf("SELECT id, name, url, skin, title, keywords, urchin, suppliers, description, active, products, clicks, currency_id as currency_id, auto_cat, template_settings, gwt, bing, home_bucket, twitter_username, twitter_password, bitly_username, bitly_password, auto_follow, follow_phrases FROM stores WHERE user_id=%d ORDER BY active DESC, id ASC", $this->db->queryParameter($user['id'], true));

		$results = $this->db->getQuery($store_query);
		foreach($results as $site)
		{
			// get twitter shat
			$phrases = unserialize($site['follow_phrases']);
			if(is_array($phrases))
			{
				$site['follow_phrases'] = implode(',', $phrases);
			}

			// get merchant shat
			$arrList = "'-1'";
			$arrSuppliers = split(',', $site['suppliers']);
			foreach($arrSuppliers as $key => $supplier_id)
			{
				if(strlen($supplier_id) > 0)
				{
					$arrList .= ",'".$supplier_id."'";
				}
			}

			$sql = sprintf("SELECT merchant_name FROM merchants WHERE merchant_id IN (%s)", $arrList);
			$result = $this->db->getQuery($sql);
			$site['selected_merchants'] = $result;

			// get group shat
			$site['groups'] = $this->get_groups($site['id']);
			$this->sites[$site['id']] = $site;
		}
	}

	public function get_group_types()
	{
		$sql = "SELECT sgt.id, sgt.type FROM stroke_group_types sgt";
		$results = $this->db->getQuery($sql);
		$group_types = array();
		if(is_array($results))
		{
			foreach($results as $group_type)
			{
				$group_types[$group_type['id']] = $group_type;
			}
		}
		return $group_types;
	}

	private function get_groups($site_id)
	{
		$cats = $this->get_categories($site_id);
		$sql = sprintf("SELECT sg.id, sg.left_right, sg.title, sgt.type, sg.stroke_group_type_id
						FROM stroke_groups sg
						INNER JOIN stroke_group_types sgt ON sgt.id=sg.stroke_group_type_id
						WHERE sg.site_id = %d", $site_id);
		$results = $this->db->getQuery($sql);
		$groups = array();
		if(is_array($results))
		{
			foreach($results as $group)
			{
				$group['cats'] = $cats[$group['id']];
				$groups[$group['stroke_group_type_id']][$group['id']] = $group;
			}
		}
		return $groups;
	}

	public function add_group($group, $site_id)
	{
		$sql = sprintf("INSERT INTO stroke_groups (title, stroke_group_type_id, template, left_right, site_id)
						SELECT %s, %d, template, 'left', %d
						FROM stroke_group_types sgt
						WHERE sgt.id=%d",
						$this->db->queryParameter($group['title']),
						$this->db->queryParameter($group['group_type'], true),
						$site_id,
						$this->db->queryParameter($group['group_type'], true));
		$this->db->changeQuery($sql);
		// check for pre-sitebuilder cats
		if($group['group_type']	 == 1)
		{
			$new_id_sql = sprintf("SELECT id
						FROM stroke_groups
						WHERE stroke_group_type_id=%d
						AND site_id=%d",
							$this->db->queryParameter($group['group_type'], true),
							$site_id
							);
			$result = $this->db->getQuery($new_id_sql);
			$new_id = $result[0]['id'];

			$sql = sprintf("UPDATE stroke_cats SET stroke_group_id=%d WHERE stroke_group_id=1 AND store_id=%d", $new_id, $site_id);
			$this->db->changeQuery($sql);
		}
		$this->sites[$site_id]['groups'] = $this->get_groups($site_id);
	}

	public function remove_group($group, $site_id)
	{
		if($this->sites[$site_id]['groups'][1][$group['group_id']]['stroke_group_type_id'] == 1)
		{
			$sql = sprintf("UPDATE stroke_cats SET stroke_group_id=1 WHERE stroke_group_id=%d AND store_id=%d", $group['group_id'], $site_id);
			//echo $sql;
			$this->db->changeQuery($sql);
		}
		$sql = sprintf("DELETE FROM stroke_groups WHERE id=%d AND site_id=%d", $group['group_id'], $site_id);
		$this->db->changeQuery($sql);
		$this->sites[$site_id]['groups'] = $this->get_groups($site_id);
	}

	private function get_categories($site_id)
	{
		$sql = sprintf("SELECT id, cat_name as title, cat_link as link, ext_url as rss_url, sort_order, stroke_group_id FROM stroke_cats WHERE store_id=%d ORDER BY sort_order", $site_id);
		$results = $this->db->getQuery($sql);
		$cats = array();
		if(is_array($results))
		{
			foreach($results as $cat)
			{
				$cats[$cat['stroke_group_id']][$cat['id']] = $cat;
			}
		}
		return $cats;
	}


	/*** updating the sort of of categories within a group ***/
	public function save_settings($order, $site_id)
	{
		$settings = str_replace('+','',str_replace('list_categories=','', $order));
		$sortby = explode(',', $settings);
		foreach($sortby as $key => $cat)
		{
			$cat_id = str_replace('listcat','',$cat);
			$sql = sprintf("UPDATE stroke_cats SET sort_order=%d WHERE id=%d AND store_id=%d", $key, $cat_id, $site_id);
			//echo $sql."<br />";
			$this->db->changeQuery($sql);
		}
	}

	/** updating the left,right,sort_order of groups (the drag and drop shit) ***/
	public function save_layout_settings($order, $site_id)
	{
		$left_right = explode('left_group=',$order['layout_settings']);
		$right = $left_right[0];
		$left = $left_right[1];

		$right_settings = str_replace('+','',str_replace('right_group=','', $right));
		$right_sortby = explode(',', $right_settings);

		$left_settings = str_replace('+','',str_replace('left_group=','', $left));
		$left_sortby = explode(',', $left_settings);

		$settings['left'] = $left_sortby;
		$settings['right'] = $right_sortby;

		foreach($settings as $left_right => $sortby)
		{
			foreach($sortby as $key => $cat)
			{
				$cat_id = str_replace('group','',$cat);
				$sql = sprintf("UPDATE stroke_groups SET sort_order=%d, left_right=%s
								WHERE id=%d AND site_id=%d",
									$key,
									$this->db->queryParameter($left_right),
									$cat_id,
									$site_id);
				//echo $sql."<br />";
				$this->db->changeQuery($sql);
			}
		}
	}

	public function remove_cat($cat_id, $site_id)
	{
		$sql = sprintf("DELETE FROM stroke_cats WHERE id=%d AND store_id=%d", $cat_id, $site_id);
		$this->db->changeQuery($sql);
	}
	/*
	public function add_ebay_cat($cat, $site_id)
	{
		$sql = sprintf("INSERT INTO stroke_cats (store_id, cat_name, ext_url, stroke_group_id, semi_auto) VALUES (%d,%s,%s,%d,'N')",
			$site_id,
			$this->db->queryParameter($cat['title']),
			$this->db->queryParameter($cat['rss_url']),
			$cat['stroke_group_id']
			);
		if($this->db->changeQuery($sql))
		{
			$this->sites[$site_id]['groups'] = $this->get_groups($site_id);
			return true;
		}
		else
		{
			return false;
		}
	}
	*/

	public function add_cat($cat, $site_id, $user)
	{
		switch($cat['stroke_group_type_id'])
		{
			case 3:
			{
				$ext_url = $cat['rss_url'];
				break;
			}
			// amazon
			case 4:
			{
				if(!$cat['search_term'])
				{
					$cat['search_term'] = $cat['title'];
				}
				if($cat['search_index'])
				{
					$SearchIndex = $cat['search_index'];
				}
				else
				{
					$SearchIndex = 'All';
				}

				switch($cat['locale'])
				{
					case "US":
					{
						$locale = 'com';
						break;
					}
					case "UK":
					default:
					{
						$locale = 'co.uk';
						break;
					}
				}

				$request = 'http://ecs.amazonaws.'.$locale;
				$request .= '/onca/xml?Service=AWSECommerceService&AWSAccessKeyId='.$user['amazon_key'];
				$request .= '&AssociateTag='.$user['amazon'];
				$request .= '&Version=2006-09-11&Operation=ItemSearch&ResponseGroup=Medium,Offers';
				$request .= '&SearchIndex='.$SearchIndex;
				$request .= '&Keywords='.str_replace(' ','+',$cat['search_term']);
				$request .= '&ItemPage=1';
				$ext_url = $request;
				break;
			}
		}

		$sql = sprintf("INSERT INTO stroke_cats (store_id, cat_name, ext_url, stroke_group_id, semi_auto) VALUES (%d,%s,%s,%d,'N')",
			$site_id,
			$this->db->queryParameter($cat['title']),
			$this->db->queryParameter($ext_url),
			$cat['stroke_group_id']
			);

		if($this->db->changeQuery($sql))
		{
			$this->sites[$site_id]['groups'] = $this->get_groups($site_id);
			return true;
		}
		else
		{
			return false;
		}
	}

	public function save_site_settings($site, $site_id, $user)
	{
		if($site['auto_follow'] != "on")
		{
			$auto_follow = "N";
		}
		else
		{
			$auto_follow = "Y";
		}
		$phrases = explode(',', $site['follow_phrases']);
		foreach($phrases as $phrase)
		{
			$trimmed[] = trim($phrase);
		}
		$follow_phrases = serialize($trimmed);
		$query = sprintf("UPDATE stores SET name=%s, title=%s, url=%s, keywords=%s, description=%s, urchin=%s, gwt=%s, bing=%s,  twitter_username=%s, twitter_password=%s, bitly_username=%s, bitly_password=%s, home_bucket=%d, auto_follow=%s, follow_phrases=%s WHERE id=%d",
								$this->db->queryParameter($site['name']),
								$this->db->queryParameter($site['title']),
								$this->db->queryParameter($site['url']),
								$this->db->queryParameter($site['keywords']),
								$this->db->queryParameter($site['meta_description']),
								$this->db->queryParameter($site['urchin']),
								$this->db->queryParameter($site['gwt']),
								$this->db->queryParameter($site['bing']),
								$this->db->queryParameter($site['twitter_username']),
								$this->db->queryParameter($site['twitter_password']),
								$this->db->queryParameter($site['bitly_username']),
								$this->db->queryParameter($site['bitly_password']),
								$this->db->queryParameter($site['home_bucket'], true),
								$this->db->queryParameter($auto_follow),
								$this->db->queryParameter($follow_phrases),
								$site_id);
		if($this->db->changeQuery($query))
		{
			$this->get_sites($user['id']);
			return true;
		}
		else
		{
			return false;
		}
	}

	/*
	public function get_products($refresh=false)
	{
		if(!$_SESSION['products'] || $refresh)
		{
			$sql = "SELECT p.id, m.affiliate_network as network_id, m.merchant_ref
					FROM products p
					INNER JOIN merchants m ON m.merchant_id = p.merchant_id";
			$result = $this->db->getQuery($sql);
			foreach($result as $product)
			{
				$products[$product['network_id']][$product['merchant_ref']][$product['id']] = $product;
			}
			$_SESSION['products'] = $products;
		}
		else
		{
			$products = $_SESSION['products'];
		}
		return $products;
	}
	*/

	public function search_cpa($term, $site)
	{
		//$products = $this->get_products(true);
		//print_r($products);
		//echo count($products);

		// merchant match type
		$sql = sprintf("SELECT m.merchant_id, mm.merchant_ref, mm.name as merchant_name, mm.display_url, mm.strapline, mm.description, mm.logo, mm.network_id, n.name as network_name, mm.product_count as products, 'Merchant' as match_type,
						ROUND(MATCH(mm.name,mm.strapline,mm.description,mm.category) AGAINST(%s),3) as relevancy
						FROM merchant_metadata mm
						INNER JOIN networks n ON n.id=mm.network_id
						LEFT JOIN merchants m ON (m.merchant_ref=mm.merchant_ref AND m.affiliate_network=mm.network_id)
						WHERE MATCH(mm.name,mm.strapline,mm.description,mm.category) AGAINST(%s) > 2",
							$this->db->queryParameter($term),
							$this->db->queryParameter($term));
		// product match type
		$sql .= sprintf(" UNION (SELECT m.merchant_id, m.merchant_ref, m.merchant_name, p.AffiliateURL as display_url, p.ProductName as strapline, p.ProductDescription as description, 'n/a' as logo, m.affiliate_network as network_id, n.name as network_name, m.products, 'Product' as match_type,
						ROUND(max(MATCH(ProductName, ProductDescription, BrandName, Category, ProductID) AGAINST (%s)),3) as relevancy
						FROM products p
						INNER JOIN merchants m ON m.merchant_id = p.merchant_id
						INNER JOIN networks n ON n.id=m.affiliate_network
						WHERE MATCH (ProductName, ProductDescription, BrandName, Category, ProductID) AGAINST (%s) > 5 AND m.currency_id=%d
						GROUP BY m.merchant_id)
						ORDER BY relevancy DESC",
							$this->db->queryParameter($term),
							$this->db->queryParameter($term),
							$this->db->queryParameter($site['currency_id'], true));

		//echo $sql;
		$result = $this->db->getQuery($sql);
		$locked_merchants = $site['suppliers'];
		foreach($result as $merchant)
		{
			if($merchant['network_id'] == 1)
			{
				$merchant['merchant_ref'] = substr($merchant['merchant_ref'], 1);
			}
			$findme = ",".$merchant['merchant_id'].",";
			$pos = strpos($site['suppliers'], $findme);
			if($pos === false)
			{
				$merchant['selected'] = 'N';
			}
			else
			{
				$merchant['selected'] = 'Y';
				$locked_merchants = str_replace(",".$merchant['merchant_id'], '', $locked_merchants);
			}
			$merchants[] = $merchant;
		}

		$return['merchants'] = $merchants;
		$return['locked'] = $locked_merchants;

		return $return;
	}

	public function save_selected_merchants($params, $site)
	{
		$sql = sprintf("UPDATE stores SET suppliers=%s WHERE id=%d",
							$this->db->queryParameter($params['selected_merchants']),
							$site['id']);

		$this->db->changeQuery($sql);

	}

	public function save_theme_settings($settings, $site_id, $user)
	{
		foreach($settings as $key => $value)
		{
			$t_settings[$key] = $value;
		}
		$compact_settings = serialize($t_settings);
		$sql = sprintf("UPDATE stores SET template_settings=%s WHERE id=%d AND user_id=%d",
					$this->db->queryParameter($compact_settings),
					$this->db->queryParameter($site_id, true),
					$this->db->queryParameter($user['id'], true));
		if($this->db->changeQuery($sql))
		{
			$this->get_sites($user['id']);
			return true;
		}
		else
		{
			return false;
		}

	}

	public function get_search_index_values($locale)
	{
		switch($locale)
		{
			/* USA
			    * SearchIndex: All
    * SearchIndex: Apparel
    * SearchIndex: Automotive
    * SearchIndex: Baby
    * SearchIndex: Beauty
    * SearchIndex: Blended
    * SearchIndex: Books
    * SearchIndex: Classical
    * SearchIndex: DigitalMusic
    * SearchIndex: MP3Downloads
    * SearchIndex: DVD
    * SearchIndex: Electronics
    * SearchIndex: GourmetFood
    * SearchIndex: HealthPersonalCare
    * SearchIndex: HomeGarden
    * SearchIndex: Industrial
    * SearchIndex: Jewelry
    * SearchIndex: KindleStore
    * SearchIndex: Kitchen
    * SearchIndex: Magazines
    * SearchIndex: Merchants
    * SearchIndex: Miscellaneous
    * SearchIndex: Music
    * SearchIndex: MusicalInstruments
    * SearchIndex: MusicTracks
    * SearchIndex: OfficeProducts
    * SearchIndex: OutdoorLiving
    * SearchIndex: PCHardware
    * SearchIndex: PetSupplies
    * SearchIndex: Photo
    * SearchIndex: Shoes
    * SearchIndex: SilverMerchants
    * SearchIndex: Software
    * SearchIndex: SportingGoods
    * SearchIndex: Tools
    * SearchIndex: Toys
    * SearchIndex: UnboxVideo
    * SearchIndex: VHS
    * SearchIndex: Video
    * SearchIndex: VideoGames
    * SearchIndex: Watches
    * SearchIndex: Wireless
    * SearchIndex: WirelessAccessories
			*/
			case "UK";
			{
				$values = array('All',
							'Automotive',
							'Blended',
							'Apparel',
							'Baby',
							'Beauty',
							'Books',
							'Classical',
							'DVD',
							'Electronics',
							'HealthPersonalCare',
							'HomeGarden',
							'Jewelry',
							'Kitchen',
							'Lighting',
							'MP3Downloads',
							'Music',
							'MusicTracks',
							'OfficeProducts',
							'OutdoorLiving',
							'Outlet',
							'Shoes',
							'Software',
							'SoftwareVideoGames',
							'Toys',
							'VHS',
							'Video',
							'VideoGames',
							'Watches');
				foreach($values as $value)
				{
					$search_index_values[$value] = $value;
				}
				break;
			}
		}
		return $search_index_values;
	}

}

?>