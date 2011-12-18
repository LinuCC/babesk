<?php

/**
 *
 * Template-Class which contains non-specific functions for access to the table
 * It should be the parent of all the other Manager-Classes in the access-files.
 *
 * @author Pascal Ernst, Informatik-AG LeG
 *
 */

require_once 'exception_def.php';

class TableManager {

	/**
	 * ClassConstructor, sets up connection to database
	 * It connects with the database. The Information for that is given in dbconnect.php.
	 *
	 * @param the name of the table in MySQL
	 */
	public function __construct($tablename) {
		require "dbconnect.php";
		$this->db = $db;
		$this->tablename = $tablename;
	}

	/**
	 * Returns the value of the requested fields for the given id or all entries in database.
	 *
	 * The Function takes a variable amount of parameters, the first being the element id,
	 * the other parameters are interpreted as being the fieldnames in the table.
	 * The data will be returned in an array with the fieldnames being the keys.
	 *
	 * @param variable amount, see description
	 * @return false if error
	 */
	public function getEntryData() {

		require_once 'constants.php';

		$num_args = func_num_args();
			
		if($num_args == 1) {
			//all data of the specific entry
			$id = func_get_arg(0);
			$query = sql_prev_inj(sprintf('SELECT * FROM %s WHERE ID=%s', 
											$this->tablename, $id));
		}
		else if($num_args > 1){
			//specific TableData
			$id = func_get_arg(0);
			$fields = "";

			for($i = 1; $i < $num_args - 1; $i++) {
				$fields .= func_get_arg($i).',';
			}
			$fields .= func_get_arg($num_args - 1);  //query must not contain an ',' after the last field name

			$query = 'SELECT
    					'.$fields.'
    				FROM
    					'.$this->tablename.'
    				WHERE
    					ID = '.$id.'';
		}
		else{//wrong arguments
			throw new BadMethodCallException('wrong arguments');
		}
		$result = $this->db->query($query);
		if (!$result) {
			throw new MySQLConnectionException(DB_QUERY_ERROR.$this->db->error."<br />".$query);
		}
		if($res_var = $result->fetch_assoc()) {
			return $res_var;
		}
		else {
			throw new MySQLVoidDataException(DB_QUERY_ERROR.$this->db->error."<br />".$query);
		}
	}


	/**
	 * Its function is to get several entries from the MySQL-Server, selected by the given Parameter
	 * getTableData() accepts zero or one Parameter. If zero Parameters are given, the function will return
	 * all of the data in the table. If one Parameter is given, it will interpreted as being the string behind
	 * the WHERE-command of the MySQL-query-string.
	 *
	 *  @param func_get_arg(0) The mySQL-query-string behind the WHERE-command. Example: ID = 46.
	 *  		If the values are strings, do not forget the quotation-marks.
	 *
	 *  @return twodimensional-array: $return_var[EntryArray[FieldnameArray]]
	 */
	function getTableData() {
		require_once 'constants.php';

		$num_args = func_num_args();
		$args = func_get_args();

		if($num_args == 0){
			//all elements of the table
			$query = 'SELECT * FROM '.$this->tablename;
		}
		else if($num_args == 1){
			$arg = func_get_arg(0);
			$query = 'SELECT * FROM '.$this->tablename.' WHERE '.$arg;
		}
		else {
			throw new Exception('Wrong number of arguments in '.__METHOD__);
		}
		$result = $this->db->query($query);
		if (!$result) {
			throw new MySQLConnectionException(DB_QUERY_ERROR.$this->db->error."<br />".$query);
		}
		while($buffer = $result->fetch_assoc())$res_array[] = $buffer;
		if(!isset($res_array)) {
			throw new MySQLVoidDataException('MySQL returned no data!');
		}
		return $res_array;
	}

	/**
	 * Adds an entry, taking variable amount of parameters
	 * This function adds an entry to the table. Parameters needed are the fields that
	 * should get filled out (if something like AUTO_INCREMENT in MySQL should be used, dont give var and identifier as parameter.)
	 * First Parameter is the Column-Identifier (like ID, Date,...) the second is the value of it,
	 * the third one the second column-Identifier, the fourth the value of it and so on.
	 *
	 * @param variable amount, even number. see description.
	 */
	public function addEntry() {
		require_once 'constants.php';

		$column_identifier_str = '';
		$column_value_str = '';

		$num_args = func_num_args();

		if(($num_args % 2 == 1)) {
			throw new Exception(ERR_NUMBER_PARAM);
		}

		for($i = 1; $i <= $num_args; $i++) {
			if($i % 2 == 1) {
				//identifier

				$column_identifier_str .= func_get_arg($i - 1).',';
			}
			else { //value
				if (func_get_arg($i - 1) == 'CURRENT_TIMESTAMP'){
					// some mysql-constants, that shouldnt need quotation marks
					$column_value_str .= func_get_arg($i - 1).',';
				}
				else if(!is_numeric(func_get_arg($i - 1))) {
					//MySQL needs quotation marks for strings
					$column_value_str .= '"'.func_get_arg($i - 1).'",';
				}
				else {
					$column_value_str .= func_get_arg($i - 1).',';
				}
			}
		}

		//no need for kommata after last entry because of MySQL
		$column_value_str = substr($column_value_str,0,-1);
		$column_identifier_str = substr($column_identifier_str,0,-1);

		$query = 'INSERT INTO '.$this->tablename.'('
		.$column_identifier_str.')'.
					'VALUES ('.$column_value_str.');';

		$result = $this->db->query($query);
		if(!$result) {
			throw new Exception(DB_QUERY_ERROR.$this->db->error);
		}
	}
	
	/**
	 * Alters a table-entry of MySQL
	 * This function takes a variable amount of parameters, the first being the ID of the object to
	 * change, the second one the name of the value to change nad the third one the value.
	 * You can update more than one value, you then add another two parameters;
	 * the name of the value and then the value itself.
	 * 
	 * @throws MySQLVoidDataException if it could not connect to MySQL or some error was produced by MySQL
	 * @throws BadMethodCallException if the number of parameters are not correct
	 * 
	 * @param a variable amount, see description
	 */
	public function alterEntry () {
		$args = func_get_args();
		if(count($args) < 1 || count($args) % 2 != 1) {
			throw new BadMethodCallException('Wrong number of Parameters for Function '.__FUNCTION__); 
		}
		$ID = $args[0];
		$set_str = '';
		//starts with one cause arg[0] is the ID
		for($i = 1; isset($args[$i]); $i += 1) {
			switch ($i % 2) {
				case 0: // A value
					if(!is_numeric($args[$i])) {
						$args[$i] = '"'.$args[$i].'"';
					}
					$set_str .= $args[$i].',';
					break;
				case 1:	// A string describing what value should be set
					$set_str .= $args[$i].'=';
					break;
			}
		}
		$set_str = substr($set_str, 0, -1);
		$query = 'UPDATE '.$this->tablename.' SET '.$set_str.' WHERE ID='.$ID;
		$result = $this->db->query($query);
		if(!$result) {
			throw new MySQLConnectionException(DB_QUERY_ERROR.$this->db->error);
		}
	}

	/**
	 * Deletes an entry from the table
	 * Delete the entry from the table which owns the given ID
	 *
	 * @param ID The ID of the entry to delete
	 */
	public function delEntry($ID) {
		require_once 'constants.php';

		if(!is_numeric($ID)){
			//parameter-checking
			throw new UnexpectedValueException(ERR_TYPE_PARAM_ID);
		}
		$query = 'DELETE FROM
		               '.$this->tablename.'
                  WHERE ID = '.$ID.';';
		$result = $this->db->query($query);
		if (!$result) {
			throw new Exception(DB_QUERY_ERROR.$this->db->error);
		}
	}

	/**
	 * Contains the MySQL-tablename
	 * @var string
	 */
	protected $tablename;

	/**
	 * Contains the database to which to connect
	 * @var MySQLi-Object
	 */
	protected $db;

}

?>