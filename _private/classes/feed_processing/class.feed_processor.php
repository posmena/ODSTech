<?php

class feed_processor
{
	public static function process_feed($feed_id)
	{
		global $db;
		
		$sql="SELECT f.id, f.name, f.url, n.class_name
				FROM pm_feeds f
				INNER JOIN pm_networks n ON n.id=f.network_id
			  	WHERE f.id=".$feed_id." ORDER BY f.name ASC";
		$feeds = $db->getQuery($sql);
		$path4feed = 'files/feeds/';
		foreach($feeds as $feed)
		{
			print "Processing feed ". $feed['name']."\n";
			$file = $path4feed.'feed'.$feed['id'];
			$data = self::curl_get_file_contents($feed['url']);
			$fp = fopen($file, 'w+');
			fwrite($fp, $data);
			fclose($fp);

			// determine network
			if (true === class_exists($feed['class_name'])) {
				$network = new $feed['class_name'];
				$products = $network->parse_xml($file, $feed_id);

				// categorise
				// this doesn't look right, but i've been drinking beer...
				self::categorise();
	
				$sql = 'UPDATE pm_feeds af SET af.products=(SELECT count(ap.id) FROM pm_products ap WHERE ap.feed_id=af.id) WHERE af.id='.$feed_id;
				$db->changeQuery($sql);
				if ($products !== false) {
					print $products." products inserted.\nDone.\n";
				}
			} else {
				print 'Class '.$feed['class_name'].' dunt exist\n';
			}
		}
	}

	function categorise()
	{
		global $db;

		$sql = 'SELECT DISTINCT feed_id, Category FROM pm_products';
		$categories = $db->getQuery($sql);
		foreach ($categories as $category)
		{
			$sql = "INSERT INTO pm_categories (id, feed_id, name) VALUES ('','".$category['feed_id']."',".$db->queryParameter($category['Category']).");";
			$db->changeQuery($sql);
		}
	}

	// Function to download a file.
	function curl_get_file_contents($url)
	{
		// Output something so we know it's working.
		print "Downloading...\n";//'".$url."'\n";
		flush();

		$c = curl_init();
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_URL, $url);
		curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 5000);
		curl_setopt($c, CURLOPT_TIMEOUT, 10000);
		$contents = curl_exec($c);
		curl_close($c);

		return $contents;
	}

}