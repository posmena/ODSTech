<?php

class errors{

	var $logfilename = "ipma_errors.log";
	
	function logException($error, $alertUser = false)
	{
  		$fileHandle = fopen($this->logfilename, "a");
		if($fileHandle)
		{
  		$bytes = fwrite($fileHandle, date("d M Y  H:i:s") . "  -  " . $error . ".\r\n");
			fclose($fileHandle);
			return $bytes;
		}
		else
		{
  		return false;
		}
	}
}