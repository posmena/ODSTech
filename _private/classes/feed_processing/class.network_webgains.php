<?php

class network_webgains extends network_base
{
	private static $name = 'Webgains';
	private static $fields = array();
	private static $prefix;
	
	public function __construct($local_file = null)
	{
		self::$fields = array(0 => array(
									'id'   => 'id',
									'name' => 'Webgains ID',
									'type' => 'text'),
							  1 => array(
							  		'id'   => 'username',
							  		'name' => 'Webgains Username',
									'type' => 'text'),							  		
							  2 => array(
							  		'id'   => 'password',
							  		'name' => 'Webgains Password',
									'type' => 'password'),							  		
							);
		self::$prefix = 'wg';
	}
	
	public function getName()
	{
		return self::$name;
	}
	
	public function getFields()
	{
		return self::$fields;
	}
	
	public function getPrefix()
	{
		return self::$prefix;
	}
	
	function updateFeedList($network_id)
	{
		global $db, $config;
		$url       = 'http://ws.webgains.com/aws.php';
		$user      = $config->getWebgainsUser();
		$pass      = $config->getWebgainsPass();
		$campaign  = $config->getWebgainsCampaign();
	
		$client    = new SoapClient($url);
		$merchants = $client->getProgramsWithMembershipStatus($user, $pass, $campaign);
		foreach($merchants as $merchant)
		{
			if ($merchant->programMembershipStatusCode == 10) {
				switch($merchant->programNetworkID) {
					case 1:
					{
						$country_id = 1;
						break;
					}
					case 2:
					{
						//FRANCE
						$country_id = 4;
						break;
					}
					case 3:
					{
						//Germany
						$country_id = 2;
						break;
					}
					case 4:
					{
						//netherland
						$country_id = 5;
						break;	
					}
					case 7:
					{
						//spain
						$country_id = 6;
						break;
					}
					case 8:
					{
						//usa
						$country_id = 3;
						break;
					}
					case 9:
					{
						//ireland
						$country_id = 7;
						break;
					}
					case 5:
					{
						$country_id = 8;
						break;
					}
					case 6:
					{
						$country_id = 9;
						break;
					}
	
				}
				
				$current_merchant = array();
				$current_merchant['network_id'] = $network_id;
				$current_merchant['country_id'] = $country_id;;
				$current_merchant['id'] = $merchant->programID;
				$current_merchant['displayurl'] = $merchant->programURL;
				$current_merchant['name'] = $merchant->programName;
				$current_merchant['description'] = $merchant->programDescription;
				$current_merchant['strapline'] = $merchant->programShortDescription;
				$current_merchant['logo'] = 'http://www.webgains.com/logos/showlogo.html?program_id='.$merchant->programID;
				$merchant_list[] = $current_merchant;
			}
		}

		$i=0;
		//echo count($merchant_list);
		$sql = "INSERT INTO pm_programs (id, network_id, country_id, name, merchant_ref, logo) VALUES ";
		foreach($merchant_list as $key => $merchant)
		{
			$values = sprintf("('', %d, %d, '%s', '%s', '%s'),",
					$merchant['network_id'],
					$merchant['country_id'],
					str_replace("'", "\'", $merchant['name']),
					$merchant['id'],
					$merchant['logo']
				);
			//print $values."\n";
			$sql .= $values;
			$i++;
		}
		$sql = substr_replace($sql,";",-1);
		$delete = "DELETE FROM pm_programs WHERE network_id=".$network_id;
		$db->changeQuery($delete);
		$db->changeQuery($sql);

		$sql = "UPDATE pm_networks SET last_update=".time()." WHERE class_name='".get_class($this)."'";
		$db->changeQuery($sql);

		$output = "Updated Webgains.\n";
		$output .= $i." webgains merchants inserted.\n";
		print $output;
	}

	public function addFeed($feed)
	{
		global $db;		
		
		$feed_url = 'http://content.webgains.com/affiliates/datafeed.html?action=download&campaign='.$db->getWebgainsCampaign().'&username='.$db->getWebgainsUser().'&password='.$db->getWebgainsPass().'&format=xml&zipformat=none&fields=extended&programs='.$feed->merchant_ref.'&allowedtags=&categories=all';
		$sql = "INSERT INTO pm_feeds (id, name, url, network_id, merchant_ref)
				VALUES ('','".$feed->name."','".$feed_url."',".$feed->network_id.",'".$feed->merchant_ref."')";
		$db->changeQuery($sql);
		
		return $feed->name.' added!';
		
	}
	
	function parse_xml($file, $feed_id)
	{
		global $db;

		$reader = new XMLReader();
		$reader->open($file);

		$sql = "DELETE FROM pm_products WHERE feed_id =".$feed_id;
		$db->changeQuery($sql);

		// Read each line of the XML
		$i=0;
		$query ='';
		while ($reader->read())
		{
			switch ($reader->nodeType)
			{
				// Check that this line is an element, rather than a declartion or a comment.
				case (XMLREADER::ELEMENT):
				{
					// We only care if the element is a product
					if ($reader->localName == 'product')
					{
						$node = $reader->expand();
						$dom = new DomDocument();
						$domNode = $dom->importNode($node,true);
						$dom->appendChild($domNode);
						$product = simplexml_import_dom($domNode);

						$product['feed_id'] =			$feed_id;
						$product['product_id'] =           $db->queryParameter($product->product_id);
						$product['product_name'] =         $db->queryParameter($product->product_name);
						$product['product_price'] =        $db->queryParameter($product->price, true);
						$product['product_description'] =  $db->queryParameter($product->description);
						$product['short_description'] =    $db->queryParameter($product->description);
						$product['gender'] =               $db->queryParameter($product->gender);
						$product['brand'] =                $db->queryParameter($product->brand);
						$product['rrp'] =                  $db->queryParameter($product->recommended_retail_price);
						$product['deeplink'] =             $db->queryParameter($product->deeplink);
						$product['image_url'] =            $db->queryParameter($product->image_url);
						$product['image_thumbnail'] =      (false !== strpos($product->image_thumbnail_url, 'http')) ? $db->queryParameter($product->image_thumbnail_url) : $product['image_url'];
						$product['category'] =             $db->queryParameter($product->merchant_category);
						
						if (isset($product['product_price']) === true && $product['product_price'] != '') {
							$query .= "('',".$product['feed_id'].",".$product['product_name'].",".$product['product_price'].",".$product['product_description'].",".$product['short_description'].",".$product['gender'].",".$product['brand'].",".$product['rrp'].",".$product['product_id'].",".$product['deeplink'].",".$product['image_url'].",".$product['category'].",".$product['image_thumbnail'].",".$product['image_url']."),";
						}

						$i++;

					}
				}
			}
		}
		if ($this->insert_products($query) === false) {
			return false;
		}
		return $i;
	}
}