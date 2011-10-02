<?php

class errors{

	private static $logfilename = "odst_errors.log";
	
	function logException($error, $alertUser = false)
	{
  		$fileHandle = fopen(self::$logfilename, "a");
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