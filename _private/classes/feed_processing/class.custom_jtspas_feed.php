<?php

class custom_jtspas_feed extends network_base
{
	private $name = 'Custom - JTSpas';
	public $feedid = 'jtspas';
	private $fields = array();
	private $prefix;
	public $custom = array();
	
	public function __construct($local_file = null, $full = false)
	{
		global $db;

		if( filesize($local_file) > 50000 )
			{
			echo("Processing file\n");
			$this->csv_call($local_file, $full);
			
			echo("Cleaning up\n");
			$conn = new Mongo('localhost');
			// access database
			$mdb = $conn->odstech;
			// access collection
			$collection = $mdb->dump_jtspas;
			
			$products = $collection->find();
			
			foreach($products as $product)
				{
				// convert back to original encoding
				$theData = mb_convert_encoding($product['description'],'ISO-8859-1','UTF-8');
				
				$theData = strip_tags($theData);
				// convert to html entities
				$theData = htmlentities($theData);
				
				$product['description'] = $theData;
				$collection->save($product);
				}
			}
		else
			{
			mail("support@odst.co.uk","JT Spas Error","Unable to download file from server");
			}
			
	}

	public function doCustomEdits($data, $key, $field)
	{
		// Custom edits for JtSpas
		if ($field == 'category') {
			
			$item['category'] = $data[$key];
			$parts            = explode(",", $data[$key]);
			$parts            = array_reverse($parts);
			$category         = implode(' > ', $parts);
			$this->custom['category'] = $category;
		}

		if ($field == 'shipping') {
			$parts = explode('::UK', $data[$key],2);
			if(true === array_key_exists(1, $parts)) {
				$parts2                   = explode(',::', $parts[1]);
				$deliver                  = 'UK'.$parts2[0];
				$deliverParts             = explode(':', $deliver);
				$deliverPrice             = $deliverParts[1];
				$this->custom['shipping_uk']      = $deliver;
				$this->custom['shipping_cost_uk'] = $deliverPrice;
			}
		}	
	}
}