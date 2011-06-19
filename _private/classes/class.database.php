<?php
set_time_limit(0);
class database extends configuration {

	private $persistantConnection;
	private $exceptionHandler;

	var $query_count = 0;
	var $query_id = "";
	var $result = array();
	var $num_rows = 0;
	var $db_name;

	// private functions
	private function set_persistantConnection($value){
  		if(is_object($value)){
    		$this->persistantConnection = $value;
  		}
	}

	// public functions
	public function get_persistantConnection(){
  		return $this->persistantConnection;
	}

	public function __construct(){
  		$this->exceptionHandler = new errors();
	}

	public function __destruct(){
  		unset($this->persistantConnection);
		unset($this->exceptionHandler);
	}

	public function connection($databaseName){

		$this->db_name = $databaseName;

		$dbConnection = new mysqli($this->get_dbServer(), $this->get_dbUser(), $this->get_dbPassword(), $databaseName);

		if(mysqli_connect_errno()){
			$exceptions = $this->exceptionHandler;
			$exceptions->logException(mysqli_connect_error());

			return false;
		}

		$this->set_persistantConnection($dbConnection);

		return true;
  	}

	public function disconnect(){

		$connection = $this->get_persistantConnection();

		if(is_object($connection)) $connection->close();

		return true;

	}

	public function debugKillConnection($value){
		$this->disconnect();
		die($value);
	}

	public function queryParameter($value, $noQuotes = false){

			$connection = $this->get_persistantConnection();
			if(!$noQuotes){
				return("'" . $connection->real_escape_string($value) . "'");
			}
			else{
				return $connection->real_escape_string($value);
			}
	}

	public function beginTransaction(){
		$connection = $this->get_persistantConnection();
		$connection->query('BEGIN');
	}

	public function rollbackTransaction(){
		$connection = $this->get_persistantConnection();
		$connection->query('ROLLBACK');
	}

	public function commitTransaction(){
  		$connection = $this->get_persistantConnection();
		$connection->query('COMMIT');
	}

	public function getQuery($dbQuery){
		$connection = $this->get_persistantConnection();

		if($this->scriptDebugMode == true){
                $exceptions = $this->exceptionHandler;
                $exceptions->logException("Script Debug - " . $dbQuery);
        }

		if(!$dbResult = $connection->query($dbQuery)){
			$exceptions = $this->exceptionHandler;
			$exceptions->logException($connection->sqlstate . " - " . $connection->error . " - " . $dbQuery);

			if($this->debugMode == true){
				$this->debugKillConnection("MySQL Error: ".$connection->sqlstate . " - " . $connection->error . " - " . $dbQuery);
			}

			return false;
		}

		$dataArray = array();

		while($dataRecord = $dbResult->fetch_assoc()){
			$dataArray[] = $dataRecord;
		}

		$this->num_rows = $dbResult->num_rows;
		$this->query_count++;

		return $dataArray;
	}

	public function getQuerySpecific($dbQuery, $key, $item){

		$connection = $this->get_persistantConnection();

		if(!$dbResult = $connection->query($dbQuery)){
			$exceptions = $this->exceptionHandler;
			$exceptions->logException($connection->sqlstate . " - " . $connection->error);

			return false;
		}

		if($this->scriptDebugMode == true){
  			$exceptions = $this->exceptionHandler;
			$exceptions->logException("Script Debug - " . $dbQuery);
		}

		$dataArray = array();

		while($dataRecord = $dbResult->fetch_assoc()){
			$dataArray[$dataRecord[$key]] = $dataRecord[$item];
		}

		$this->query_count++;

		return $dataArray;

	}

	public function changeQuery($dbQuery){

		$connection = $this->get_persistantConnection();

  		if (!$connection->query($dbQuery)){
			$exceptions = $this->exceptionHandler;
			$exceptions->logException($connection->sqlstate . " - " . $connection->error);

			return false;
  		}
		else{
  			if($this->scriptDebugMode == true){
    			$exceptions = $this->exceptionHandler;
  				$exceptions->logException("Script Debug - " . $dbQuery);
  			}

			$this->query_count++;

  			return true;

		}
	}

    public function multiQuery($dbQuery){

		$connection = $this->get_persistantConnection();

  		if (!$connection->multi_query($dbQuery)){
			$exceptions = $this->exceptionHandler;
			$exceptions->logException($connection->sqlstate . " - " . $connection->error);

			return false;
  		}
		else{
  			if($this->scriptDebugMode == true){
    			$exceptions = $this->exceptionHandler;
  				$exceptions->logException("Script Debug - " . $dbQuery);
  			}

			$this->query_count++;

  			return true;

		}
	}

	public function encryptData($string){
		$hex="";
		for ($i=0;$i<strlen($string);$i++)
			$hex.=(strlen(dechex(ord($string[$i])))<2)? "0".dechex(ord($string[$i])): dechex(ord($string[$i]));

			return $hex;
		}

	public function decryptData($hex){
		$string="";
		for ($i=0;$i<strlen($hex)-1;$i+=2)
			$string.=chr(hexdec($hex[$i].$hex[$i+1]));

			return $string;
	}


    //--------------------------------------
    // Fetch the number of rows in a result set
    //--------------------------------------

    function get_num_rows() {
		return $this->num_rows;
    }

    //--------------------------------------
    // Return the amount of queries used
    //--------------------------------------

    function get_query_cnt() {
        return $this->query_count;
    }

	//--------------------------------------
    // This function compiles the inputs and gives out a nice tidy INSERT query.
    // It saves having to manually format the (INSERT INTO table) ('field', 'field', 'field') VALUES ('val', 'val')
    //--------------------------------------
    //
	//	Example:
	//	$db_string = $DB->compile_db_insert_string( array ( 'field'	=> $val,
	//														'field'	=> $val,
	//														'field' => $val,
	//													   ));
	//	$DB->changeGuery("INSERT INTO ".$INFO['sql_tbl_prefix']."tournaments
	//											(" .$db_string['FIELD_NAMES']. ") VALUES
	//											(". $db_string['FIELD_VALUES'] .")");
	//---------------------------------------
    function compile_db_insert_string($data) {

    	$field_names  = "";
		$field_values = "";

		foreach ($data as $k => $v)
		{
			$v = preg_replace( "/'/", "\\'", $v );
			//$v = preg_replace( "/#/", "\\#", $v );
			$field_names  .= "$k,";
			$field_values .= "'$v',";
		}

		$field_names  = preg_replace( "/,$/" , "" , $field_names  );
		$field_values = preg_replace( "/,$/" , "" , $field_values );

		return array( 'FIELD_NAMES'  => $field_names,
					  'FIELD_VALUES' => $field_values,
					);
	}

	//--------------------------------------
    // This function compiles the inputs and gives out a nice tidy INSERT query.
    // It saves having to manually format the (INSERT INTO table) ('field', 'field', 'field') VALUES ('val', 'val')
    //--------------------------------------
    //
	//	Example:
	//	$db_string = $DB->compile_db_insert_string( array ( 'field'	=> $val,
	//														'field'	=> $val,
	//														'field' => $val,
	//													   ));
	//	$DB->changeQuery("UPDATE tablename SET ".$db_string);
	//---------------------------------------

    function compile_db_update_string($data) {

		$return_string = "";

		foreach ($data as $k => $v)
		{
			$v = preg_replace( "/'/", "\\'", $v );
			$return_string .= $k . "='".$v."',";
		}

		$return_string = preg_replace( "/,$/" , "" , $return_string );

		return $return_string;
	}
	
	function getFields($dbQuery) {
		$connection = $this->get_persistantConnection();

		if($this->scriptDebugMode == true){
                $exceptions = $this->exceptionHandler;
                $exceptions->logException("Script Debug - " . $dbQuery);
        }

		if(!$dbResult = $connection->query($dbQuery)){
			$exceptions = $this->exceptionHandler;
			$exceptions->logException($connection->sqlstate . " - " . $connection->error . " - " . $dbQuery);

			if($this->debugMode == true){
				$this->debugKillConnection("MySQL Error: ".$connection->sqlstate . " - " . $connection->error . " - " . $dbQuery);
			}

			return false;
		}
		
		while ($row = $dbResult->fetch_field()) {
			$fields[$row->name] = $row->name;
		}
		return $fields;
	}

}
?>
