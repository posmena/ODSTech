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
			if( filesize($local_file) > 50000 )
			{
			$this->meh($local_file, $full);
			}
			else
			{
			mail("support@odst.co.uk","Forthill Home Error","Unable to download file from server");
			}
	
			
		
	}

	public function doCustomEdits($data, $key, $field)
	{
		if ($field == 'productid') {
			$productId = $this->xmlMapping['productid'];
			$this->custom['deeplink'] = 'http://www.forthillhome.com/product-p/' . strtoupper($data->$productId) . '.htm';
			$this->custom['image_thumbnail'] = 'http://www.forthillhome.com/v/vspfiles/photos/' . strtoupper($data->$productId) . '-1.jpg';
			$this->custom['image_large'] = 'http://www.forthillhome.com/v/vspfiles/photos/' . strtoupper($data->$productId) . '-2T.jpg';
			$description = $this->xmlMapping['description'];
			$this->custom['description'] = strip_tags($data->$description);
		}	
	}
	
	public function meh($local_file = null, $full = false)
	{
		global $db;
		$conn = new Mongo('localhost');
		// access database
		$mdb = $conn->odstech;
		// access collection
  		$collection = $mdb->live_forthillhome;
		$collection->update( array( "_id" => array("exists" => true)),array("updated" => false),array("multi" => true));
					 
		//$this->setFields();.
		$fields = $this->getFields();
		
		//$properties = $mdb->properties_raw->find();
		//$properties = $db->getQuery('SELECT p.*, pl.scrape_id FROM Property p INNER JOIN pm_scrape_property_lookup pl ON pl.odst_id=p.PropertyID');

		
		$timeStart  = time();

		print "Inserting into DB\n";
		print($local_file);
	
		$reader = new XMLReader();
		$reader->open($local_file,"utf-8");
		
		
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
									$item[strtolower($field)] = $html;
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
						
					

						$item['updated'] = true;
						$item['imagethumbnail'] = 'http://www.forthillhome.com/v/vspfiles/photos/' . strtoupper($item['productcode']) . '-1.jpg';
						$item['imagelarge'] = 'http://www.forthillhome.com/v/vspfiles/photos/' . strtoupper($item['productcode']) . '-2T.jpg';
						$item['deeplink'] = 'http://www.forthillhome.com/product-p/' . strtoupper($item['productcode']) . '.htm';
						$item['warranty'] = 1;

						$item['id'] = $item['productcode'];
						$item['mpn'] = $item['productcode'];
						
						$item['title'] = $item['productname'];
						$item['link'] = $item['deeplink'];
						
						if( $item['freeshippingitem'] == "Y")
							{
							$item['shipping'] =  '0';							
							}
						else
							{
							$item['shipping'] =  '35';							
							}
							
						if( $item['warehousebin'] != "1" && $item['warehousebin'] != "")  {
						$item['price'] = $item['warehousebin'];						
						}
							else	{						
						$item['price'] = $item['warehousecustom'];						
						}
						
						//$item['productdescription'] = preg_replace('/[^(\x20-\x7F)]*/','', $item['productdescription']);
						
						// convert back to original encoding
						$theData = mb_convert_encoding($item['productdescription'],'UTF-8','ISO-8859-1');
						$theData = str_replace("\xc2","",$theData);
						$theData = str_replace("\xc3","",$theData);
					
						$theData = strip_tags($theData);
						// convert to html entities
						$theData = htmlentities($theData);
						
						$theData = mb_convert_encoding($item['productdescription'],'ISO-8859-1','UTF-8');
						
						//$item['productdescription'] = str_replace("\xC3", "", $item['productdescription']);
						//$item['productdescription'] = str_replace("\x82", "", $item['productdescription']);
						
						$item['productdescription'] = $theData;
						$item['description'] =  $theData;
						$item['condition'] = $item['productcondition'];
						$item['image_link'] = $item['imagelarge'];
						$item['category'] = $item['google_product_type'];
						$item['product_type'] = $item['google_product_type'];
						$item['quantity'] = 1;
						$item['availability'] = 'In Stock';

						$existingitem = $collection->findOne(array("id" => $item['productcode']));
						if( null != $existingitem) 
							{
							$item['brand'] = $existingitem['brand'];
							$item['_id'] = $existingitem['_id'];
							}
						
						try{
						$collection->save($item);
						}
						catch( Exception $e)
							{
							echo 'Caught exception: ',  $e->getMessage(), "\n";
							}
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
		if( $i > 100 )
			{
				//only removed if feed downloaded 
				$collection->remove( array( "updated" => false ) ); 
			}
				// tidy up
				$collection->remove( array( "description" => "" ) ); 
				$collection->remove( array( "description" => null ) ); 
				$collection->remove( array( "deeplink" => "" ) ); 
				$collection->remove( array( "title" => "" ) ); 
				$collection->remove( array( "price" => "" ) ); 
				$collection->remove( array( "price" => null ) ); 
				
				$collection->update( array( "condition" => "" ), array ( "condition" => "New"), array("multi" => true) ); 
				$collection->update( array( "condition" => null ), array ( "condition" => "New"), array("multi" => true) );
			
		 
		
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
							  'WarehouseBin',							  
							  'ProductDescription',
							  'Availability',
							  'FreeShippingItem',
							  'Google_Product_Type',
							  'Google_Product_Category',
							  'ProductCondition',
							  'ProductManufacturer',
							  'ProductFeatures',
							  'UPC_Code');
		return $this->fields;
	}
	
	

}

/*
 /usr/local/mongodb/bin/mongoexport -d odstech -c for --csv -f 'productcode','productid','productname','warehousecustom','productdescription','availability','freeshippingitem','google_product_type','productcondition','productmanufacturer','productfeatures','deeplink','imagethumbnail','imagelarge' -o files/hostedfeeds/forthill/forthillall.csv
 */