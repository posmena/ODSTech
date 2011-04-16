<?php

/*
+-----------------------------------------
|	Eishar v1.0.0 : class.errors.php
|	======================================
|	by Bob DeVeaux
|	(c) bobdeveaux 1985 - 2006
|	http://www.bobdeveaux.com
|	======================================
|	email:	bob@bobdeveaux.com	
+-----------------------------------------
|	Script Started: 26/04/2006 13:07
+-----------------------------------------
*/

class exceptionHandling{

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

class Debug
{
    function startTimer()
    {
        global $starttime;
        $mtime = microtime ();
        $mtime = explode (' ', $mtime);
        $mtime = $mtime[1] + $mtime[0];
        $starttime = $mtime;
    }
    function endTimer()
    {
        global $starttime;
        $mtime = microtime ();
        $mtime = explode (' ', $mtime);
        $mtime = $mtime[1] + $mtime[0];
        $endtime = $mtime;
        $totaltime = round (($endtime - $starttime), 5);
        return $totaltime;
    }
}
?>