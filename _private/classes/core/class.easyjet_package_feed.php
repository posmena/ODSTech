<?php

class core_easyjet_package_feed
{
	private $template = 'n/a';
	private $assignments;
	private $file = '/Users/bobbeh/Sites/techodst-dev/_private/files/hostedfeeds/easyjet/packages.csv';
	
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
		$sql = "SELECT 
		CONCAT('http://holidays.easyjet.com/dl.aspx?mode=FlightPlusHotel&depdate=' , DAY(raw.departure_date) , '/' , MONTH(raw.departure_date) , '/', YEAR(raw.departure_date) , '&nights=', raw.duration, '&adults=2&airport=', out_departure_airport_code, '&resort=', p.ResortID, '&property=', raw.property_id) as 'url',
		p.*, raw.*  FROM pm_custom_products_easyjet raw
		INNER JOIN pm_scrape_property_lookup lookup ON lookup.scrape_id=raw.property_id
		INNER JOIN Property p ON p.PropertyID=lookup.odst_id
		LIMIT 0,10";
		$result = $db->getQuery($sql);
		
		$output = '';
		$separator = '","';
		$newline = "\r\n";
		$header = '"name'. $separator .
					'url' . $separator .
					'resort' . $separator .
					'resort_id' . $separator .
					'region' . $separator .
					'country' . $separator .
					'rating' . $separator .
					'hotel_type' . $separator .
					'airport_code' . $separator .
					'description' . $separator .
					'address' . $separator .
					'property_id' . $separator .
					'image1_url' . $separator .
					'postcode"' . $newline;
		foreach($result as $property) {
			$output .= '"'.$property['Name'] . $separator .
							$property['url'] . $separator .
							$property['Resort'] . $separator .
							$property['ResortID'] . $separator .
							$property['Region'] . $separator .
							$property['Country'] . $separator .
							$property['Rating'] . $separator .
							$property['HotelType'] . $separator .
							$property['AirportCode'] . $separator .
							$property['Description'] . $separator .
							$property['Address'] . $separator .
							$property['PropertyID'] . $separator .
							$property['Image1URL'] . $separator .
							$property['PostCode'] . '"'.$newline;
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