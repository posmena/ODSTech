<?php

$conn = new Mongo('localhost');
// access database
$mdb = $conn->odstech;

switch($argv[1]) {
	case 'easyjet':
	{
		// access collection
		$collection = $mdb->properties_raw;
		$raw        = $collection->find();
		
		$collection = $mdb->properties_scrape;
		$prices     = $collection->find();
		
		$collection = $mdb->easyjet_properties;
		$response = $collection->drop();
		
		foreach($raw as $property) {
			$properties[$property['propertyid']] = $property;
		}
		
		//  only save properties with price
		foreach($prices as $price) {
			$priceProp = $properties[$price['_id']];
			$priceProp['price'] = $price['price'];
			$priceProp['url'] = 'http://holidays.easyjet.com/Holiday.aspx?PropertyID=' . $priceProp['propertyid'];
			$collection->save($priceProp);
		}
		echo $collection->find()->count(). ' properties inserted';
	}
}