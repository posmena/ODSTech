<?php

$search = $_GET['query'];

if(strlen($search) < 3 )
{
die();
}

$str = curl_get_file_contents('http://www3.hilton.com/sayt/?q=' . urlencode($search) . '&min=2&max=12&params[locale]=en_GB&searchType=ALL');

echo('searchAsYouType.handleAjaxResponse({"query":"' . $search . '","results":' . $str . '});');

function curl_get_file_contents($url, $limit = null)
	{
		// Output something so we know it's working.
		flush();
		
		$c = curl_init();

		curl_setopt($c, CURLOPT_ENCODING, 'UTF-8');

		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
		
		if ($limit !== null) {
			$offset = 0;
			$size = $limit;
			
			$a = $offset;
			$b = $offset + $size-1;
			curl_setopt($c, CURLOPT_HTTPHEADER, array("Range: bytes=$a-$b") );
		}
		
		curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($c, CURLOPT_URL, $url);
		curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 15000);
		curl_setopt($c, CURLOPT_TIMEOUT, 110000);

		$contents = curl_exec($c);
		curl_close($c);

		return $contents;
	}
	
?>