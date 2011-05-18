<?php

class core_easyjet_package_feed
{
	private $template = 'n/a';
	private $assignments;
	private $file = '/Users/bobbeh/Sites/techodst-dev/_private/files/hostedfeeds/easyjet/packages.csv';
	//private $file = '/var/www/techodst-dev/_private/files/hostedfeeds/easyjet/packages.csv';
	
	
	public function __construct($db, $qs) {
		if (array_key_exists('generate', $qs)) {
			$this->generateFeed($db);
		} elseif (true === file_exists($this->file))
		{
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=\"easyjet-packages.csv\"");
			header("Content-type: application/force-download"); 
		    header("Content-length: ".filesize($this->file)); 
		    readfile($this->file);
			exit; 
		} else {
			print 'File does not exist';
			exit;
		}
	}
	
	public function generateFeed($db)
	{
		echo 'Generating Feed...';
		$sql = "SELECT raw.hotel_name, 
		CONCAT('http://holidays.easyjet.com/dl.aspx?mode=FlightPlusHotel&depdate=' , DAY(raw.departure_date) , '/' , MONTH(raw.departure_date) , '/', YEAR(raw.departure_date) , '&nights=', raw.duration, '&adults=2&airport=', out_departure_airport_code, '&resort=', p.ResortID, '&property=', raw.property_id) as 'url',
		raw.country,
		raw.region,
		raw.resort,
		raw.duration,
		raw.board,
		raw.cost,
		raw.currency,
		raw.departure_date,
		raw.package_url,
		raw.rating,
		raw.property_id,
		raw.image_url, 
		raw.room_type,
		raw.out_departure_airport_code,
		raw.out_departure_airport_name,
		raw.out_flight_departure_date,
		raw.out_destination_airport_name,
		raw.out_destination_airport_code,
		raw.ret_departure_airport_code,
		raw.ret_departure_airport_name,
		raw.ret_flight_departure_date,
		raw.ret_destination_airport_name,
		raw.ret_destination_airport_code
		FROM pm_custom_products_easyjet raw
		INNER JOIN pm_scrape_property_lookup lookup ON lookup.scrape_id=raw.property_id
		INNER JOIN Property p ON p.PropertyID=lookup.odst_id
		LIMIT 0,10";
		$result = $db->getQuery($sql);
		
		$output = '';
		$separator = '","';
		$newline = "\r\n";
		$header = '"hotel_name'. $separator .
					'url' . $separator .
					'country' . $separator .
					'region' . $separator .
					'resort' . $separator .
					'duration' . $separator .
					'board' . $separator .
					'cost' . $separator .
					'currency' . $separator .
					'departure_date' . $separator .
					'package_url' . $separator .
					'rating' . $separator .
					'image_url' . $separator .
					'room_type' . $separator .
					'out_departure_airport_code' . $separator .
					'out_departure_airport_name' . $separator .
					'out_flight_departure_date' . $separator .
					'out_destination_airport_name' . $separator .
					'out_destination_airport_code' . $separator .
					'ret_departure_airport_code' . $separator .
					'ret_departure_airport_name' . $separator .
					'ret_flight_departure_date' . $separator .
					'ret_destination_airport_name' . $separator .
					'ret_destination_airport_code"' . $newline;
		foreach($result as $property) {
			$output .= '"'.$property['hotel_name'] . $separator .
							$property['url'] . $separator .
							$property['country'] . $separator .
							$property['region'] . $separator .
							$property['resort'] . $separator .
							$property['duration'] . $separator .
							$property['board'] . $separator .
							$property['cost'] . $separator .
							$property['currency'] . $separator .
							$property['departure_date'] . $separator .
							$property['package_url'] . $separator .
							$property['rating'] . $separator .
							$property['image_url'] . $separator .
							$property['room_type'] . $separator .
							$property['out_departure_airport_code'] . $separator .
							$property['out_departure_airport_name'] . $separator .
							$property['out_flight_departure_date'] . $separator .
							$property['out_destination_airport_name'] . $separator .
							$property['out_destination_airport_code'] . $separator .
							$property['ret_departure_airport_code'] . $separator .
							$property['ret_departure_airport_name'] . $separator .
							$property['ret_flight_departure_date'] . $separator .
							$property['ret_destination_airport_name'] . $separator .
							$property['ret_destination_airport_code'] . '"'.$newline;
		}
		
		$output = $header . $output;
		
		$hn = fopen($this->file, 'w+');
		fwrite($hn, $output);
		fclose($hn);
		print "\n";
		print 'File Generated';
		exit;
	}
	
	
	public function getTemplate() {
		return $this->template;
	}
	public function getAssignments() {
		return $this->assignments;
	}
}