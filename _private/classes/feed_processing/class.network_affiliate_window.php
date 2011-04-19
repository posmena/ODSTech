<?php

if (class_exists('feed_processor') === false) {
	include 'classes/feed_processing/class.feed_processor.php';
}

class network_affiliate_window extends network_base
{
	private static $name = 'Affiliate Window';
	private static $fields = array();
	private static $prefix;
	
	public function __construct($local_file = null)
	{
		self::$fields = array(0 => array(
									'id'   => 'id',
									'name' => 'Affiliate Window ID',
									'type' => 'text'),
							  1 => array(
							  		'id'   => 'api_key',
							  		'name' => 'API Key',
									'type' => 'text'),						  		
							);
		self::$prefix = 'awin';
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
	
	public function updateFeedList($network_id)
	{
		global $db; 
		
		$meta_url = 'http://www.affiliatewindow.com/affiliates/shopwindow/datafeed_metadata.php?user=AW_ID&password=AW_SECRET&format=XML&filter=AW_FILTER&compression=';
		$meta_url = str_replace('AW_FILTER', 'SUBSCRIBED_ENABLED',
						str_replace('AW_SECRET', $db->getAwinAPIKey(),
							str_replace('AW_ID', $db->getAwinUser(), $meta_url)));
		$fp = new feed_processor();
		$contents = $fp->curl_get_file_contents($meta_url);
		
		if (!$myxml=simplexml_load_string($contents))
		{
            print "Error reading the XML file";
            return false;
        }
		
		$merchant_list = array();
		foreach($myxml as $key => $merchant)
		{
			$current_merchant = array();
			if($key == 'merchant')
			{
				foreach($merchant->attributes() as $m_key => $data)
				{
					$current_merchant[$m_key] = $data;
					//print $m_key.' --- '.$data."\n";
				}
				foreach($merchant as $m_key => $data)
				{
					$current_merchant[$m_key] = $data;
					//print $m_key.' --- '.$data."\n";
					if($m_key == 'categoryList')
					{
						foreach($data as $cl_key => $cl_data)
						{
							$current_merchant[$cl_key] = $cl_data;
							//print $cl_key.' --- '.$cl_data."\n";
						}
					}
					if($m_key == 'productfeed')
					{
						foreach($data as $pf_key => $pf_data)
						{
							$current_merchant[$pf_key] = $pf_data;
							//print $pf_key.' --- '.$pf_data."\n";
						}
					}

				}
				//print_r($current_merchant);

				//echo $sql;
				//print "\n========================================================\n\n\n";
				$merchant_list[] = $current_merchant;
			}
		}

		$i=0;
		$sql = "INSERT INTO pm_programs (network_id, country_id, name, merchant_ref, logo) VALUES ";
		foreach($merchant_list as $key => $merchant)
		{
			$values = sprintf("(%d, %d, '%s', '%s', '%s'),",
								$network_id,
								1, //awin only uk
								$merchant['name'],
								$merchant['id'],
								$merchant['logo']);
			$sql .= $values;
			$i++;
		}
		$sql = substr_replace($sql,";",-1);
		$delete = sprintf("DELETE FROM pm_programs WHERE network_id=%d", $network_id);
		$db->changeQuery($delete);
		$db->changeQuery($sql);
		
		$output = "Updated Affiliate Window.\n";
		$output .= $i." Affiliate Window merchants inserted.\n";
		
		print $output;
	}
	
	public function addFeed($feed)
	{
		global $db;
		
		$feed_url = sprintf('http://datafeeds.productserve.com/datafeed_products.php?user='.$db->getAwinUser().'&password='.$db->getAwinAPIKey().'&mid=%s&format=XML&compression=&dtd=1.0', $feed->merchant_ref);
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
                case (XMLREADER::ELEMENT):
                {
                    if ($reader->localName == 'product')
                    {
                        $node = $reader->expand();
                        $dom = new DomDocument();
                        $domNode = $dom->importNode($node,true);
                        $dom->appendChild($domNode);
                        $product = simplexml_import_dom($domNode);
						$product['feed_id'] =			$feed_id;
						$product['product_id'] =           $db->queryParameter($product->productID);
						$product['product_name'] =         $db->queryParameter($product->productname);
						$product['product_price'] =        $db->queryParameter($product->Price, true);
						$product['product_description'] =  $db->queryParameter(htmlentities(htmlspecialchars_decode($product->description), ENT_QUOTES, 'UTF-8'));
						$product['short_description']   =  $db->queryParameter('');
						$product['gender'] =               $db->queryParameter('');
						$product['brand'] =                $db->queryParameter($product->brand);
						$product['rrp'] =                  $db->queryParameter($product->Price, true);
						$product['deeplink'] =             $db->queryParameter($product->deepLink);
						$product['image_url'] =            $db->queryParameter($product->imageURL);
						$product['image_thumbnail'] =      $db->queryParameter($product->imageURL);
						$product['category'] =             $db->queryParameter($product->masterCategory);
						
						$query .= "('',".$product['feed_id'].",".$product['product_name'].",".$product['product_price'].",".$product['product_description'].",".$product['short_description'].",".$product['gender'].",".$product['brand'].",".$product['rrp'].",".$product['product_id'].",".$product['deeplink'].",".$product['image_url'].",".$product['category'].",".$product['image_thumbnail'].",".$product['image_url']."),";
                    	$i++;

                    }
                }
            }
 		}
		if($this->insert_products($query) === false) {
			return false;
		}
		return $i;
	}
}