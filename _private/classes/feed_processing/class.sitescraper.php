<?php

include 'classes/feed_processing/class.feed_processor.php';

class sitescraper
{
	function scrape($site, $urls) {
		switch ($site) {
			case 'easyjet': {
				foreach($urls as $url) {
					$info     = '';
					$page     = feed_processor::curl_get_file_contents($url);
					$newlines = array("\t","\n","\r","\x20\x20","\0","\x0B");
					$content  = str_replace($newlines, "", html_entity_decode($page));
					$start    = strpos($content, '<div class="leadInPrice">');
					if ($start === false) {
						continue;
					}
					$end      = strpos($content,'</div>',$start) + 6;
					$info     = substr($content,$start,$end-$start);
					
					$start    = strpos($info, '<em>') + 4;
					if ($start === false) {
						continue;
					}
					$end      = strpos($info,'</em>',$start);
					$price     = substr($info,$start,$end-$start);
					
					print $price."\n";
				}
				break;
			}
		}
	}
}