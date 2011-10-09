<?php

class custom_jtspas_feed extends network_base
{
	private $name = 'Custom - JTSpas';
	private $fields = array();
	private $prefix;
	
	public function __construct($local_file = null)
	{
		global $db;
		$conn = new Mongo('localhost');
		// access database
		$mdb = $conn->odstech;
		// access collection
  		$collection = $mdb->jtspas;
		//$response = $collection->drop();
		
		// set up the indices
		//$collection->ensureIndex(array('out_departure_airport_code' =>  1));
		//$collection->ensureIndex(array('out_destination_airport_code' =>  1));
		//$collection->ensureIndex(array('mongo_departure_date' =>  1));
		$collection->ensureIndex(array('id' =>  1));
		//$collection->ensureIndex(array('region' =>  1));
		//$collection->ensureIndex(array('package_id' => 1) , array('unique' => true));
		
		//$collection->ensureIndex(array('out_departure_airport_code' =>  1, 'out_destination_airport_code' =>  1));
		//$collection->ensureIndex(array('out_departure_airport_code' =>  1, 'out_destination_airport_code' =>  1, 'cost' =>  1));
		//$collection->ensureIndex(array('out_departure_airport_code' =>  1, 'out_destination_airport_code' =>  1, 'mongo_departure_date' =>  1, 'cost' =>  1));
		//$collection->ensureIndex(array('out_departure_airport_code' =>  1, 'out_destination_airport_code' =>  1, 'mongo_departure_date' =>  1, 'cost' =>  1, 'region' => 1));
		
		// search widget
		//$collection->ensureIndex(array('out_departure_airport_code' => 1, 'region' => 1, 'mongo_departure_date' => 1));
		
		print_r($response);
		$this->setFields();
		$fields = $this->getFields();
		//$properties = $mdb->properties_raw->find();
		//$properties = $db->getQuery('SELECT p.*, pl.scrape_id FROM Property p INNER JOIN pm_scrape_property_lookup pl ON pl.odst_id=p.PropertyID');
		
		//$nwProperties = array();
		//foreach($properties as $property) {
		//	$nwProperties[$property['propertyid']] = $property;
		//}
		
		//unset($properties);
		//print "Property Count: ". count($nwProperties)."\n";
		
		// Load ODST Property Table (and more if required);
		//$sql = 'SELECT * FROM Property LIMIT 0,1';
		//$otFieldData = $db->getFields($sql);
		

		//echo 'Unzipping'."\n";
		$local_file = 'files/feeds/feed3';
		$timeStart  = time();
		/*
		//$cmd = 'unzip '.$local_file.'.zip -d files/feeds';;
		//exec($cmd);
		//print "Converting from binary\n";
		$this->removeBOM($local_file.'.txt');
		//	 $local_file= str_replace('.zip', '', $local_file); 
		print "Inserting into DB\n";
		*/
		$handle     = fopen($local_file, 'r');
		$comma      = "\t";
		
		$i          = 0;
		$missed     = 0;

		while ($data = fgetcsv($handle, null, $comma)) {
			$i++;
			if($i == 1) {
				continue;
			}
			// check we split on the comma
			if (count($data) == 1) {
				$data = explode($comma, $data[0]);
			}
			
			// skip blank lines
			if (count($data) < 2) {
				echo 'Column mismatch around line: '.$i."\n";
				continue;
			}
			
			
			$item = array();
			foreach ($fields as $key => $field) {
				if ($field != '') {

					if (strstr($field, 'product_type')) {
						$item['category'] = $data[$key];
						$parts            = explode(",", $data[$key]);
						$parts            = array_reverse($parts);
						$category         = implode(' > ', $parts);

						$item['webgains_category'] = $category;
						$item['nextag_category']   = $category;
					}
					if (strstr($field, 'shipping')) {

						$parts = explode('::UK', $data[$key],2);
						if(true === array_key_exists(1, $parts)) {
							$parts2                   = explode(',::', $parts[1]);
							$deliver                  = 'UK'.$parts2[0];
							$deliverParts             = explode(':', $deliver);
							$deliverPrice             = $deliverParts[1];
							$item['shipping_uk']      = $deliver;
							$item['shipping_cost_uk'] = $deliverPrice;
						}
					}

					if ($field == 'link') {
						$item['deeplink'] = $data[$key];

						$item['nextag_deeplink'] = $item['deeplink'] . '?utm_source=Nextag&utm_medium=cost-per-click';
						$item['google_deeplink'] = $item['deeplink'] . '?utm_source=googlebase&utm_medium=Free_clicks';
						$item['webgains_deeplink'] = $item['deeplink'] . '?utm_source=google&utm_medium=CPA';
						$item['dooyoo_deeplink'] = $item['deeplink'] . '?utm_source=leguide&utm_medium=cost-per-impression';

					}

					$item['warranty'] = 1;
					$item['availability'] = 'In Stock';

					$item[$field] = $data[$key];
				}

			}
			
			$item['_id'] = $item['id'];
  			$collection->save($item);
			
			if (false === array_key_exists('_id', $item)) {
				print 'Error saving item: '.$i."\n";
				break;
			}
			//echo 'Inserted document with ID: ' . $item['_id']."\n";
			unset($data);
			unset($key);
			unset($field);
			unset($item);
			//if ($i == 10000) break;
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
							  'id',
							  'title',
							  'link',
							  'price',
							  'description',
							  'condition',
							  'shipping',
							  'shipping_weight',
							  'gtin',
							  'brand',
							  'mpn',
							  'item_group_id',
							  'colour',
							  'material',
							  'pattern',
							  'size',
							  'image_link',
							  'additional_image_link',
							  'product_type',
							  'google_product_category',
							  'quantity',
							  'availability',
							  'expiration_date');
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
 /usr/local/mongodb/bin/mongoexport -d odstech -c jtspas --csv -f 'id','title','link','price','description','condition','shipping','shipping_weight','gtin','brand','mpn','image_link','product_type','quantity','availability','expiration_date','webgains_category','shipping_uk','shipping_cost_uk' -o files/hostedfiles/jtspas/jtspasall.csv
 */