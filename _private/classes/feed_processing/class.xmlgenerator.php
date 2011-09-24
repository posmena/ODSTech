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
		$xmlContent = '<products>';

		while ($data = fgetcsv($handle, null, $comma)) {
			$i++;

			if ($i == 1) {
				$headers = $data;
				continue;
			}
			
			$xmlContent .= '<product>';
			foreach ($headers as $key => $heading) {
				$xmlContent .= '<' . $heading . '>' . $data[$key] . '</' . $heading . '>' . "\n";
				
			}
			$xmlContent .= '</product>';
		}
		$xmlContent .= '</products>';
		
		$xmlDoc .= $xmlContent;
		fclose($handle);

		$xmlPath = configuration::APPROOT . '_private/files/hostedfeeds/' . $name . '/' . $filter . '.xml';

		$handle = fopen($xmlPath, 'w+');
		fwrite($handle, $xmlDoc);
		fclose($handle);
	}
}