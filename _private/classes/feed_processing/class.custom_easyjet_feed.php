<?php

class custom_easyjet_feed extends network_base
{
	private $name = 'Custom - EasyJet';
	private $fields = array();
	private $prefix;
	
	public function __construct($local_file = null)
	{
		global $db;
		
		$this->setFields();
		$fields = $this->getFields();
		$delete = "DELETE FROM pm_custom_products_easyjet";
		$db->changeQuery($delete);
		
		$insert = "INSERT INTO pm_custom_products_easyjet (id";
		foreach ($fields as $field) {
			if ($field != '') {
				$insert .= ",".$field;
			}	
		}
		$insert .= ') VALUES';
		
		echo 'Unzipping'."\n";
		$local_file = 'files/feeds/easyJetHolidays_DDfeed';
		$cmd = 'unzip '.$local_file.'.zip -d files/feeds';;
		exec($cmd);
		print "Converting from binary\n";
		$this->removeBOM($local_file.'.txt');
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
			foreach ($fields as $key => $field) {
				if ($field != '') {
					switch($field) {
						case 'property_id':
							$values .= ",".$db->queryParameter($data[$key], true);
							break;
						default:
							$values .= ",".$db->queryParameter($data[$key]);
					}
				}
			}
			$values .= "),"; 
			//if ($cycle == 10000) {
			if ($cycle == 10000) {
				$values = substr_replace($values,'',-1);
				$sql = $insert.$values;
				if(false === $db->changeQuery($sql)) {
					die("Error - see log\n\n".$sql);
				}
				unset($data);
				unset($key);
				unset($field);
				$cycle = 0;
				$values = '';
				break;
				
			}
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
		return $this->fields;
	}
	
	public function getPrefix()
	{
		return $this->prefix;
	}
	
	public function setFields() {
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