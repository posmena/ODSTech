<?php

/* script to temporarily overwrite files... */

$feed_id = 0;
if (array_key_exists(1, $argv)) {
	$feed_id = $argv[1];
} else {
	print 'Script requires feed_id';
	exit;
}

$temp_files = array(3 => 'scripts/data/jtspas_rrp.csv');

switch ($feed_id) {
	case 'jtspas':
	{
		if (time() > strtotime('Friday 5th August 2011')) {
			//print 'Expired.';
			//exit;
		}

		$conn = new Mongo('localhost');
		// access database
		$mdb = $conn->odstech;
		// access collection
  		$collection = $mdb->dump_jtspas;

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

			$id   = $data[0];
			$item = array();
			$items = $collection->find(array('productid' => $id));

			foreach ($items as $item)
			{
				$item['rrp']   = $data[18];
				//echo $item['rrp']."\n";
				$collection->save($item, array('_id' => $item['_id']));
			}
		}
	}
}

