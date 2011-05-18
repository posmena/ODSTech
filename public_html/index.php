<?php

function __autoload($class_name) 
{
	if (false !== strpos($class_name, 'core')) {
		$class_name = str_replace('core_', '', $class_name);
		$include_file = '../_private/classes/core/class.' . $class_name . '.php';
	} else {
		$include_file = '../_private/classes/class.' . $class_name . '.php';
	}
	if (file_exists($include_file) === true)
	{
    	include $include_file;
	} else {
		echo 'There was an error importing the ' . $include_file . ' class.';	
	}
}
Session_Start();
$site = new core_odstech_base;
$site->direct();
$site->draw();
/*
http://holidays.easyjet.com/dl.aspx?mode=FlightPlusHotel&depdate=20/06/2011&nights=7&adults=2airport=LGW&resort=455

SELECT 
CONCAT('http://holidays.easyjet.com/dl.aspx?mode=FlightPlusHotel&depdate=' , DAY(raw.departure_date) , '/' , MONTH(raw.departure_date) , '/', YEAR(raw.departure_date) , '&nights=', raw.duration, '&adults=2&airport=', out_departure_airport_code, '&resort=', p.ResortID) as 'url',
p.*, raw.*  FROM pm_custom_products_easyjet raw
INNER JOIN pm_scrape_property_lookup lookup ON lookup.scrape_id=raw.property_id
INNER JOIN Property p ON p.PropertyID=lookup.odst_id
LIMIT 0,1
*/