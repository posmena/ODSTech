<?php

include 'classes/feed_processing/class.feed_processor.php';

class sitescraper
{
	function scrape($site, $instance, $of) {
		global $db;
						
		switch ($site) {
		case 'forthillhome':
			{
				$conn = new Mongo('localhost');
				$mdb = $conn->odstech;
				$collection = $mdb->live_forthillhome;
				$products = $collection->find();
				foreach ($products as $product) {
					$page = feed_processor::curl_get_file_contents($product['deeplink']);
					$start = stripos($page,'Browse by Manufacturers</a>');
					$brand = "";
					if( $start !== FALSE ){
						if( $start !== FALSE ) {
							$start = stripos($page,'>',$start+50)+1;
				
								$end = stripos($page,'</a',$start);
							$brand = trim(substr($page,$start,$end-$start));
						}
					}
					
					$product['brand'] = $brand;
					$collection->save($product);
					unset($product);
					
				}
				
				break;
			}
			
			case 'easylife':
			{
				$conn = new Mongo('localhost');
				$mdb = $conn->odstech;
				$collection = $mdb->live_easylife;
				$otcollection = $mdb->ot_easylife;
				$otcollection->drop();
				$products = $collection->find();
				$url = 'http://www.easylifegroup.com/';
				$removed = 0;
				$added = 0;
				
				foreach ($products as $product) {
					$page = feed_processor::curl_get_file_contents($product['deeplink']);
					$img = "";
					
					if( stripos($page,'EMSImage1252') !== FALSE )
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
							$desc2 = trim(substr($page,$start,$end-$start));					
					//		$desc2 = strip_tags($desc2);
					//		$desc2 = str_replace('\n','\r\n',$desc2);
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
											
						$desc = str_replace("\n", '. ', strip_tags($desc));
						$desc1 = str_replace("\n", '. ', strip_tags($desc1));
						$desc2 = str_replace("\n", '. ', strip_tags($desc2));
						if (strlen($desc) > 2) {
							$desc = substr($desc, 2, strlen($desc));	
						}
						
						$product['description'] = str_replace("\"","'",$desc1 . " " . $desc2); //$metadesc . '. ' . $desc;
					
					
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
					//echo "\nSkipped: " . $product['deeplink'];
					}
					
				}



				break;
			}
			case 'damsel':
			{
				$conn = new Mongo('localhost');
				$mdb = $conn->odstech;
				$collection = $mdb->damsel_scrape;
				
													
				$site = "http://www.damselinadress.co.uk";
				$urls = array('dresses > any occasion'              => 'http://www.damselinadress.co.uk/shop/dresses.aspx?i=32&px=0&ob=1&vbs=309&vbb=0&vbc=0&vbp=0',
							  'dresses > day dresses'               => 'http://www.damselinadress.co.uk/shop/dresses.aspx?i=32&px=0&ob=1&vbs=57&vbb=0&vbc=0&vbp=0',
							  'dresses > evening dresses'           => 'http://www.damselinadress.co.uk/shop/dresses.aspx?i=32&px=0&ob=1&vbs=287&vbb=0&vbc=0&vbp=0',
							  'dresses > wedding attire'            => 'http://www.damselinadress.co.uk/shop/dresses.aspx?i=32&px=0&ob=1&vbs=402&vbb=0&vbc=0&vbp=0',
							  'coats & jackets > womens coats'      => 'http://www.damselinadress.co.uk/shop/coats-and-jackets.aspx?i=32&px=0&ob=1&vbs=300&vbb=0&vbc=0&vbp=0',
							  'coats & jackets > womens jackets'    => 'http://www.damselinadress.co.uk/shop/coats-and-jackets.aspx?i=32&px=0&ob=1&vbs=301&vbb=0&vbc=0&vbp=0',
							  'skirts & trousers > womens skirts'   => 'http://www.damselinadress.co.uk/shop/skirts-and-trousers.aspx?i=32&px=0&ob=1&vbs=298&vbb=0&vbc=0&vbp=0',
							  'skirts & trousers > womens trousers' => 'http://www.damselinadress.co.uk/shop/skirts-and-trousers.aspx?i=32&px=0&ob=1&vbs=297&vbb=0&vbc=0&vbp=0',
							  'tailoring > work wear'				=> 'http://www.damselinadress.co.uk/shop/tailoring/work-wear.aspx',
							  'tops > blouses & shirts'             => 'http://www.damselinadress.co.uk/shop/tops.aspx?i=32&px=0&ob=1&vbs=293&vbb=0&vbc=0&vbp=0',
							  'tops > jersey wear'                  => 'http://www.damselinadress.co.uk/shop/tops.aspx?i=32&px=0&ob=1&vbs=295&vbb=0&vbc=0&vbp=0',
							  'tops > cardigans'					=> 'http://www.damselinadress.co.uk/shop/tops/cardigans.aspx',
							  'tops > shrugs'                       => 'http://www.damselinadress.co.uk/shop/tops.aspx?i=32&px=0&ob=1&vbs=294&vbb=0&vbc=0&vbp=0',
							  'accessories > scarves'				=> 'http://www.damselinadress.co.uk/shop/accessories/scarves.aspx',
							  'sale > dresses'                      => 'http://www.damselinadress.co.uk/shop/aw11-sale/dresses.aspx?i=32&px=32&ob=3&vbb=0&vbc=0&vbp=0',
							  'sale > dresses'						=> 'http://www.damselinadress.co.uk/shop/aw11-sale/dresses.aspx?i=32&px=0&ob=3&vbb=0&vbc=0&vbp=0',
							  'sale > jackets'						=> 'http://www.damselinadress.co.uk/shop/aw11-sale/jackets.aspx',
							  'sale > coats'						=> 'http://www.damselinadress.co.uk/shop/aw11-sale/coats.aspx',
							  'sale > skirts'						=> 'http://www.damselinadress.co.uk/shop/aw11-sale/skirts.aspx',
							  'sale > trousers'						=> 'http://www.damselinadress.co.uk/shop/aw11-sale/trousers.aspx',
							  'sale > blouses & shirts'				=> 'http://www.damselinadress.co.uk/shop/aw11-sale/blouses-and-shirts.aspx',
							  'sale > shrugs'                       => 'http://www.damselinadress.co.uk/shop/aw11-sale/shrugs.aspx',
							  'sale > jersey wear'                  => 'http://www.damselinadress.co.uk/shop/aw11-sale/jersey-wear.aspx',
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
									$item['id']          = $code;
									$item['gtin']        = $code;
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


									$existing = $collection->findOne(array('title' => $name));
									if( !$existing ) {
										$collection->save($item);
									}
									
								} catch(Exception $ex) {
									
								}
							}
						}
					}
				}

				break;
			}

			case 'chesca':
			{
				$conn = new Mongo('localhost');
				$mdb = $conn->odstech;
				$collection = $mdb->chesca_scrape;
				$site = "http://chescadirect.co.uk";
				$urls = array('jackets' => $site.'/departments/1-jackets-coats',
							  'trousers'    => $site.'/departments/2-trousers',
							  'skirts'      => $site.'/departments/3-skirts',
							  'knitwear'    => $site.'/departments/4-knitwear',
							  'jerseys'     => $site.'/departments/5-jerseys',
							  'blouses'     => $site.'/departments/6-blouses',
							  'dresses'     => $site.'/departments/7-dresses',
							  'accessories' => $site.'/departments/8-accessories');
				$regexp = "/<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>/siU";

				foreach ($urls as $category => $url) {
					$page = feed_processor::curl_get_file_contents($url);
					$pages = array();
					if (preg_match_all($regexp, $page, $matches)) {
						foreach($matches[0] as $product_url) {
							if (false !== strpos($product_url, '&amp;page=')) {
								$pages[] = $product_url;
							}
							
							// page 1
							if (false !== strpos($product_url,'href="/products')) {
								$pUrls[$category][] = $product_url;
							}
						}
						
						// loop through all pages
						foreach ($pages as $page) {
							if (preg_match($regexp, $page, $matches)) {
								$pageUrls[$category][] = $matches[2];
							}
						}
						
						$matches = '';
						foreach($pageUrls[$category] as $url) {
							//$url = html_entity_decode($url);
							$page = feed_processor::curl_get_file_contents($site.$url);
							if (preg_match_all($regexp, $page, $matches)) {
								foreach($matches[0] as $product_url) {
									// page x
									if (false !== strpos($product_url,'href="/products')) {
										$pUrls[$category][] = $product_url;
									}
								}
							}	
						}
					}
				}

				if (true === is_array($pUrls) && count($pUrls) > 0) {
					foreach ($pUrls as $categories => $cat) {
						foreach ($cat as $pUrl) {
							if (preg_match($regexp, $pUrl, $matches)) {
								$url = $site.$matches[2];
								//$url = 'http://chescadirect.co.uk/products/97-champagne-panelled-skirt-limited-size-range-please-phone-0207-60-3434-before-ordering';
								$product = feed_processor::curl_get_file_contents($url);
								$item = array();
								try {
									$start = strpos($product, "<div class='info'>");

									if ($start === false) {
										throw new Exception ('Name not found for ' . $url);
									}

									$end   = strpos($product,'</div>',$start) + 6;
									$info  = substr($product,$start,$end-$start);

									$start = strpos($info, '<h1>') + 4;
									$end   = strpos($info, '</h1>', $start);
									$name  = substr($info,$start,$end-$start);
								
									$start = strpos($info, "<p class='code'>") + 30;
									$end   = strpos($info, '</p>', $start);
									$code  = trim(substr($info,$start,$end-$start));
									
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

									$start = strpos($product, "<p class='description'>") + 23;
									$end   = strpos($product, "</p>", $start);
									$desc  = trim(substr($product,$start,$end-$start));

									$start = strpos($product, "<div class='images'>");
									$end   = strpos($product, "</a>", $start) + 4;
									$image = trim(substr($product,$start,$end-$start));

									if (preg_match($regexp, $image, $imgmatches)) {
										$largeImage = $site.$imgmatches[2];
										$thumbnail = str_replace('large', 'small', $largeImage);
									}

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
									
								}
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

