<?php

class feed_processor
{
	public static function process_feed($feed_id)
	{
		global $db;
		
		$sql="SELECT f.id, f.name, f.url, f.connection, f.username, f.password, f.filename, COALESCE(n.class_name, f.class_name) as class_name
				FROM pm_feeds f
				LEFT JOIN pm_networks n ON n.id=f.network_id
			  	WHERE f.id=".$feed_id." ORDER BY f.name ASC";
		$feeds = $db->getQuery($sql);
		$path4feed = 'files/feeds/';
		foreach($feeds as $feed)
		{
			print "Processing feed ". $feed['name']."\n";
			switch ($feed['connection']) {
				case 'ftp':
				{
					echo 'Downloading via FTP...';
					$server_file = $feed['filename'];
					//$local_file = $path4feed.'feed'.$feed['id'].'.'.substr(strrchr($server_file,'.'),1);
					$local_file = $path4feed.$feed['filename'];
					
					// set up basic connection
					$conn_id = ftp_connect($feed['url']);
					
					// login with username and password
					$login_result = ftp_login($conn_id, $feed['username'], $feed['password']);
					
					print $login_result;
					
					// try to download $server_file and save to $local_file
					/*
					if (ftp_get($conn_id, $local_file, $server_file, FTP_BINARY)) {
					    echo "Successfully written to $local_file\n";
					} else {
					    echo "There was a problem\n";
					}
					// close the connection
					ftp_close($conn_id);
					*/
					break;
				}
				default:
				{
					$file = $path4feed.'feed'.$feed['id'];
					$data = self::curl_get_file_contents($feed['url']);
					$fp = fopen($file, 'w+');
					fwrite($fp, $data);
					fclose($fp);
				}
			}

			// determine network
			if (true === class_exists($feed['class_name'])) {
				if (false === strpos($feed['class_name'], 'custom')) {
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
					// custom feeds will handle their own shizzle.
					$network = new $feed['class_name']($local_file);
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
	function curl_get_file_contents($url, $limit = null)
	{
		// Output something so we know it's working.
		print "Downloading...\n";//'".$url."'\n";
		flush();
		
		$c = curl_init();
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		
		if ($limit !== null) {
			$offset = 0;
			$size = $limit;
			
			$a = $offset;
			$b = $offset + $size-1;
			curl_setopt($c, CURLOPT_HTTPHEADER, array("Range: bytes=$a-$b") );
		}
		
		curl_setopt($c, CURLOPT_URL, $url);
		curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 5000);
		curl_setopt($c, CURLOPT_TIMEOUT, 10000);
		$contents = curl_exec($c);
		curl_close($c);

		return $contents;
	}

}