<?php

class custom_forthillhome_feed extends network_base
{
	private $name = 'Custom -Forthill Home';
	public $feedid = 'forthillhome';
	private $fields = array();
	private $prefix;
	public $custom = array();

	public function __construct($local_file = null, $full = false)
	{
		global $db;

		$this->xml_call($local_file, $full);
	}

	public function doCustomEdits($data, $key, $field)
	{
		if ($field == 'productid') {
			$productId = $this->xmlMapping['productid'];
			$this->custom['deeplink'] = 'http://www.forthillhome.co.uk/product-p/' . strtoupper($data->$productId) . '.htm';
			$this->custom['image_thumbnail'] = 'http://www.forthillhome.co.uk/v/vspfiles/photos/' . strtoupper($data->$productId) . '-1.jpg';
			$this->custom['image_large'] = 'http://www.forthillhome.co.uk/v/vspfiles/photos/' . strtoupper($data->$productId) . '-2T.jpg';
		}	

		if ($field == 'description') {
			$this->custom['deeplink'] = strip_tags($data[$key]);
		}
	}
	
	public function meh($local_file = null, $full = false)
	{
		global $db;
		$conn = new Mongo('localhost');
		// access database
		$mdb = $conn->odstech;
		// access collection
  		$collection = $mdb->forthillhome;
		//$response = $collection->drop();
		
		$this->setFields();
		$fields = $this->getFields();
		//$properties = $mdb->properties_raw->find();
		//$properties = $db->getQuery('SELECT p.*, pl.scrape_id FROM Property p INNER JOIN pm_scrape_property_lookup pl ON pl.odst_id=p.PropertyID');

		
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
	
	/*
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
	*/
	

}

/*
 /usr/local/mongodb/bin/mongoexport -d odstech -c for --csv -f 'productcode','productid','productname','warehousecustom','productdescription','availability','freeshippingitem','google_product_type','productcondition','productmanufacturer','productfeatures','deeplink','imagethumbnail','imagelarge' -o files/hostedfeeds/forthill/forthillall.csv
 */