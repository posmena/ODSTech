<?php

class goldenfeeds_feed extends network_base
{
	private $name = 'Goldenfeeds Feed';
	public $feedid = '';
	private $fields = array();
	private $prefix;
	public $custom = array();
	private $local_file;
	private $full;
	public $num_products;
	
	public function __construct($local_file = null, $full = false, $feed_id = null)
	{
		global $db;
		$this->num_products = 0;
		$this->local_file = $local_file;
		$this->full = $full;
		$this->feedid = $feed_id;
	
	}
	
	public function pre_process()
	{
	echo("PRE PROCESS FEED");	
	
	}
	
	public function post_process($success)
	{
	echo("POST PROCESS FEED");
	if(false === $success)
	{
	echo("Error - did not update Kekloo succesfully");
	die();
	}
	
	if ( true === $this->full ) 
		{
		global $db;
		
		$products = $db->p20_products;
		
		$diff = 60 * 60 * 12 ; //12 hours in seconds
		$mongotime = New Mongodate(time()-$diff);
		$condition = array('last_updated' => array('$lt'=>$mongotime) );

		//$products->remove($condition);
		}
	}
	
	public function process()
	{
	
		try
				{
				
			if ( stripos($this->local_file,".xml") )
			{				
					$this->num_products = $this->parse_xml($this->local_file, $this->feedid);	
					}
			else if ( stripos($this->local_file,".csv") )
				{
					$this->csv_call($this->local_file, $this->full, ",");
					$this->num_products = $this->process_csvdata($this->feedid);	
					// this reads into into kelkoo_dump = need to process this now
				}
				
				
				}
			
		catch(Exception $e)
				{
				print("Error parsing " . $this->feedid . ", file " . $this->local_file . $e);
				}
	}

	function process_csvdata($feed_id)
	{
	echo("Process\n");
	
	// for each product in kelkoo_dump
	// update or add to p20_products
	global $db;
	$dc = 'dump_' . $feed_id;
	$dc  = $db->$dc;
	$dumpedProducts  = $dc->find();
	
	$feeds = $db->ot_feeds;
	$feed = $feeds->findOne(array('client' => $feed_id));
	
	
	$products = $db->p20_products;
	$i = 0;
	foreach($dumpedProducts as $product) {
			$i++;
			$product['_id'] = $feed_id . "_" . $product['productid'];
		    echo($product['_id']. '\n');
		    $product['feed_id'] = $feed_id;
			$product['program_name'] = $feed['feedname'];
			$product['last_updated'] = new MongoDate();
			$products->save($product);			
		}
		
		return $i;
	
	}
	
	function parse_xml($file, $feed_id)
	{
	//$file = "C:\\Users\\Mike\\Downloads\\custom-feed-linkshare-cathkid131-cath-kidston.xml\\custom-feed-linkshare-cathkid131-cath-kidston.xml";
		print("Parsing " . $file . "\n");
		
		global $db;
	
		$collection = $db->ot_feeds;
		$doc = $collection->findOne(array("client" => $feed_id));
		$feed_name = $doc['feedname'];
		print($doc['feedname'] . "\n");
		
		$collection = $db->p20_products;
		
		$reader = new XMLReader();
		$reader->open($file,"UTF-8");

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
						if( $node instanceof DOMNode )
							{
							$domNode = $dom->importNode($node,true);
							$dom->appendChild($domNode);
							$product = simplexml_import_dom($domNode);
							$dbproduct= array();
							$dbproduct['image_thumbnail'] = "";
							$dbproduct['categories'] = array();
							
							$dbproduct['_id'] = 				 $feed_id . '_' . $product->product_number;
							$dbproduct['feed_id'] =			     $feed_id;
							$dbproduct['program_name'] =	$feed_name;
							
							foreach( $product as $field => $value)
								{
								switch( (string)$field )
									{
									 case "thumbnail_image_URL":
										$dbproduct["image_thumbnail"] = (string)$value;
									 break;
									 
									  case "large_image_URL":
										$dbproduct["image_url"] = (string)$value;
									 break;
									 
									   case "url":
										$dbproduct["deeplink"] = (string)$value;
									 break;
									 
									   case "category":
									    print((string)$value . "\n");
										$dbproduct["merchant_category"] = (string)$value;
										
									 break;
									 
									 
									 default:
										$dbproduct[(string)$field] = (string)$value;
									}									
							
								}
								
														
							$dbproduct['image_thumbnail'] = (false !== strpos($dbproduct['image_thumbnail'], 'http')) ? $dbproduct['image_thumbnail'] : $dbproduct['image_url'];
							
							$collection->save($dbproduct);
						
							$i++;
							}
					}
				}
			}
		}
		
		
		return $i;
	}
	
	public function doCustomEdits($data, $key, $field)
	{
	}
	
}

/*
 /usr/local/mongodb/bin/mongoexport -d odstech -c for --csv -f 'productcode','productid','productname','warehousecustom','productdescription','availability','freeshippingitem','google_product_type','productcondition','productmanufacturer','productfeatures','deeplink','imagethumbnail','imagelarge' -o files/hostedfeeds/forthill/forthillall.csv
 */