<?php

class custom_forthillhome_feed extends network_base
{
	private $name = 'Custom -Forthill Home';
	private $fields = array();
	private $prefix;
	
	public function __construct($local_file = null)
	{
		global $db;
		$conn = new Mongo('localhost');
		// access database
		$mdb = $conn->odstech;
		// access collection
  		$collection = $mdb->forthillhome;
		//$response = $collection->drop();
		
		// set up the indices
		//$collection->ensureIndex(array('out_departure_airport_code' =>  1));
		//$collection->ensureIndex(array('out_destination_airport_code' =>  1));
		//$collection->ensureIndex(array('mongo_departure_date' =>  1));
		//$collection->ensureIndex(array('cost' =>  1));
		//$collection->ensureIndex(array('region' =>  1));
		//$collection->ensureIndex(array('package_id' => 1) , array('unique' => true));
		
		//$collection->ensureIndex(array('out_departure_airport_code' =>  1, 'out_destination_airport_code' =>  1));
		//$collection->ensureIndex(array('out_departure_airport_code' =>  1, 'out_destination_airport_code' =>  1, 'cost' =>  1));
		//$collection->ensureIndex(array('out_departure_airport_code' =>  1, 'out_destination_airport_code' =>  1, 'mongo_departure_date' =>  1, 'cost' =>  1));
		//$collection->ensureIndex(array('out_departure_airport_code' =>  1, 'out_destination_airport_code' =>  1, 'mongo_departure_date' =>  1, 'cost' =>  1, 'region' => 1));
		
		// search widget
		
		
		print_r($response);
		$this->setFields();
		$fields = $this->getFields();
		//$properties = $mdb->properties_raw->find();
		//$properties = $db->getQuery('SELECT p.*, pl.scrape_id FROM Property p INNER JOIN pm_scrape_property_lookup pl ON pl.odst_id=p.PropertyID');

		
		
		$local_file = 'files/feeds/feed2';
		$timeStart  = time();

		print "Inserting into DB\n";
		$reader = new XMLReader();
		$reader->open($local_file);
		
		$comma      = ',';
		$i          = 0;
		
		$values     = '';
		$missed     = 0;
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
						$node = $reader->expand();
						$dom = new DomDocument();
						$domNode = $dom->importNode($node,true);
						$dom->appendChild($domNode);
						$product = simplexml_import_dom($domNode);
						

						$item = array();
						foreach ($fields as $key => $field) {
							if ($field != '') {
								

								$item[strtolower($field)] = (string) $product->$field;
								if ($field == 'ProductDescription') {
									$html                     = (string) $product->$field;
									// $item[strtolower($field)] = htmlentities($html, ENT_QUOTES, 'UTF-8');
									$item[strtolower($field)] = strip_tags($html);
								}
								
								/*
								if ($field == 'cost') {
									$item[$field] = (int) $data[$key];
								} else {
									$item[$field] = $data[$key];
								}
								if (strstr($field, 'date')) {
									$item['mongo_' . $field] = new MongoDate(strtotime($data[$key]));
								}
								*/
							}
						}
						
						$item['_id'] = strtolower($item['productid']);
						$item['deeplink'] = 'http://www.forthillhome.co.uk/product-p/' . strtolower($item['productcode']) . '.htm';

						$item['nextag_deeplink'] = $item['deeplink'] . '?utm_source=Nextag&utm_medium=cost-per-click';
						$item['google_deeplink'] = $item['deeplink'] . '?utm_source=googlebase&utm_medium=Free_clicks';
						$item['webgains_deeplink'] = $item['deeplink'] . '?utm_source=google&utm_medium=CPA';
                        $item['dooyoo_deeplink'] = $item['deeplink'] . '?utm_source=leguide&utm_medium=cost-per-impression';



						$item['imagethumbnail'] = 'http://www.forthillhome.co.uk/v/vspfiles/photos/' . strtoupper($item['productcode']) . '-1.jpg';
						$item['imagelarge'] = 'http://www.forthillhome.co.uk/v/vspfiles/photos/' . strtoupper($item['productcode']) . '-2T.jpg';

						$item['warranty'] = 1;

						$item['id'] = $item['productcode'];
						$item['title'] = $item['productname'];
						$item['link'] = $item['deeplink'];
						$item['price'] = $item['warehousecustom'];
						$item['description'] = $item['productdescription'];
						$item['condition'] = $item['productcondition'];
						$item['image_link'] = $item['imagelarge'];
						$item['category'] = $item['google_product_type'];
						$item['quantity'] = 1;
						$item['availability'] = 'In Stock';


						$collection->save($item);
						if (false === array_key_exists('_id', $item)) {
							print 'Error saving item: '.$i."\n";
							continue;
						}

						$i++;
						//echo 'Inserted document with ID: ' . $item['_id']."\n";
						unset($data);
						unset($key);
						unset($field);
						unset($item);
					}
				}
			}
		}

		$timeEnd = time();
		echo 'Time taken to parse file ('.$i.' lines): ' . ($timeEnd-$timeStart) . 's'."\n";
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function getFields()
	{
		$this->fields = array(
							  'ProductCode',
							  'ProductID',
							  'ProductName',
							  'WarehouseCustom',
							  'ProductDescription',
							  'Availability',
							  'FreeShippingItem',
							  'Google_Product_Type',
							  'ProductCondition',
							  'ProductManufacturer',
							  'ProductFeatures');
		return $this->fields;
	}
	
	public function getPrefix()
	{
		return $this->prefix;
	}
	
	public function setFields() {
		//							  
	}
	
	public function addFeed($feed)
	{
		// @TODO
		//INSERT INTO `pm_feeds` VALUES(1, 'EasyJet Holidays', 'ftp.lowcostbeds.co.uk', 0, 0, '0', 'ftp', 'lowcostbeds.co.uk_easyjet', 'e4syj3t123', 'easyJetHolidays_DDfeed.zip', 'custom_easyjet_feed');
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

/*
 /usr/local/mongodb/bin/mongoexport -d odstech -c for --csv -f 'productcode','productid','productname','warehousecustom','productdescription','availability','freeshippingitem','google_product_type','productcondition','productmanufacturer','productfeatures','deeplink','imagethumbnail','imagelarge' -o files/hostedfeeds/forthill/forthillall.csv
 */