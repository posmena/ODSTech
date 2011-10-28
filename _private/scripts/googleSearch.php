<?php

include '../classes/feed_processing/class.feed_processor.php';

$google='http://www.google.co.uk/search?q=';
$criteria = 'site:aarongrant.com+aaron+grant';
$start = 0;

$url = $google.$criteria.'&start='.$start;

$data = feed_processor::curl_get_file_contents($url);

function google_search_api($args, $referer = 'http://localhost/test/', $endpoint = 'web'){
	$url = "https://www.googleapis.com/customsearch/v1?key=INSERT-YOUR-KEY&cx=007841803192896622421:fh1hilgx8gy&q=".$criteria."&alt=json";
 
	if ( !array_key_exists('v', $args) )
		$args['v'] = '1.0';
 
	$url .= '?'.http_build_query($args, '', '&');
 
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	// note that the referer *must* be set
	curl_setopt($ch, CURLOPT_REFERER, $referer);
	$body = curl_exec($ch);
	curl_close($ch);
	//decode and return the response
	return json_decode($body);
}
 
$rez = google_search_api(array(
		'q' => 'antique shoes',
 ));
 
print_r($rez);