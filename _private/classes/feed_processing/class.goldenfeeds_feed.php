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
	echo("Error - did not update feed succesfully");
	die();
	}
	
	if ( true === $this->full ) 
		{
		global $db;
		
		$products = $db->p20_products;
		
		//$diff = 60 * 60 * 12 ; //12 hours in seconds
		//$mongotime = New Mongodate(time()-$diff);
		
		$feed = $db->ot_feeds->findOne(array('client' => $this->feedid));
		$mongotime = $feed['last_updated'];
	
		$condition = array('last_updated' => array('$lt'=>$mongotime), 'feed_id' => $this->feedid );
		$x = $products->remove($condition);		
		print("removed $x products");
		}
		
	}
	
	public function process()
	{
	
	global $db;
	$feed = $db->ot_feeds->findOne(array('client' => $this->feedid));
	
		try
				{
				
			if ( $feed['format'] == 'xml' )
			{				
					$this->num_products = $this->parse_xml($this->local_file, $this->feedid);	
					}
			else if ( $feed['format'] == 'csv' )
				{
				print($this->local_file);
					$this->csv_call($this->local_file, $this->full, ",");
					$this->num_products = $this->process_csvdata($this->feedid);	
					// this reads into into kelkoo_dump = need to process this now
				}
			else if ( $feed['format'] == 'tab' )
			{
			print($this->local_file);
				$this->csv_call($this->local_file, $this->full );
				$this->num_products = $this->process_csvdata($this->feedid);	
				// this reads into into kelkoo_dump = need to process this now
			}
				
				print("85. Processed " . $this->num_products . " products\n");
				}
			
		catch(Exception $e)
				{
				print("Error parsing " . $this->feedid . ", file " . $this->local_file . $e);
				}
	}
	
	function GetTimeLeft($dt)
	{
	$now = new DateTime("now");
	
	$interval = $now->diff($dt);
	$days = $interval->format('%a');
	$hours = $interval->format('%h');
	$mins = $interval->format('%i');
	
	if( $days <= 1 )
		{
		if( $hours <= 1 )
			{
			return $mins . " mins";
			}
		return $hours . " hours";
		}
		
	return $days . " days";
	
	}
	
	function process_csvdata($feed_id)
	{
	echo("Process csvdata\n");
	
	// for each product in kelkoo_dump
	// update or add to p20_products
	global $db;
	$dc = 'dump_' . $feed_id;
	$dc  = $db->$dc;
	$dumpedProducts  = $dc->find();
	$dumpedProducts->immortal();
	$feeds = $db->ot_feeds;
	$feed = $feeds->findOne(array('client' => $feed_id));
	
	$products = $db->p20_products;
	$i = 0;
	foreach($dumpedProducts as $product) {
			$i++;
			if( isset($product['productid']) )
				{
				$product['_id'] = $feed_id . "_" . $product['productid'];
				}
			
			if( isset($product['Offers_ends_at']) )
				{
				//$product['Offers_ends_in'] =  $this->GetTimeLeft(new DateTime($product['Offers_ends_at']));
				//$product['Offers_ends_at'] =  new MongoDate(new DateTime($product['Offers_ends_at']));
				$d = new DateTime($product['Offers_ends_at']);
				$md = new MongoDate($d->getTimestamp());
				

				$product['Offers_ends'] = $md;
				//$product['offer_time_left'] = $this->GetTimeLeft($product['Offers_ends_at']);
				}
			if( $feed_id == "creiffhydro")
				{
				$product['product_name'] = str_replace('package','pkg',$product['product_name']);
				$product['product_name'] = str_replace('Package','Pkg',$product['product_name']);
				$product['product_name'] = str_replace('including','incl',strtolower($product['product_name']));
				$product['product_name'] = str_replace('includes','incl',strtolower($product['product_name']));				
				}
		    $product['feed_id'] = $feed_id;
			$product['program_name'] = $feed['feedname'];
			$product['last_updated'] = new MongoDate();
			if( isset($product['product_operation']) && $product['product_operation'] == 'delete' )
				{
				$products->remove(array('_id' => $feed_id . "_" . $product['productid']) );			
				}
			else
				{
				if( $product['price'] != "" )
					{
					$products->save($product);			
					}
				}
		}
		
		print("processed " . $i . "\n");
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
		{
		while ($reader->read())
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