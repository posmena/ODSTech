<?php

class core_easyjet_package_feed
{
	private $template = 'n/a';
	private $assignments;
	private $file = '';
	
	public function __construct($db, $qs) {
		$this->file = configuration::APPROOT . '_private/files/hostedfeeds/easyjet/packages.csv';
		
		if (array_key_exists('generate', $qs)) {
			$this->generateFeed($db);
		} elseif (true === file_exists($this->file)) {
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
		$sql = "SELECT raw.id,
		raw.hotel_name, 
		CONCAT('http://holidays.easyjet.com/dl.aspx?mode=FlightPlusHotel&depdate=' , DAY(raw.departure_date) , '/' , MONTH(raw.departure_date) , '/', YEAR(raw.departure_date) , '&nights=', raw.duration, '&adults=2&airport=', out_departure_airport_code, '&resort=', p.ResortID, '&property=', raw.property_id) as 'url',
		raw.country,
		raw.region,
		raw.resort,
		raw.duration,
		raw.board,
		raw.cost,
		raw.currency,
		raw.departure_date,
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
		raw.ret_destination_airport_code,
		p.HotelType,
		p.Image1URL,
		p.Image2URL,
		p.Image3URL,
		p.Description,
		p.Address,
		p.PostCode
		FROM pm_custom_products_easyjet raw
		INNER JOIN pm_scrape_property_lookup lookup ON lookup.scrape_id=raw.property_id
		INNER JOIN Property p ON p.PropertyID=lookup.odst_id";
		$result = $db->getQuery($sql);
		
		$output = '';
		$separator = '","';
		$newline = "\r\n";
		$header = '"id'. $separator .
					'hotel_name' . $separator .
					'url' . $separator .
					'country' . $separator .
					'region' . $separator .
					'resort' . $separator .
					'duration' . $separator .
					'board' . $separator .
					'cost' . $separator .
					'currency' . $separator .
					'departure_date' . $separator .
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
					'ret_destination_airport_code' . $separator .
					'hotel_type' . $separator .
					'additional_image1' . $separator .
					'additional_image2' . $separator .
					'additional_image3' . $separator .
					'description' . $separator .
					'address"' . $newline;
		
		$hn = fopen($this->file, 'w+');
		fwrite($hn, $header);
		fclose($hn);
		$i=0;
		foreach($result as $property) {
			$output .= '"'.$property['id'] . $separator .
							$property['hotel_name'] . $separator .
							$property['url'] . $separator .
							$property['country'] . $separator .
							$property['region'] . $separator .
							$property['resort'] . $separator .
							$property['duration'] . $separator .
							$property['board'] . $separator .
							$property['cost'] . $separator .
							$property['currency'] . $separator .
							$property['departure_date'] . $separator .
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
							$property['ret_destination_airport_code'] . $separator .
							$property['HotelType'] . $separator .
							$property['Image1URL'] . $separator .
							$property['Image2URL'] . $separator .
							$property['Image3URL'] . $separator .
							$property['Description'] . $separator .
							$property['Address'] . '"'.$newline;
			$i++;
			if ($i == 1000) {
				$hn = fopen($this->file, 'a');
				fwrite($hn, $output);
				fclose($hn);
				$output = '';
				$i=0;
			}
		}
		
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