<?php

include 'classes/feed_processing/class.feed_processor.php';

class sitescraper
{
	function scrape($site, $instance, $of) {
		global $db;
		
		switch ($site) {
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

									$start = strpos($info, '<h2>') + 4;
									$end   = strpos($info, '</h2>', $start);
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
									
									if ($stock != 'IN STOCK') {			
										$stock = 'OUT OF STOCK';
									}

									$item['_id']         = $code;
									$item['name']        = $name;
									$item['product_id']  = $code;
									$item['category']    = $categories;
									$item['price']       = $price;
									$item['description'] = $desc;
									$item['deeplink']    = $url;
									$item['largeimage']  = $largeImage;
									$item['thumbnail']   = $thumbnail;
									$item['sizes']       = $itemSize;
									$item['stock']       = $stock;
									$item['delivery_time'] = 'Standard delivery within 10-14 working days';
									$item['delivery_cost'] = '£6.50';
									$item['full_merchant_price'] = $oldPrice;

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