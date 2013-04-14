<?php

if (class_exists('util') === false) {
	include 'classes/class.util.php';
}

class feed_processor
{
	public static function process_feed($feed_id, $full, $search)
	{
		global $db;
		$count = 0;
			
		$collection = $db->ot_feeds;
		
		if( false === isset($search) )
			{
			$cursor = $collection->find(array('client' => $feed_id));
			}
		else
			{
			// merge the search array 
			$arr = array_merge(array('client' => $feed_id), $search);
			
			$cursor = $collection->find($arr);
			}
			
		foreach ($cursor as $item) {		
			$feeds[] = $item;
		}
		
		$path4feed = 'files/feeds/';
		foreach($feeds as $feed)
		{
			print "Processing feed ". $feed['feedname']."\n";

			switch ($feed['connection']) {
				case 'ftp':
				{
					echo "Downloading via FTP...\n";
					$server_file = $feed['filename'];
					$local_file  = $path4feed.$feed['filename'];
					// set up basic connection
					$conn_id = ftp_connect($feed['url']);
					
					// login with username and password
					$login_result = ftp_login($conn_id, $feed['username'], $feed['password']);
					
					print $login_result;
					print $local_file;
					
					// try to download $server_file and save to $local_file
					
					if (ftp_get($conn_id, $local_file, $server_file, FTP_BINARY)) {
					    echo "Successfully written to $local_file\n";
					} else {
					    echo "\nThere was a problem\n\n";
					    print_r(error_get_last());
					}
					
					// close the connection
					ftp_close($conn_id);
					
					// if need to unzip then unzip
					if( isset($feed['zipped']) && true === $feed['zipped'] )
						{
						print("Unzip $local_file");
							$unzipped = util::unzip($local_file,$feed['format']);
							print("UNZIPEED to " . $unzipped);
							
							if( $unzipped !== false )
								{
								$local_file = $unzipped;
												
							}
						}
						
					break;
				}
				default:
				{
					$format = "";
					if( isset($feed['format']) )
						{
						$format = "." . $feed['format'];
						}
					$local_file = $path4feed.'feed'.$feed_id.$format;
					print("Downloading from html:" . $feed['url']."\n");
					$data = self::curl_get_file_contents($feed['url']);
					print("Finished downloading");
					$fp = fopen($local_file, 'w+');
					fwrite($fp, $data);
					fclose($fp);
					
					// if need to unzip then unzip
					if( isset($feed['zipped']) && true === $feed['zipped'] )
						{
						print("Unzip $local_file");
							$unzipped = util::unzip($local_file,$feed['format']);
							print("UNZIPEED to " . $unzipped);
							
							if( $unzipped !== false )
								{
								$local_file = $unzipped;
												
							}
						}
				}
			}

			// determine network
			if (true === class_exists($feed['classname'])) {
				{
					print("\nHERE 112\n");
					// custom feeds will handle their own shizzle.
					
					$network = new $feed['classname']($local_file, $full, $feed_id);
					
					
					if( method_exists($network,'process') )
						{
						$network->process();
						}
					$count = $network->num_products;
					print("Inserted $count products");
					if( $count ) 
						{
						$feed['last_update_state'] = 1;
						$collection->save($feed);
						}
					
				}
			} else {
				print 'Class '.$feed['classname'].' dunt exist\n';
			}
		}
		
		return $count;
	}

	public static function pre_process_feed($feed_id, $full)
	{
		global $db;
		
		$collection = $db->ot_feeds;
	
		$cursor = $collection->find(array('client' => $feed_id));
			
		echo("pre_process_feed");	
		foreach ($cursor as $item) {
		
		$item['last_update_state'] = 0;
		$feed['last_updated'] = new MongoDate();
					
		$collection ->save($item);
		
			$feeds[] = $item;
		}
		
		
		foreach($feeds as $feed)
		{
			
			// determine network
			if (true === class_exists($feed['classname'])) {
				{
					print("\nHERE\n");
					// custom feeds will handle their own shizzle.
					
					$network = new $feed['classname']('', $full, $feed_id);
					$network->pre_process();
					return;
					
				}
			} else {
				print 'Class '.$feed['classname'].' dunt exist\n';
			}
		}
		
		
	}
	
	
	public static function post_process_feed($feed_id, $full)
	{
		global $db;
		
		echo($feed_id);
		$collection = $db->ot_feeds;
	
		$cursor = $collection->find(array('client' => $feed_id));
			
		$success = true;
		foreach ($cursor as $item) {
		
		 if( $item['last_update_state'] == 0 ) 
			{
			$success = false;
			break;
			}
		$feeds[] = $item;
		}
		
		
		
			
		foreach($feeds as $feed)
		{
			
			// determine network
			if (true === class_exists($feed['classname'])) {
				{
					print("\nHERE\n");
					// custom feeds will handle their own shizzle.
					
					$network = new $feed['classname']('', $full, $feed_id);
					$network->post_process($success);
					return;
					
				}
			} else {
				print 'Class '.$feed['classname'].' dunt exist\n';
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

		curl_setopt($c, CURLOPT_ENCODING, 'UTF-8');

		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
		
		if ($limit !== null) {
			$offset = 0;
			$size = $limit;
			
			$a = $offset;
			$b = $offset + $size-1;
			curl_setopt($c, CURLOPT_HTTPHEADER, array("Range: bytes=$a-$b") );
		}
		
		curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($c, CURLOPT_URL, $url);
		curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 15000);
		curl_setopt($c, CURLOPT_TIMEOUT, 110000);

		$contents = curl_exec($c);
		curl_close($c);

		return $contents;
	}

}