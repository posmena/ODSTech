<?php

include 'classes/feed_processing/class.feed_processor.php';

class sitescraper
{
	function scrape($site) {
		global $db;
		
		switch ($site) {
			case 'easyjet': {
				
				/*
				for ($i=0;$i<=$limit;$i++) {
					$urls[] = 'http://holidays.easyjet.com/Holiday.aspx?PropertyID=' . $i;
				}
				*/
				
				$sql    = 'SELECT PropertyID FROM Property ORDER BY PropertyID ASC';
				$result = $db->getQuery($sql);
				foreach ($result as $url) {
					$urls[$url['PropertyID']] = 'http://holidays.easyjet.com/Holiday.aspx?PropertyID=' . $url['PropertyID'];
				}
				
				$limit = count($urls);
				$timeStart = time();
				$sql = 'INSERT INTO pm_scrape_ejh_property (id, name, tagline, price) VALUES ';
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
					
					$values .= "(" . $PropertyId . "," . $db->queryParameter($name) . "," . $db->queryParameter($tag) . "," . str_replace(',','',$price) . "),";
					$timeTaken = time() - $timeStart;
					if($x++ == 10) {
						$values = substr($values,0,-1);
						$qry = $sql . $values;
						if($db->changeQuery($qry) === false) {
							echo $qry;
							print("\n\nerror\n\n");
						}
						$values = '';
						echo $x . ' records inserted. Progress ' . $i . '/' . $limit . " (" . $timeTaken . "s)\n";
						$x = 0;
					} else {
						echo 'Progress ' . $i . '/' . $limit . " - (" . $x	 . ") (" . $timeTaken . "s)\n";
					}
				}
				
				// clean up remainder
				if ($x > 0) {
					$values = substr($values,0,-1);
					$qry = $sql . $values;
					$db->changeQuery($qry);
				}
				
				break;
			}
		}
	}
}