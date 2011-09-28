<?php

class ODSTech_xmlgenerator
{
	public function generate($name, $filter)
	{
		$filepath = configuration::APPROOT . '_private/files/compressedfeeds/' . $name . '/' . $filter . '.csv.zip';

		$expandedPath = configuration::APPROOT . '_private/files/inflated/' . $name . '/';

		if (false === file_exists($filepath)) {
			throw new Exception ('File: ' . $filepath . ' does not exist');
		}

		shell_exec('unzip -jo ' . $filepath . ' -d ' . $expandedPath);

		$handle     = fopen($expandedPath . $filter . '.csv', 'r');
		$comma      = ",";
		$i          = 0;
		$xmlDoc     = '<?xml version="1.0" encoding="UTF-8" ?>';
		$xmlContent = '<rss version ="2.0" xmlns:g="http://base.google.com/ns/1.0">';
		$xmlContent .= '<channel>';

		$xmlContent .= '<title>'. $name . ' Datafeed</title>';
		$xmlContent .= '<description>List of all our products.</description>';
		$xmlContent .= '<link>http://www.odst.co.uk/'.$name . '</link>';

		while ($data = fgetcsv($handle, null, $comma)) {
			$i++;

			if ($i == 1) {
				$headers = $data;
				continue;
			}
			
			$xmlContent .= '<item>';
			foreach ($headers as $key => $heading) {
				if ($heading == 'product_type' || $heading == 'description' || $heading == 'title' || $heading == 'delivery_time') {
					$content = '<![CDATA[' .htmlentities($data[$key]) . ']]>';
				} else {
					$content = $data[$key];
				}
				
				$xmlContent .= '<g:' . $heading . '>' . $content . '</g:' . $heading . '>' . "\n";	
				
			}
			$xmlContent .= '</item>';
		}
		$xmlContent .= '</channel>';
		$xmlContent .= '</rss>';
		
		$xmlDoc .= $xmlContent;
		fclose($handle);

		$xmlPath = configuration::APPROOT . '_private/files/hostedfeeds/' . $name . '/' . $filter . '.xml';

		$handle = fopen($xmlPath, 'w+');
		fwrite($handle, $xmlDoc);
		fclose($handle);
	}
}