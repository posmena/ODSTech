<?php

class webgains_feed extends network_base
{
	private $name = 'Webgains Feed';
	public $feedid = '';
	private $fields = array();
	private $prefix;
	public $custom = array();
	private $local_file;
	public $num_products;
	
	public function __construct($local_file = null, $full = false, $feed_id = null)
	{
		global $db;
		$this->num_products = 0;
		
		try
			{
			$this->num_products = $this->parse_xml($local_file, $feed_id);	
			}
		catch(Exception $e)
			{
			print("Error parsing " . $feed_id . ", file " . $local_file . $e);
			}
	}

	function parse_xml($file, $feed_id)
	{
		print("Parsing " . $file . "\n");
		
		global $db;
		
		$collection = $db->p20_products;
		
		$reader = new XMLReader();
		$reader->open($file,"ISO-8859-1");

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
							
							$dbproduct['_id'] = 				 $feed_id . '_' . $product->product_id;
							$dbproduct['feed_id'] =			     $feed_id;
							
							foreach( $product as $field => $value)
								{
								if($field == 'categories') 
									{
									foreach($value as $fld => $val)
										{
										$atts = $val->attributes();
										foreach( $atts as $at => $x)
											{
											if( $at == 'path' )
												{
												$dbproduct['categories'][] = (string) $x;										
												}
											}
										//print $fld . " : " . $val;
										}
									}
									else
										{
										$dbproduct[(string)$field] = (string)$value;
										}
								}
								
							
							$dbproduct['image_thumbnail'] =      (false !== strpos($dbproduct['image_thumbnail'], 'http')) ? $dbproduct['image_thumbnail'] : $dbproduct['image_url'];
							
							$collection->save($dbproduct);
							
							$i++;
							}
					}
				}
			}
		}
		
		
		return $i;
	}
}

/*
 /usr/local/mongodb/bin/mongoexport -d odstech -c for --csv -f 'productcode','productid','productname','warehousecustom','productdescription','availability','freeshippingitem','google_product_type','productcondition','productmanufacturer','productfeatures','deeplink','imagethumbnail','imagelarge' -o files/hostedfeeds/forthill/forthillall.csv
 */