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
			$this->csv_call($local_file, $full);
			
			
			$conn = new Mongo('localhost');
			// access database
			$mdb = $conn->odstech;
			// access collection
			$collection = $mdb->live_jtspas;
			
			$products = $collection->find();
			
			foreach($products as $product)
				{
				$product['description'] = str_replace("\xC3", "", $product['description']);
				$product['description'] = str_replace("\x82", "", $product['description']);
				
				$product['description'] = strip_tags($product['description']);
				
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