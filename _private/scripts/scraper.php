<?php
 
$urls = array ('http://holidays.easyjet.com/egypt/egypt/hurghada/al-mas-palace-resort.htm',
			   'http://holidays.easyjet.com/greece/halkidiki/kassandra---paliouri/chrousso-village.htm',
			   'http://holidays.easyjet.com/spain/costa-de-la-luz/cadiz/hotel-jerez-and-spa.htm');
			   

include 'classes/feed_processing/class.sitescraper.php';

sitescraper::scrape('easyjet', $urls);

