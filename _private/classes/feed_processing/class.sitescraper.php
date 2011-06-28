<?php

include 'classes/feed_processing/class.feed_processor.php';

class sitescraper
{
	function scrape($site, $instance, $of) {
		global $db;
		
		switch ($site) {
			case 'easyjet': {
				
				/*
				for ($i=0;$i<=$limit;$i++) {
					$urls[] = 'http://holidays.easyjet.com/Holiday.aspx?PropertyID=' . $i;
				}
				*/
				
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