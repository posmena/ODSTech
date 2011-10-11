<?php

include 'classes/feed_processing/class.feed_processor.php';

class sitescraper
{
	function scrape($site, $instance, $of) {
		global $db;
		
		switch ($site) {
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
							  'tops > blouses & shirts'             => 'http://www.damselinadress.co.uk/shop/tops.aspx?i=32&px=0&ob=1&vbs=293&vbb=0&vbc=0&vbp=0',
							  'tops > jersey wear'                  => 'http://www.damselinadress.co.uk/shop/tops.aspx?i=32&px=0&ob=1&vbs=295&vbb=0&vbc=0&vbp=0',
							  'tops > shrugs'                       => 'http://www.damselinadress.co.uk/shop/tops.aspx?i=32&px=0&ob=1&vbs=294&vbb=0&vbc=0&vbp=0',
							  'sale > dresses'                      => 'http://www.damselinadress.co.uk/shop/ss11-sale/dresses.aspx',
							  'sale > shrugs'                       => 'http://www.damselinadress.co.uk/shop/ss11-sale/shrugs.aspx',
							  'sale > tailoring'                    => 'http://www.damselinadress.co.uk/shop/ss11-sale/tailoring.aspx',
							  'sale > jersey wear'                  => 'http://www.damselinadress.co.uk/shop/ss11-sale/jersey-wear.aspx',
							  'sale > tops'                         => 'http://www.damselinadress.co.uk/shop/ss11-sale/tops.aspx',
							  'sale > accessories'                  => 'http://www.damselinadress.co.uk/shop/ss11-sale/accessories.aspx');
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
								//$url = 'http://chescadirect.co.uk/products/97-champagne-panelled-skirt-limited-size-range-please-phone-0207-60-3434-before-ordering';
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
									$end   = strpos($product, "</span>", $start);
									$price = substr($product, $start,$end-$start);

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

									if (false === isset($code) || $code == '') {
										continue;
									}

									$item['_id']         = $code;
									$item['title']       = $name;
									$item['id']          = $code;
									$item['category']    = $categories;
									$item['price']       = $price;
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

									$collection->save($item, array('_id' => $code));
									
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