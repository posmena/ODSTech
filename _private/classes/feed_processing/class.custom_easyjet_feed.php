<?php

class custom_easyjet_feed extends network_base
{
	private $name = 'Custom - EasyJet';
	private $fields = array();
	private $prefix;
	
	public function __construct($local_file = null)
	{
		global $db;
		$conn = new Mongo('localhost');
		// access database
		$mdb = $conn->odstech;
		// access collection
  		$collection = $mdb->packageschecksum;
		$response = $collection->drop();
		
		// set up the indices
		$collection->ensureIndex(array('out_departure_airport_code' =>  1));
		$collection->ensureIndex(array('out_destination_airport_code' =>  1));
		$collection->ensureIndex(array('cost' =>  1));
		$collection->ensureIndex(array('out_departure_airport_code' =>  1, 'out_destination_airport_code' =>  1));
		$collection->ensureIndex(array('out_departure_airport_code' =>  1, 'out_destination_airport_code' =>  1, 'cost' =>  1));
		
		print_r($response);
		$this->setFields();
		$fields = $this->getFields();
		
		$properties = $db->getQuery('SELECT p.*, pl.scrape_id FROM Property p INNER JOIN pm_scrape_property_lookup pl ON pl.odst_id=p.PropertyID');
		foreach($properties as $property) {
			$nwProperties[$property['scrape_id']] = $property;
		}
		unset($properties);
		print "Property Count: ". count($nwProperties)."\n";
		
		// Load ODST Property Table (and more if required);
		$sql = 'SELECT * FROM Property LIMIT 0,1';
		$otFieldData = $db->getFields($sql);
		
		/* need to enable but commented out for debugging
		echo 'Unzipping'."\n";
		$local_file = 'files/feeds/easyJetHolidays_DDfeed';
		$cmd = 'unzip '.$local_file.'.zip -d files/feeds';;
		exec($cmd);
		print "Converting from binary\n";
		$this->removeBOM($local_file.'.txt');
		 */
			 $local_file= str_replace('.zip', '', $local_file); 
		print "Inserting into DB\n";
		$handle     = fopen($local_file.'.txt', 'r');
		$comma      = ',';
		$i          = 0;
		$timeStart  = time();
		$cycle      = 0;
		$values     = '';
		while ($data = fgetcsv($handle, null, $comma)) {
			$i++;
			$cycle++;
			// check we split on the comma
			if (count($data) == 1) {
				$data = explode($comma, $data[0]);
			}
			
			// skip blank lines
			if (count($data) < 2) {
				echo 'Column mismatch around line: '.$i."\n";
				continue;
			}
			
			
			$values .= "(''";
			$item = array();
			foreach ($fields as $key => $field) {
				if ($field != '') {
					if ($field == 'cost') {
						$item[$field] = (int) $data[$key];
					} else {
						$item[$field] = $data[$key];
					}
					if (strstr($field, 'date')) {
						$item['mongo_' . $field] = new MongoDate(strtotime($data[$key]));
					}
				}
			}
			
			if (array_key_exists($item['property_id'], $nwProperties) === false) {
				// cannot process
				continue;
			}

			foreach($otFieldData as $extra_field) {
				$item[strtolower($extra_field)] = $nwProperties[$item['property_id']][$extra_field];
			}
			
			$item['search_deeplink'] = 'http://holidays.easyjet.com/dl.aspx?mode=FlightPlusHotel&depdate=' . 
										date('j/m/Y', strtotime($item['departure_date'])) . 
										'&nights=' . $item['duration'] . 
										'&adults=2&airport=' . $item['out_departure_airport_code'] . 
										'&resort=' . $item['resortid'] .
										'&property=' . $item['property_id'];
			$item['package_id'] = md5(serialize($item));
			
  			$collection->save($item);
			//echo 'Inserted document with ID: ' . $item['_id']."\n";
			unset($data);
			unset($key);
			unset($field);
			unset($item);
			if ($cycle == 100000) break;
		}
		fclose($handle);
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
							  'country',
							  'region',
							  'resort',
							  'duration',
							  'board',
							  'cost',
							  '',
							  '',
							  '',
							  'currency',
							  'departure_date',
							  'package_url',
							  '',
							  'hotel_name',
							  'rating',
							  'property_id',
							  'image_url',
							  'room_type',
							  'out_departure_airport_code',
							  'out_departure_airport_name',
							  'out_flight_departure_date',
							  '',
							  'out_destination_airport_name',
							  'out_destination_airport_code',
							  '',
							  'ret_departure_airport_code',
							  'ret_departure_airport_name',
							  'ret_flight_departure_date',
							  '',
							  'ret_destination_airport_name',
							  'ret_destination_airport_code');
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
	
	function parse_csv()
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