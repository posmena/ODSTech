<?php

class core_easyjet_package_feed
{
	private $template = 'n/a';
	private $assignments;
	private $file = '';
	private $isHome = false;
	
	public function __construct($db, $qs) {
		
		if (false === array_key_exists('region', $qs) && false === array_key_exists('propertylist')) {
			print 'Region/Property must be supplied';
			exit;
		}
		if (false === array_key_exists('propertylist', $qs)) {
			$this->file = configuration::APPROOT . '_private/files/compressedfeeds/easyjet/region' . ucfirst($qs['region']) . '.csv.zip';
			
			if (true === file_exists($this->file)) {
				header("Content-type: application/octet-stream");
				header("Content-Disposition: attachment; filename=\"easyjet-packages-" .$qs['region']. ".csv.zip\"");
				header("Content-type: application/force-download"); 
			    //header("Content-length: ".filesize($this->file)); 
			    readfile($this->file);
				exit; 
			} else {
				print 'File does not exist';
				exit;
			}
		} else {
			$this->file = configuration::APPROOT . '_private/files/compressedfeeds/easyjet/propertiesfull.csv.zip';
			if (true === file_exists($this->file)) {
				header("Content-type: application/octet-stream");
				header("Content-Disposition: attachment; filename=\"easyjet-properties.csv.zip\"");
				header("Content-type: application/force-download"); 
			    //header("Content-length: ".filesize($this->file)); 
			    readfile($this->file);
				exit; 
			} else {
				print 'File does not exist';
				exit;
			}
		}
	}
	/*
	public function generateFeed($db)
	{
		echo 'Generating Feed...';
		$tools = new core_admin_tools($db, false);
		$client = $tools->getClient(1);
		$feeds = $client->getFeeds();
		
		foreach($feeds as $feed) {
			
			$fieldselects = '';
			$header = '"'.
			$separator = '","';
			$newline = "\r\n";
			$filters = '';
			foreach($feed['fields'] as $field) {
				$fieldselects .= $field['fieldname'].',';
				$header .= $field['name'].$separator;
			}
			
			foreach ($feed['filters'] as $filter) {
				$filters .= 'AND ' . $filter['fieldname'] . '=\'' . $filter['criteria'] .'\'';
			}
			$fieldselects .= 'raw.id as dummy_id';		
			$header .= 'dummy id"'.$newline;
			$sql = "SELECT ";
			$sql .= $fieldselects;
			$sql .= " FROM pm_custom_products_easyjet raw
			INNER JOIN pm_scrape_property_lookup lookup ON lookup.scrape_id=raw.property_id
			INNER JOIN Property p ON p.PropertyID=lookup.odst_id
			WHERE 1=1 ";
			$sql .= $filters;
			$sql .= " LIMIT %d, %d";
			$output = '';
			$hn = fopen($this->file, 'w+');
			fwrite($hn, $header);
			fclose($hn);
			
			echo $sql;
			die();

			$rowsPerQuery = 20000;
			$i = 0;
			$done = false;
			while($done === false) {
				$start = $i*$rowsPerQuery;
				$query = sprintf($sql, $start, $rowsPerQuery);
				$result = $db->getQuery($query);
				$x = 0;
				$output = '';
				foreach ($result as $property) {
					$output .= '"';
					
					foreach($feed['fields'] as $field) {
						$fieldparts = explode('.', $field['fieldname']);
						if (array_key_exists($fieldparts[1], $property) === true) {
							$output .= $property[$fieldparts[1]] . $separator;
						} elseif (array_key_exists($field['name'], $property) === true) {
							$output .= $property[$field['name']] . $separator;
						}
					}
					
					$output .= 'dummy"'.$newline;
					$x++;
				}
				$hn = fopen($this->file, 'a');
				fwrite($hn, $output);
				fclose($hn);
				$i++;
	
				if ($x < $rowsPerQuery) {
					echo $x . 'wooo';
					$done = true;
				} else {
					echo $rowsPerQuery*$i."\n";
					if ($rowsPerQuery*$i > 20000) {
						echo 'fail safe 20k product limit for now';
						$done = true;
					}		
				}
			}

			print "\n";
			print 'File Generated';
			exit;
		}
		
	}
	 * *
	 */
	
	
	public function getTemplate() {
		return $this->template;
	}
	public function getAssignments() {
		return $this->assignments;
	}
	
	public function isHome() {
		return $this->isHome;
	}
}
