<?php

class network_base implements network
{
	private $mapping = array();
	public $xmlMapping = array();
	function __construct($local_file = null, $full = false, $feed_id = null)
	{
	}

	function xml_call($local_file, $full)
	{
		global $db;
		// access collection
		$otc = 'ot_' . $this->feedid;
		$otc = $db->$otc;

		$dc = 'dump_' . $this->feedid;
		$dc  = $db->$dc;

		$fc = 'live_' . $this->feedid;
		$fc  = $db->$fc;

		$fic = 'field_columns_' . $this->feedid;
		$fic = $db->$fic;

		if (true === $full) {
			$otc->drop();
			$otc->ensureIndex(array('id' =>  1));
			$dc->drop();
			$dc->ensureIndex(array('id' =>  1));
		} else
		{
			$dc->drop();
			$dc->ensureIndex(array('id' =>  1));
		}
  		
		//$response = $collection->drop();
		
		// set up the indices
		

		$timeStart  = time();

		$reader = new XMLReader();
		$reader->open($local_file);


		$i = 0;
		$u = 0;
		while ($reader->read())
		{
			switch ($reader->nodeType)
			{
				// Check that this line is an element, rather than a declartion or a comment.
				case (XMLREADER::ELEMENT):
				{
					// We only care if the element is a product
					if ($reader->localName == 'Products')
					{
						$i++;
						$node = $reader->expand();
						$dom = new DomDocument();
						$domNode = $dom->importNode($node,true);
						$dom->appendChild($domNode);
						$product = simplexml_import_dom($domNode);
						
						if ($i == 1) {
							$fic->drop();
							foreach ($product as $field => $value) {
								$field = trim($field);
								$mField['id']        = $field;
								$mField['fieldname'] = $field;
								$fic->save($mField);
								unset($mField);
							}

							$fields = $this->getFields();
							if (count($fields) == 0) {
								print "No field mapping found\n";
								exit;
								break;
							}

							$this->xmlMapping = array_flip($this->mapping);
						}
												
						$item = array();
						$this->custom = array();
						foreach ($fields['standard'] as $key => $field) {
							if ($field != '' && $field != 'unmapped') {
								$xmlField = $this->xmlMapping[$field];
								$item[$field] = (string) $product->$xmlField;
								$this->doCustomEdits($product, $key, $field);
							}
						}
						
						if (true === isset($fields['extra'])) {
							foreach ($fields['extra'] as $otField => $categories) {
								foreach($categories as $categoryName => $contents) {
									switch ($contents['type']) {
										case "new": 
										{
											$item[$categoryName] = $contents['value'];
											break;
										}
										case "custom":
										{
											$item[$categoryName] = $this->custom[$contents['value']];
											break;
										}
										case "copy":
										{
											$item[$categoryName] = $item[$otField];
											break;
										}
										default:
										case 'append':
										{
											$item[$categoryName] = $item[$otField] . $contents['value'];
											break;
										}
									}
								}
							}
						}

						$item['_id'] = $item['productid'];	
						if (true === $full) {
							$otc->save($item);
							$dc->save($item);
						} else {
							$dc->save($item);
						}
			  			
						if (false === array_key_exists('_id', $item)) {
							print 'Error saving item: '.$i."\n";
							break;
						}
						//echo 'Inserted document with ID: ' . $item['_id']."\n";
						unset($data);
						unset($key);
						unset($field);
						unset($item);
						$u++;
					}
				}
			}
		}

		$timeEnd = time();
		echo 'Time taken to parse file ('.$i.'(' . $u .') lines): ' . ($timeEnd-$timeStart) . 's'."\n";
		
		if ($u > 0) {
			// we now have either a full dump, or a price update. create the merged collection
			
			$dynamicFeed = $dc->find();

			// nasty hack to ensure all 'extra' fields are updated on live. as the may not have been in the previous 'full' update..

			foreach ($dynamicFeed as $item) {
				$staticFeed  = $otc->find(array('_id' => $item['_id']));
				foreach ($staticFeed as $live_item) {
					/*
					foreach ($fields['extra'] as $otField => $categories) {
						foreach($categories as $categoryName => $contents) {
							$live_item[$categoryName] = $item[$categoryName];
						}
					}
					*/
					$live_item['price'] = $item['price'];
					$fc->save($live_item);
					unset($live_item);
				}
			}

			print 'Prices updated.';
		}
	}

	function csv_call($local_file, $full, $comma = "\t")
	{
		global $db;
		// access collection
		$otc = 'ot_' . $this->feedid;
		$otc = $db->$otc;

		$dc = 'dump_' . $this->feedid;
		$dc  = $db->$dc;

		$fc = 'live_' . $this->feedid;
		$fc  = $db->$fc;

		$fic = 'field_columns_' . $this->feedid;
		$fic = $db->$fic;

		if (true === $full) {
			$otc->drop();
			$otc->ensureIndex(array('id' =>  1));
			$dc->drop();
			$dc->ensureIndex(array('id' =>  1));
		} else
		{
			$dc->drop();
			$dc->ensureIndex(array('id' =>  1));
		}
  		
		//$response = $collection->drop();
		
		// set up the indices
		

		$timeStart  = time();

		$handle     = fopen($local_file, 'r');
		
		
		$i          = 0;
		$u 			= 0;
		$missed     = 0;

		while ($data = fgetcsv($handle, null, $comma)) {
			$i++;
			
			// check we split on the comma
			if (count($data) == 1) {
				$data = explode($comma, $data[0]);
			}
			
			// skip blank lines
			if (count($data) < 2) {
				echo 'Column mismatch around line: '.$i."\n";
				continue;
			}
			
			if($i == 1) {
				$fic->drop();

				foreach ($data as $key => $fieldname) {
					if ($key == 0) {
						if (get_called_class() == 'custom_jtspas_feed') {
							$fieldname = 'id';
						}	
					}

					$fieldname = trim($fieldname);
					
					$mField['id']        = $key;
					$mField['fieldname'] = $fieldname;
					$fic->save($mField);
					unset($mField);
				}
				continue;
			}

			
			$fields = $this->getFields();
			if (count($fields) == 0) {
				print "No field mapping found\n";
				break;
			}

			$item = array();
			$this->custom = array();
			
			foreach ($fields['standard'] as $key => $field) {
				if ($field != '') {
					$isUTF8 = mb_detect_encoding($data[$key], 'UTF-8', true); // false
					if (false === $isUTF8) {
						$data[$key] = mb_convert_encoding($data[$key], "UTF-8");
					}
					$item[$field] = $data[$key];
					$this->doCustomEdits($data, $key, $field);
				}
			}

			if (true === isset($fields['extra']) && count($fields['extra']) > 0) {
				foreach ($fields['extra'] as $otField => $categories) {
					foreach($categories as $categoryName => $contents) {
						switch ($contents['type']) {
							case "new": 
							{
								$item[$categoryName] = $contents['value'];
								break;
							}
							case "custom":
							{
								$item[$categoryName] = $this->custom[$contents['value']];
								break;
							}
							case "copy":
							{
								$item[$categoryName] = $item[$otField];
								break;
							}
							default:
							case 'append':
							{
								$item[$categoryName] = $item[$otField] . $contents['value'];
								break;
							}
						}
					}
				}
			}
			
			$item['_id'] = $item['productid'];
			if (true === $full) {
				$otc->save($item);
				$dc->save($item);
			} else {
				$dc->save($item);
			}
  			
			if (false === array_key_exists('_id', $item)) {
				print 'Error saving item: '.$i."\n";
				break;
			}
			//echo 'Inserted document with ID: ' . $item['_id']."\n";
			unset($data);
			unset($key);
			unset($field);
			unset($item);
			$u++;
			//if ($i == 10000) break;
		}
		fclose($handle);
		$timeEnd = time();
		echo 'Time taken to parse file ('.$i.'(' . $u .') lines): ' . ($timeEnd-$timeStart) . 's'."\n";
		
		if ($u > 0) {
			// we now have either a full dump, or a price update. create the merged collection
			
			$dynamicFeed = $dc->find();

			// nasty hack to ensure all 'extra' fields are updated on live. as the may not have been in the previous 'full' update..

			foreach ($dynamicFeed as $item) {
				$staticFeed  = $otc->find(array('_id' => $item['_id']));
				foreach ($staticFeed as $live_item) {
					/*
					foreach ($fields['extra'] as $otField => $categories) {
						foreach($categories as $categoryName => $contents) {
							$live_item[$categoryName] = $item[$categoryName];
						}
					}
					*/
					$live_item['price'] = $item['price'];
					if (get_called_class() == 'custom_jtspas_feed') {
						$live_item['expiration_date']  = $item['expiration_date'];
						$live_item['shipping_cost_uk'] = $item['shipping_cost_uk'];
					}
					$fc->save($live_item);
					unset($live_item);
				}
			}

			print 'Prices updated.';
		}
	}
	
	function getFields()
	{
		global $db;
		
		$fc = 'field_mapping_' . $this->feedid;
		$fc     = $db->$fc;
		$cursor = $fc->find();		
		
		if (false === $cursor->hasNext()) {
			return array();
		}

		foreach($cursor as $field) {
			$this->mapping[$field['column']] = $field['ot_field'];
		}
		
		$fc = 'field_columns_' . $this->feedid;
		$fc     = $db->$fc;
		$cursor = $fc->find();
		$cursor->sort(array('id' => 1));
		
		if (false === $cursor->hasNext()) {
			return array();
		}

		foreach ($cursor as $field) {
			if (false === isset($this->mapping[$field['fieldname']])) {
				//print 'Unknown field: ' . $field['fieldname'] . "\n";
				$fields['standard'][] = $field['fieldname'];
			}
			else
				{
				//print 'Field: ' . $field['fieldname'] . "\n";
					$fields['standard'][] = $this->mapping[$field['fieldname']];
				}
				
		}

		$fc = 'field_extra_' . $this->feedid;
		$fc     = $db->$fc;
		$cursor = $fc->find();

		if (true === $cursor->hasNext()) {
			$x = 0;
			foreach ($cursor as $field) {
				$x++;
				if (isset($field['ot_field']) === false || $field['ot_field'] == '') {
					$field['ot_field'] = 'dummy' . $x;
				}

				$fields['extra'][$field['ot_field']][$field['fieldname']]['type'] =  $field['type'];
				$fields['extra'][$field['ot_field']][$field['fieldname']]['value'] =  $field['value'];
			}
		}
		return $fields;
	}
	
	function getName()
	{
	}
	
	function getPrefix()
	{
	}
	
	function updateFeedList($network_id)
	{
	}
	
	function addFeed($feed)
	{
	}
	
	function parse_xml($file, $feed_id)
	{
	}
	
	function insert_products($products)
	{
		global $db;
		
		$query = 'INSERT INTO pm_products (id,feed_id,ProductName,ProductPrice,ProductDescription,SummaryDescription, Gender,BrandName,RRP,ProductID,AffiliateURL,ImageURL,Category,SmallImageURL,LargeImageURL) VALUES ';
		$sql = $query.$products;
		$sql = substr_replace($sql,";",-1);
		if ($db->changeQuery($sql)) {
			return true;
			//print $product['product_name']."<br />";
		}
		else
		{
			print "Sorry. There was an error. Please try again\n";
			return false;
		}
	}

	public static function removeBOM($filename)
	{
		if (is_file($filename)) 
		{
			$handle = fopen($filename, "r+b");
		}
		else
		{
			print 'File does not exist';
			exit;
		}
		$bom2 = bin2hex(fread($handle, 2));//BOM can be 2 or 3 bytes
		rewind($handle);
		$bom3 = bin2hex(fread($handle, 3));
		if($bom3 == 'efbbbf') //UTF-8. could possibly use iconv for this as well, but leaving in place for backwards compatibility
		{
			$cmd = "tail -c +4 ".escapeshellcmd($filename)." > ".escapeshellcmd($filename).".tmp";
			exec($cmd);
			
			// remove old file and rename the temp file
			unlink($filename);
			$cmd = "mv ".escapeshellcmd($filename).".tmp ".escapeshellcmd($filename);
			exec($cmd);
        }
		elseif($bom2 == 'fffe' || $bom2 == 'feff') // UTF-16
		{
			$cmd = "iconv -f UTF-16 -t UTF-8 ".escapeshellcmd($filename)." > ".escapeshellcmd($filename).'.tmp';
			exec($cmd);
			
			// remove old file and rename the temp file
			unlink($filename);
			$cmd = "mv ".escapeshellcmd($filename).".tmp ".escapeshellcmd($filename);
			exec($cmd);
        }
		elseif($bom3 == '0000feff' || $bom2 == 'fffe0000') // UTF-32
		{
			$cmd = "iconv -f UTF-32 -t UTF-8 ".escapeshellcmd($filename)." > ".escapeshellcmd($filename).'.tmp';
			exec($cmd);
			
			// remove old file and rename the temp file
			unlink($filename);
			$cmd = "mv ".escapeshellcmd($filename).".tmp ".escapeshellcmd($filename);
			exec($cmd);
        }
        fclose($handle);
	}
}