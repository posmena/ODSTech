<?php

/* script to temporarily overwrite files... */

$feed_id = 0;
if (array_key_exists(1, $argv)) {
	$feed_id = $argv[1];
} else {
	print 'Script requires feed_id';
	exit;
}

$temp_files = array(3 => 'scripts/data/jtspasextra2_5ProductAll.csv');

switch ($feed_id) {
	case 3:
	{
		if (time() > strtotime('Friday 5th August 2011')) {
			print 'Expired.';
			exit;
		}

		$conn = new Mongo('localhost');
		// access database
		$mdb = $conn->odstech;
		// access collection
  		$collection = $mdb->jtspas;

		$local_file = $temp_files[3];
		$handle     = fopen($local_file, 'r');
		$comma      = ",";
		$i = 0;

		while ($data = fgetcsv($handle, null, $comma)) {
			$i++;
			if($i == 1) {
				continue;
			}
			// check we split on the comma
			if (count($data) == 1) {
				$data = explode($comma, $data[0]);
			}
			
			// skip blank lines
			if (count($data) < 2) {
				echo 'Column mismatch around line: '.$i."\n";
				continue;
			}

			$id   = 'jtspasni'.$data[2];
			$item = array();
			$items = $collection->find(array('id' => $id));
			foreach ($items as $item)
			{
				$item['price'] = $data[5];
				$collection->save($item, array('_id' => $item['_id']));
			}
		}
	}
}

