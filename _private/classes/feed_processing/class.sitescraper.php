<?php
include 'classes/feed_processing/class.feed_processor.php';

class sitescraper   
{
	function scrape($site, $instance, $of) {
		global $db;
					
					
		switch ($site) {
		case 'stylepiques':
			{
				$conn = new Mongo('localhost');
				$mdb = $conn->odstech;
				$collection = $mdb->live_stylepiques;
				$collection->drop();
				
				// need to check on site if these are the current categories
				
				$urls = array('Women\'s Jeans'              => 'http://www.stylepiques.com/shop/search?q=&sort=&perpage=100&tags=&categories[]=10',
							  'Women\'s Dresses'               => 'http://www.stylepiques.com/shop/search?q=&sort=&perpage=100&tags=&categories[]=1',
							  'Women\'s Jackets'           => 'http://www.stylepiques.com/shop/search?q=&sort=&perpage=100&tags=&categories[]=7',
							  'Women\'s Knitwear'            => 'http://www.stylepiques.com/shop/search?q=&sort=&perpage=100&tags=&categories[]=8',
							  'Women\'s Playsuits'      => 'http://www.stylepiques.com/shop/search?q=&sort=&perpage=100&tags=&categories[]=9',
							  'Women\'s Shoes'    => 'http://www.stylepiques.com/shop/search?q=&sort=&perpage=100&tags=&categories[]=6',
							  'Women\'s Shorts'   => 'http://www.stylepiques.com/shop/search?q=&sort=&perpage=100&tags=&categories[]=5',
							  'Women\'s Skirts' => 'http://www.stylepiques.com/shop/search?q=&sort=&perpage=100&tags=&categories[]=3',
							  'Women\'s Tops'				=> 'http://www.stylepiques.com/shop/search?q=&sort=&perpage=100&tags=&categories[]=2',
							  'Women\'s Trousers'             => 'http://www.stylepiques.com/shop/search?q=&sort=&perpage=100&tags=&categories[]=4',
							  'Women\'s Bags'             => 'http://www.stylepiques.com/shop/search?accessories=true&q=&sort=&perpage=&tags=&price=&categories[]=16',
							  'Women\'s Belts'             => 'http://www.stylepiques.com/shop/search?accessories=true&q=&sort=&perpage=&tags=&price=&categories[]=15',
							  'Women\'s Hats'             => 'http://www.stylepiques.com/shop/search?accessories=true&q=&sort=&perpage=&tags=&price=&categories[]=13',
							  'Women\'s Jewellery'             => 'http://www.stylepiques.com/shop/search?accessories=true&q=&sort=&perpage=&tags=&price=&categories[]=11',
							  'Women\'s Accesories'             => 'http://www.stylepiques.com/shop/search?accessories=true&q=&sort=&perpage=&tags=&price=&categories[]=14'
							  );
							  
					$regexp = "/<a href=\"shop(.*)\">(.*)<\/a>/siU";
				
				
					foreach ($urls as $category => $url) {
					$page = feed_processor::curl_get_file_contents($url);
					
					$start = strpos($page, '<div class="products">');
					$end   = strpos($page, '<div class="clear">', $start);
					$promos  = substr($page,$start,$end-$start);
									
					if (preg_match_all($regexp, $promos, $matches)) {
						foreach($matches[1] as $key =>  $product_url) {
							//if ($key % 2) {
								$pUrls[$category][] = 'http://www.stylepiques.com/shop' . $product_url;
							//}
						}						
					}
					
				}
				if (true === is_array($pUrls) && count($pUrls) > 0) {
					foreach ($pUrls as $categories => $cat) {
						print ($categories ."\n");
						foreach ($cat as $pUrl) {
								print($pUrl ."\n");
								
								$product = feed_processor::curl_get_file_contents($pUrl);
								$product = substr($product,strpos($product,"<section id=\"product\">"));
								
								$item = array();
								
								$item['link']  = $pUrl;
								
								$item['productcode'] = str_replace("http://www.stylepiques.com/shop/product/","",$pUrl); // get last directory
								$item['_id']  = $item['productcode'];
								$item['id']  = $item['productcode'];
								$item['category'] = $categories;
								$item['condition'] = 'New';
								
								$regexp = "/<h1 class=\"name\">(.*)<\/h1>/siU" ;
																
								if( preg_match($regexp, $product, $arr) ) {								
									$item['title'] = (trim($arr[1]));
									}
								
								$regexp = "/<div class=\"brand\">(.*)<\/div>/siU" ;
								if( preg_match($regexp, $product, $arr) )
									{
									$item['brand'] = trim(str_replace("By","",$arr[1])) ;
									}
															
								
								$regexp = "/<div class=\"price\">(.*)<\/div>/siU" ;
								if( preg_match($regexp, $product, $arr ) )
									{			
									$price = $arr[1];
									$price = trim(str_replace("£","",$price)) ;
									$price = trim(str_replace("Price:","",$price)) ;
									$price = trim(str_replace("&pound;","",$price)) ;
									$item['original_price']	= $price;
									$item['price']	= $price;
									
									$regexp = "/<span class=\"old-price\">(.*)<\/span>/siU" ;
									if( preg_match($regexp, $product, $arr ) )
										{
											$regexp = "/<span class=\"new-price\">(.*)<\/span>/siU" ;	
											$item['original_price']	= trim(str_replace("&pound;","",$arr[1])) ;										
											if( preg_match($regexp, $product, $arr ) )
												{
												$item['price'] = trim(str_replace("NOW","",str_replace("&pound;","",$arr[1])));
												}
										}
								
									
									}
									
								$item['delivery_cost'] = 0;
								$item['delivery_time'] = "6 working days";
															
													
								$regexp = "/<div class=\"description\">(.*)<\/div>/siU";
								if( preg_match($regexp, $product, $arr) )
									{
									$arr[1] = str_replace("’","'",$arr[1]);
									$arr[1] = str_replace("–","-",$arr[1]);
									$arr[1] = str_replace("\r\n"," ",$arr[1]);
									$arr[1] = str_replace("  "," ",$arr[1]);
									
									$item['description'] = trim(utf8_encode(strip_tags($arr[1])));
									
									}
									
								
								$regexp = "/<ul id=\"mainSizes\">(.*)<\/ul>/siU";
								if( preg_match($regexp, $product, $arr) )
									{
									$ul = $arr[1];
									
									$regexp = "/<li.*class=[\"\'][^o].*>(.*)<\/li>/siU";
									if( preg_match_all($regexp, $ul, $matches) )
										{
										
											foreach($matches[1] as $key =>  $size) {
											
												$item['sizes'] .= trim(utf8_encode(strip_tags($size))) . " ";
											}
										}
									}
								
								$regexp = "/<img rel=\'(.*);/siU";
								if (preg_match_all($regexp, $product, $matches)) {
								
									$i=0;
									foreach($matches[1] as $key =>  $img_url) {
										$i++;
										if( $i > 1 )
											{
											$item['image_link' . $i] = "http://www.stylepiques.com/" . $img_url; 
											}
										else
											{
											$item['image_link'] = "http://www.stylepiques.com/" . $img_url; 
											}
									}
									
								}
								
								
								$item['availability'] = "In stock";
								
								print_r($item);
								$collection->save($item);
								
						}
					}
					}
	
			break;
			}
			
		case 'forthillhome':
			{
				echo '<pre>Starting';
				$conn = new Mongo('localhost');
				$mdb = $conn->odstech;
				$collection = $mdb->live_forthillhome;
				$products = $collection->find(array("brand" => null));
				$products->immortal();
				foreach ($products as $product) {
					$page = feed_processor::curl_get_file_contents($product['deeplink']);		
					$start = stripos($page,'Browse for more products in the same category as this item');					
					$start = stripos($page,'Browse by Manufacturers</a>',$start);
					echo($product['deeplink']);
					$brand = "Forthill";
					if( $start !== FALSE ){
						$start = stripos($page,'<a',$start+10);		
						if( $start !== FALSE ){
							$start = stripos($page,'>',$start+5)+1;		
							if( $start !== FALSE ) {							
								$end = stripos($page,'</a',$start);
								$brand = trim(substr($page,$start,$end-$start));
							}
						}
					}
					$product['brand'] = $brand;
					$collection->save($product);
					unset($product);					
				}
				echo 'finished</pre>';
				break;
			}
			
			case 'easylife':
			{
				$conn = new Mongo('localhost');
				$mdb = $conn->odstech;
				$collection = $mdb->dump_easylife;
				$otcollection = $mdb->ot_easylife;
				$otcollection->drop();
				$products = $collection->find();
				$url = 'http://www.easylifegroup.com/';
				$removed = 0;
				$added = 0;
				
				foreach ($products as $product) {
					$page = feed_processor::curl_get_file_contents($product['deeplink']);
					$img = "";
					
					if( stripos($page,'class="emsc19"') !== FALSE )
					{
						print($product['deeplink']);
						$start = strpos($page, '<meta name="description" content="') + 34;
						$end   = strpos($page, '" />', $start);
						$metadesc = trim(substr($page,$start,$end-$start));

						$start = strpos($page, "<div class=\"stretchy\" id=\"obj773\"");
						$end   = strpos($page, "</div>", $start);
						$trash = trim(substr($page,$start,$end-$start));

						$start = stripos($trash, "<ul>");
						$end   = stripos($trash, "</ul>", $start) + 5;
						$desc = trim(substr($trash,$start,$end-$start));
						
						$desc1 = "";
						
						$start = stripos($page, 'class="emsc19"');
						if( $start !== FALSE )
							{
							$start = $start + 15;
							$start = stripos($page, '>',$start) + 1;					
							$end = stripos($page, '</div>',$start);
							$desc1 = trim(substr($page,$start,$end-$start));
							}
						else
							{
							$desc1 = $desc;
							}
												
						$start = stripos($page, '<ul>');
						
						$desc2 = "";
						
						if( $start !== FALSE)
							{
							$start = $start + 4;
							$end = stripos($page, '</ul>', $start);					
							
							$end2 = stripos($page, '<TABLE', $start);					 
							if( $end2 && $end2 < $end) { $end = $end2; }
							$desc2 = trim(substr($page,$start,$end-$start));					
					//		$desc2 = strip_tags($desc2);
					//		$desc2 = str_replace("\n",'\r\n',$desc2);
							}
													
						$start = stripos($page, '<img emssteve="False"');
						//print($start ."\n");
						$end   = stripos($page, "/>", $start) + 4;
						$img = trim(substr($page,$start,$end-$start));
						//print($img);
						$start = stripos($img, 'emssrc=') + 8;
						//print($start ."\n");
						$end   = stripos($img, '"', $start);
						$img = trim(substr($img,$start,$end-$start));
						//print($img);
						$start = stripos($img,"-");
						//print($start ."\n");
						$img = substr($img,0,$start);
											
						$img2 = '';
						$img3 = '';
						$start = stripos($page, 'javascript:showbigimage778(');
						
						if( $start > 0 ) 
							{
							$start = stripos($page, 'javascript:showbigimage778(', $start+27) + 27;
							$end   = stripos($page, ",", $start);
							$img2 = 'EMSImage'. trim(substr($page,$start,$end-$start)) ;								
												
							$start = stripos($page, 'javascript:showbigimage778(',$end);
								
							if( $start > 0) 
								{
								$start = $start + 27;
								$end   = stripos($page, ",", $start);
								$img3 = 'EMSImage'. trim(substr($page,$start,$end-$start)) ;								
								}					
							
							}
						
						$cat = "";
						$start = strpos($page, 'id="obj1203"');
						if( $start > 0 )
							{
							$start = $start  + 20;						
							$end   = strpos($page, "</a>", $start)+4;
							if( $end !== FALSE )
								{
								$cat   = trim(substr($page,$start,$end-$start));
								
								$start = strpos($cat, '">') ;
								if( $start  !== FALSE)
									{
									$start = $start + 2;
									$end   = strpos($cat, "</a>", $start);
									$cat   = trim(substr($cat,$start,$end-$start));
									}
								}
						}
						if( strlen($cat) > 150 )
							{
							$cat = "";
							}
							
						$product['category'] = $cat;
						$product['image_link'] = $url.$img;
						if( '' != $img2) {
							$product['image_link2'] = $url.$img2;
						}
						
						if( '' != $img3) {
							$product['image_link3'] = $url.$img3;
						}
											
						$desc = str_replace("\n", '. ', strip_tags(html_entity_decode($desc,ENT_QUOTES,'UTF-8')));
						$desc1 = str_replace("\n", '. ', strip_tags(html_entity_decode($desc1,ENT_QUOTES,'UTF-8')));
						$desc2 = str_replace("\n", '. ', strip_tags(html_entity_decode($desc2,ENT_QUOTES,'UTF-8')));
												
						if (strlen($desc) > 2) {
							$desc = substr($desc, 2, strlen($desc));	
						}
						
						$product['description'] = str_replace("\"","'",$desc1 . " " . $desc2); //$metadesc . '. ' . $desc;
						
						$theData = mb_convert_encoding($product['description'],'ISO-8859-1','UTF-8');
						
						$theData = strip_tags($theData);
						// convert to html entities
						$theData = htmlentities($theData);
						$theData = mb_convert_encoding($theData,'UTF-8','ISO-8859-1');
						
					$product['description'] =$theData ;
					
					if ("" == $img) {
						$collection->remove(array('productid' => $product['productid']), true);
						unset($product);
						$removed++;
						continue;
					}

					foreach($product as $key => $field) {
						$isUTF8 = mb_detect_encoding($field, 'UTF-8', true);
						if (false === $isUTF8) {
							$product[$key] = mb_convert_encoding($field, "UTF-8");
						}
					}

					$product['id'] = $product['productid'];
					$product['title'] = $product['productname'];
					//$product['product_type'] = $product['category'];
					$product['condition'] = "New";
					$product['gtin'] = $product['productid'];
					
					$otcollection->save($product);
					//echo "\nAdded: " . $product['deeplink'];
					unset($product);
					$added++;	

					echo "\nadded: " . $added . ", removed: " . $removed . "\n";
					}
					else
					{
					echo "\nSkipped: " . $product['deeplink'];
					}
					
				}



				break;
			}
			case 'damsel':
			{
				$conn = new Mongo('localhost');
				$mdb = $conn->odstech;
				$collection = $mdb->damsel_scrape;
				
			    $collection->update(array("_id" => array("exists" => true)),array("updated" => false),array("multi" => true)); 
													
				$site = "http://www.damselinadress.co.uk";
				$urls = array('dresses > day dresses'               => 'http://www.damselinadress.co.uk/shop/dresses/day-dresses.aspx',
							  'dresses > occasion wear'             => 'http://www.damselinadress.co.uk/shop/dresses/any-occasion.aspx',
							  'dresses > evening dresses'           => 'http://www.damselinadress.co.uk/shop/dresses/evening-dresses.aspx',
							  'dresses > wedding attire'            => 'http://www.damselinadress.co.uk/shop/dresses/wedding-attire.aspx',
							  'skirts & trousers > womens skirts'   => 'http://www.damselinadress.co.uk/shop/skirts-and-trousers/skirts.aspx',
							  'skirts & trousers > womens trousers' => 'http://www.damselinadress.co.uk/shop/skirts-and-trousers/trousers.aspx',
							  'coats & jackets > womens jackets'    => 'http://www.damselinadress.co.uk/shop/coats-and-jackets/jackets.aspx',
							  'coats & jackets > coats'    			=> 	'http://www.damselinadress.co.uk/shop/coats-and-jackets/coats.aspx',
							  'tailoring > work wear'				=> 'http://www.damselinadress.co.uk/shop/tailoring/work-wear.aspx',
							  'tops > shrugs'                       => 'http://www.damselinadress.co.uk/shop/tops/shrugs.aspx',
							  'tops > blouses & shirts'             => 'http://www.damselinadress.co.uk/shop/tops/blouses-and-shirts.aspx',
							  'tops > jersey wear'                  => 'http://www.damselinadress.co.uk/shop/tops/jersey-wear.aspx',
							  'tops > cardigans'					=> 'http://www.damselinadress.co.uk/shop/tops/cardigans.aspx',
							  'tops > tunics'						=> 'http://www.damselinadress.co.uk/shop/tops/tunics.aspx',
							  'accessories > scarves'				=> 'http://www.damselinadress.co.uk/shop/accessories/scarves.aspx',
							  'wedding > bride'                     => 'http://www.damselinadress.co.uk/shop/wedding/bride.aspx',
							  'wedding > bridesmaids'               => 'http://www.damselinadress.co.uk/shop/wedding/bridesmaids.aspx',
							  'wedding > wedding guest'             => 'http://www.damselinadress.co.uk/shop/wedding/wedding-guest.aspx',						
							  'wedding > mother of the bride'		=> 'http://www.damselinadress.co.uk/shop/wedding/motherofthebride.aspx',
							  'sale > dresses'						=> 'http://www.damselinadress.co.uk/shop/ss12_sale/dresses.aspx',
							  'sale > jackets'						=> 'http://www.damselinadress.co.uk/shop/ss12_sale/jackets.aspx',
							  'sale > coats'						=> 'http://www.damselinadress.co.uk/shop/ss12_sale/coats.aspx',
							  'sale > skirts'						=> 'http://www.damselinadress.co.uk/shop/ss12_sale/skirts.aspx',
							  'sale > trousers'						=> 'http://www.damselinadress.co.uk/shop/ss12_sale/trousers.aspx',
							  'sale > blouses & shirts'				=> 'http://www.damselinadress.co.uk/shop/ss12_sale/blouses-and-shirts.aspx',
							  'sale > shrugs'                       => 'http://www.damselinadress.co.uk/shop/ss12_sale/shrugs.aspx',
							  'sale > jersey wear'                  => 'http://www.damselinadress.co.uk/shop/ss12_sale/jersey-wear.aspx'
							  );
				//$urls = array('dresses > any occasion'              => 'http://www.damselinadress.co.uk/shop/dresses.aspx?i=32&px=0&ob=1&vbs=309&vbb=0&vbc=0&vbp=0');
				$regexp = "/<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>/siU";
				
				foreach ($urls as $category => $url) {
					$page = feed_processor::curl_get_file_contents($url);
					$pages = array();

					$start = strpos($page, '<div id="productpromobox">');
					$end   = strpos($page, '<div class="separator"></div>', $start);
					$promos  = substr($page,$start,$end-$start);
								
					if (preg_match_all($regexp, $promos, $matches)) {
						foreach($matches[0] as $key =>  $product_url) {
							if ($key % 2) {
								$pUrls[$category][] = $product_url;
							}
						}						
					}
				}

				if (true === is_array($pUrls) && count($pUrls) > 0) {
					foreach ($pUrls as $categories => $cat) {
						foreach ($cat as $pUrl) {
							if (preg_match($regexp, $pUrl, $matches)) {
								$url = str_replace("'", '', $matches[2]);
								//$url = 'http://www.damselinadress.co.uk/shop/ss11-sale/tailoring/otis-jacket-pink.aspx';
								//$url = 'http://www.damselinadress.co.uk/shop/dresses/day-dresses/azurit-womens-dress-blue.aspx';
								$product = feed_processor::curl_get_file_contents($url);
								$item = array();
								try {

									$delivery = '4.95';
									
									print($url . "\n");
									
									$start = strpos($product, "<div id='mainproductimage'>");
									$end   = strpos($product, "</div>", $start);
									$image = trim(substr($product,$start,$end-$start));

									if (preg_match($regexp, $image, $imgmatches)) {
										$largeImage = str_replace("'", '', $imgmatches[2]);
										$thumbnail = str_replace('Large', 'Small', $largeImage);
									}

									$start = strpos($product, "<h1>") +4;
									$end   = strpos($product,'</h1>',$start);
									$name  = trim(substr($product,$start,$end-$start));
									
											print("Name " . $name . "\n");					
																						
									$start = strpos($product, '<div id="skucode">') + 18;
									$end   = strpos($product, '</div>', $start);
									$code  = trim(str_replace('<strong>Product code:</strong><br />', '', (substr($product,$start,$end-$start))));

									$start = strpos($product, '<div id="stockav">') + 18;
									$end   = strpos($product, "</div>", $start);
									$stock = trim(str_replace('<strong>Stock availability:</strong><br />', '', substr($product,$start,$end-$start)));
									
									$oldPrice = '';

									$start = strpos($product, "<span class='pricelarge'>") + 27;
									$end   = strpos($product, "</span>", $start) + 7;
									$price = str_replace('&pound;', '', trim(substr($product,$start,$end-$start)));
									
									
									$newPrice = strstr($price, "strike");
										
									if (false !== $newPrice) {
										$start = strpos($price, "trike>") + 8;
										$end   = strpos($price, "</strike>", $start);
										$oldPrice = substr($price,$start,$end-$start);

										$start = strpos($price, "<span class='sale'>") + 21;
										$end   = strpos($price, "</span>", $start);
										$newPrice = substr($price,$start,$end-$start);
										$price = $newPrice;

									}

									$price = str_replace('</span>', '', $price);

									if ($price > 150) {
										$delivery = '0';
									}

									$start = strpos($product, '<div id="probrief">') + 19;
									$end   = strpos($product, "</div>", $start);
									$desc  = trim(str_replace('"', '', substr($product,$start,$end-$start)));


									if (preg_match('/<select\s[^>](.*)<\/select>/msU',$product,$sizes)) {
										$option = $sizes[0];
										if (preg_match_all('/<option\s[^>]*(.*)<\/option>/msU',$option,$size)) {
											$itemSize = '';
											foreach ($size[1] as $value) {
												$itemSize .= trim(str_replace('SIZE ', '', str_replace('>','', strstr($value, '>')) . "|"));
											}
										}
									}

									$start = strpos($product, "Fabric content:") + 15;
									if ($start === false) {
										$material = '';
									} else {
										$end   = strpos($product, "<br />", $start);
										$material = trim(substr($product,$start,$end-$start));	
									}
									
									$material = str_replace('<strong>','', $material);
									$material = str_replace('</strong>','', $material);
									$material = str_replace('&nbsp;',' ', $material);

									if(strlen($material) > 300) {
										$material = '';
									}

									if (false === isset($name) || $name == '') {
										continue;
									}

									//if (false === isset($code) || $code == '') {
									//	continue;
									//}

									//$item['_id']         = $code;
									$item['title']       = $name;
									//$item['id']          = $item['_id'];
									//$item['gtin']        = $item['_id'];
									$item['category']    = $categories;
									$item['product_type']= $categories;
									$item['price']       = $price;
									$item['condition']   = "New";
									$item['description'] = $desc;
									$item['link']        = $url;
									$item['image_link']  = $largeImage;
									$item['thumbnail']   = $thumbnail;
									$item['sizes']       = $itemSize;
									$item['availability']       = $stock;
									$item['delivery_time'] = 'Standard delivery within 3-5 working days';
									$item['delivery_cost'] = $delivery;
									$item['shipping'] = $item['delivery_cost'];
									$item['full_merchant_price'] = $oldPrice;
									$item['material'] = $material;
									$item['condition'] = "New";
									$item['brand'] = 'Damsel in a Dress';
									$item['quantity'] = 1;
									$item['updated'] = true;

									$update = $collection->update(array('title' => $name), $item, array("upsert" => true));
									
									print($update . "\n");
									
								} catch(Exception $ex) {
									print $ex;
								}
							}
						}
					}

					
					$collection->remove(array("updated" => false),array("multi" => true));
					
					$products = $collection->find();
					foreach ($products as $product) {
						$product['id']          = (string)$product['_id'];
						$product['gtin']        = (string)$product['_id'];
						$collection->save($product);
					}
					
				}

				break;
			}

			case 'chesca':
			{
				$conn = new Mongo('localhost');
				$mdb = $conn->odstech;
				$collection = $mdb->chesca_scrape;
				$collection->drop();
				$site = "http://chescadirect.co.uk";
				$urls = array('jackets' => $site.'/departments/1-jackets-coats',
							  'trousers'    => $site.'/departments/2-trousers',
							  'skirts'      => $site.'/departments/3-skirts',
							  'knitwear'    => $site.'/departments/4-knitwear',
							  'jerseys'     => $site.'/departments/5-jerseys',
							  'blouses'     => $site.'/departments/6-blouses',
							  'dresses'     => $site.'/departments/7-dresses',
							  'accessories' => $site.'/departments/8-accessories',
							  'body-shapers' => $site.'/departments/9-body-shapers',
							  'swimwear' => $site.'/departments/10-swimwear',
							  'shrugs' => $site.'/departments/11-shrugs-cover-ups'
							  );
							  
				$regexp = "/<a\s[^>]*href=(\"??)([^\" >]*?)[^>]*>(.*)<\/a>/siU";
				
				foreach ($urls as $category => $url) {
					print("*** Category " .$category . "\n");
					$page = feed_processor::curl_get_file_contents($url);
					$pages = array();
					if (preg_match_all($regexp, $page, $matches)) {
						foreach($matches[2] as $product_url) {
						
							if (false !== strpos($product_url, '&amp;page=')) {
							 echo("Found page:" . $product_url . "\n");
								$pages[] = $site.$product_url;
								$pageUrls[$category][] = $site.$product_url;
							}
							
							// page 1
							if (false !== strpos($product_url,'/products')) {
							 echo("Found product:" . $product_url . "\n");
								$pUrls[$category][] = $site.$product_url;
							}
						}
						
						// loop through all pages
					//	foreach ($pages as $page) {
						//	if (preg_match($regexp, $page, $matches)) {
					//	 echo("Found page url:" . $matches[2] . "\n");
					//			$pageUrls[$category][] = $matches[2];
					//		}
					//	}
						
						$matches = '';
						foreach($pageUrls[$category] as $url) {
						echo("DOWNLOAD page url:" . $url . "\n");
						
							//$url = html_entity_decode($url);
							$page = feed_processor::curl_get_file_contents($site.$url);
							if (preg_match_all($regexp, $page, $matches)) {
								foreach($matches[2] as $product_url) {
									// page x
									if (false !== strpos($product_url,'/products')) {
									echo("Found product on page:" . $product_url . "\n");
										$pUrls[$category][] = $site.$product_url;
									}
								}
							}	
						}
					}
				}
				print("**** Stage2 ******\n");
				if (true === is_array($pUrls) && count($pUrls) > 0) {
					foreach ($pUrls as $categories => $cat) {
					   print("categories" . $categories ."\n");
					   print("cat" . $cat ."\n");
					   
						foreach ($cat as $pUrl) {
						    print("Process " . $pUrl . "\n");
						//	if (preg_match($regexp, $pUrl, $matches)) {
							//	$url = $site.$matches[2];
								//$url = 'http://chescadirect.co.uk/products/97-champagne-panelled-skirt-limited-size-range-please-phone-0207-60-3434-before-ordering';
								$product = feed_processor::curl_get_file_contents($pUrl);
								
								$item = array();
								try {
									$start = strpos($product, "<div class='info'>");

									if ($start === false) {
										throw new Exception ('Name not found for ' . $pUrl);
									}

									$end   = strpos($product,'</div>',$start) + 6;
									$info  = substr($product,$start,$end-$start);

									$start = strpos($info, '<h1>') + 4;
									$end   = strpos($info, '</h1>', $start);
									$name  = substr($info,$start,$end-$start);
									
									print("name" . $name . "\n");
									$start = strpos($info, "<p class='code'>") + 30;
									$end   = strpos($info, '</p>', $start);
									$code  = trim(substr($info,$start,$end-$start));
									print("code" . $code . "\n");
									
									$start = strpos($info, "<div class='price'>") + 20;
									$end   = strpos($info, "</div>");
									$price = str_replace('&pound;', '', trim(substr($info,$start,$end-$start)));
									$oldPrice = $price;
									$newPrice = strstr($price, "\n");
		
									if (false !== $newPrice) {
										$start = strpos($price, "<span class='old-price'>") + 24;
										$end   = strpos($price, "</span>");
										$oldPrice = str_replace('&pound;', '', trim(substr($price,$start,$end-$start)));
										$price = str_replace("\n", '', $newPrice);
									}

										print("price" . $price . "\n");
										
									$start = strpos($product, "<p class='description'>") + 23;
									$end   = strpos($product, "</p>", $start);
									$desc  = trim(substr($product,$start,$end-$start));

										print("desc" . $desc . "\n");
										
									$start = strpos($product, "<div class='images'>");
									$end   = strpos($product, "</a>", $start) + 4;
									$image = trim(substr($product,$start,$end-$start));

									
									
									if (preg_match($regexp1, $image, $imgmatches)) {
										$largeImage = $site.$imgmatches[2];
										$thumbnail = str_replace('large', 'small', $largeImage);
									}

									print("image" . $largeImage . "\n");
									
									if (preg_match('/<select\s[^>](.*)<\/select>/msU',$product,$sizes)) {
										$option = $sizes[0];
										if (preg_match_all('/<option\s[^>]*(.*)<\/option>/msU',$option,$size)) {
											$itemSize = '';
											foreach ($size[1] as $value) {
												$itemSize .= str_replace('>','', strstr($value, '>')) . "|";
											}
										}
									}

									$start = strpos($product, "<span class='available'>") + 24;
									$end   = strpos($product, "</span>", $start);
									$stock = trim(substr($product,$start,$end-$start));

									print("stock" . $stock . "\n");
									
									$start = strpos($product, "<p class='composition'>") + 23;
									$end   = strpos($product, "</p>", $start);
									$material = trim(substr($product,$start,$end-$start));
									$material = str_replace('Composition:','',$material);
									$material = str_replace('<span>','',$material);
									$material = str_replace('</span>','',$material);
									$material = str_replace("\n",'',$material);

									
									if ($stock != 'IN STOCK') {			
										$stock = 'OUT OF STOCK';
									}

									if (false === isset($name) || $name == '') {
										continue;
									}

									if (false === isset($code) || $code == '') {
										continue;
									}

									$item['_id']         = $code;
									$item['title']        = $name;
									$item['id']  = $code;
									$item['category']    = $categories;
									$item['price']       = $price;
									$item['description'] = $desc;
									$item['link']    = $url;
									$item['image_link']  = $largeImage;
									$item['thumbnail']   = $thumbnail;
									$item['sizes']       = $itemSize;
									$item['availability']       = $stock;
									$item['delivery_time'] = 'Standard delivery within 10-14 working days';
									$item['delivery_cost'] = '6.50';
									$item['shipping'] = $item['delivery_cost'];
									$item['full_merchant_price'] = $oldPrice;
									$item['material'] = $material;
									$item['condition'] = "New";
									$item['brand'] = 'Chesca';
									$item['quantity'] = 1;

									$item['product_type']    = $categories;
									$item['size']       = $itemSize;

									$collection->save($item, array('_id' => $code));
									
								} catch(Exception $ex) {
									print $ex;
								}
							
						}
					}
				}

				break;
			}
			case 'easyjet': {
				
				$conn = new Mongo('localhost');
				$mdb = $conn->odstech;
				$collection = $mdb->properties_raw;
				$count = $collection->find()->count();
				$perInstance = ceil($count / $of);
				$from = ($instance-1)*$perInstance;
				$cursor = $collection->find()->skip($from)->limit($perInstance);
				$scraped = $mdb->properties_scrape;
				foreach ($cursor as $url) {
					$urls[$url['propertyid']] = 'http://holidays.easyjet.com/Holiday.aspx?PropertyID=' . $url['propertyid'];
				}
				
				$limit = count($urls);
				$timeStart = time();
				$x = 0;
				$i = 0;
				$values   = '';
				foreach($urls as $PropertyId => $url) {
					$i++;
					$info     = '';
					$page     = feed_processor::curl_get_file_contents($url);
					$newlines = array("\t","\n","\r","\x20\x20","\0","\x0B");
					$content  = str_replace($newlines, "", html_entity_decode($page));
					
					try {
						// get name
						$start    = strpos($content, '<span id="spnName">') + 19;
						if ($start === false) {
							throw new Exception ('Name not found for ' . $url);
						}
						$end      = strpos($content,'</span>',$start);
						$name     = substr($content,$start,$end-$start);
						
						// get lead in price info (tag & price)
						$start    = strpos($content, '<div class="leadInPrice">');
						if ($start === false) {
							throw new Exception ('Price not found for ' . $url);
						}
	
						$end      = strpos($content,'</div>',$start) + 6;
						$info     = substr($content,$start,$end-$start);
						
						// get tag
						$start    = strpos($info, '<span>') + 6;
						if ($start === false) {
							throw new Exception ('Tag not found for ' . $url);
						}
						$end      = strpos($info,'</span>',$start);
						$tag     = substr($info,$start,$end-$start);
						
						// get price
						$start    = strpos($info, '<em>') + 6;
						if ($start === false) {
							continue;
						}
						$end      = strpos($info,'</em>',$start);
						$price     = substr($info,$start,$end-$start);
					} catch (Exception $ex) {
						print $ex->getMessage()."\n";
						continue;
					}
					
					$property['_id'] = $PropertyId;
					$property['price']      = str_replace(',','',$price);
					$property['name']       = $name;
					
					$scraped->save($property, array('_id' => $PropertyId));
					
					$timeTaken = time() - $timeStart;
					echo 'Progress ' . $i . '/' . $limit . " - (" . $x	 . ") (" . $timeTaken . "s)\n";
				}
				
				break;
			}
		}
	}
}

