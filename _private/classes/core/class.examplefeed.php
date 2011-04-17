<?php

class core_examplefeed
{
	private $template = 'n/a';
	private $assignments;
	
	public function __construct($db) {
		$sql = 'SELECT ejh.price, p . * 
				FROM  `Property` p
				INNER JOIN pm_scrape_property_lookup s ON s.odst_id = p.PropertyID
				INNER JOIN pm_scrape_ejh_property ejh ON ejh.id = s.scrape_id
				LIMIT 0 , 30';
		$result = $db->getQuery($sql);
		
		$output = '';
		$separator = '","';
		$newline = "\r\n";
		$header = '"name'. $separator .
					'price' . $separator .
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
							$property['price'] . $separator .
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
		echo $output;
		
		
		exit;
	}
	
	
	public function getTemplate() {
		return $this->template;
	}
	public function getAssignments() {
		return $this->assignments;
	}
}