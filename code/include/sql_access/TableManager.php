<?php

/**
 *
 * Template-Class which contains non-specific functions for access to the table
 * It should be the parent of all the other Manager-Classes in the access-files.
 *
 * @author Pascal Ernst, Informatik-AG LeG
 *
 */

require_once PATH_INCLUDE . '/functions.php';
require_once PATH_INCLUDE . '/exception_def.php';

class TableManager {

	/**
	 * ClassConstructor, sets up connection to database
	 * It connects with the database. The Information for that is given in databaseDistributor.php.
	 *
	 * @param the name of the table in MySQL
	 */

	public function __construct ($tablename) {

		require "databaseDistributor.php";
		$this->db = $db;
		$this->db->query('set names "utf8";');
		$this->tablename = $tablename;
	}

	/**
	 * looks up if the table does exist
	 */
	public function existsTable () {

		$query = sql_prev_inj('SHOW TABLES LIKE "' . $this->tablename . '"');
		$result = $this->db->query($query);
		if (!count($result->fetch_assoc())) {
			return false;
		}
		else {
			return true;
		}
	}

	/**
	 * Checks if the Key $key of the table exists
	 * @param unknown_type $key
	 */
	public function existsKey ($columnName) {
		
		$query = sql_prev_inj('SHOW COLUMNS FROM `' . $this->tablename . '`  LIKE "'. $columnName . '";');
		$result = $this->db->query($query);
		if(count($result->fetch_assoc())) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Checks if the Entry with the value $value of the key $key exists.
	 * @param string $key
	 * @param string $value
	 * @throws MySQLConnectionException
	 * @return boolean true if an Entry exists, false if not
	 */
	public function existsEntry ($key, $value) {
		$query = sql_prev_inj(sprintf('SELECT * FROM %s WHERE %s="%s"', $this->tablename, $key, $value));
		$result = $this->db->query($query);
		if (!$result)
			throw new MySQLConnectionException(DB_QUERY_ERROR . $this->db->error . "<br />" . $query);
		if ($res_var = $result->fetch_assoc()) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * returns the ID of the first entry found with the value $value of the key $key
	 * @param string $key
	 * @param string $value
	 */
	public function getIDByValue ($key, $value) {
		$result = $this->searchEntry(sprintf('%s="%s"', $key, $value));
		if (!$result)
			throw new MySQLVoidDataException('MySQL returned no Data to retrieve the ID from!');
		if (array_key_exists('ID', $result))
			return $result['ID'];
		else if (array_key_exists('Id', $result))
			return $result['Id'];
		else if (array_key_exists('id', $result))
			return $result['id'];
		else
			throw new Exception('No ID-Key found!');

	}

	/**
	 * Returns the value of the requested fields for the given id or all entries in database.
	 *
	 * The Function takes a variable amount of parameters, the first being the element id,
	 * the other parameters are interpreted as being the fieldnames in the table.
	 * The data will be returned in an array with the fieldnames being the keys.
	 *
	 * @param variable amount, see description
	 * @return false if error, else array
	 */
	public function getEntryData () {

		require_once PATH_INCLUDE . '/constants.php';

		$num_args = func_num_args();

		if ($num_args == 1) {
			//all data of the specific entry
			$id = func_get_arg(0);
			$query = sql_prev_inj(sprintf('SELECT * FROM %s WHERE ID=%s', $this->tablename, $id));
		}
		else if ($num_args > 1) {
			//specific TableData
			$id = func_get_arg(0);
			$fields = "";

			for ($i = 1; $i < $num_args - 1; $i++) {
				$fields .= func_get_arg($i) . ',';
			}
			$fields .= func_get_arg($num_args - 1); //query must not contain an ',' after the last field name

			$query = sql_prev_inj(sprintf('SELECT %s FROM %s WHERE ID = %s', $fields, $this->tablename, $id));
		}
		else { //wrong arguments
			throw new BadMethodCallException('wrong arguments');
		}
		$result = $this->db->query($query);
		if (!$result) {
			throw new MySQLConnectionException(DB_QUERY_ERROR . $this->db->error . "<br />" . $query);
		}
		if ($res_var = $result->fetch_assoc()) {
			return $res_var;
		}
		else {
			throw new MySQLVoidDataException(DB_QUERY_ERROR . $this->db->error . "<br />" . $query);
		}
	}

	/**
	 * Its function is to get several entries from the MySQL-Server, selected by the given Parameter
	 * getTableData() accepts zero or one Parameter. If zero Parameters are given, the function will return
	 * all of the data in the table. If one Parameter is given, it will interpreted as being the string behind
	 * the WHERE-command of the MySQL-query-string.
	 *
	 *  @param func_get_arg(0) The mySQL-query-string behind the WHERE-command. Example: ID = 46.
	 *  		If the values are strings, do not forget the quotation-marks!.
	 *
	 *  @return twodimensional-array: $return_var[EntryArray[FieldnameArray]]
	 */
	function getTableData () {
		require_once PATH_INCLUDE . '/constants.php';

		$num_args = func_num_args();
		$args = func_get_args();

		if ($num_args == 0) {
			//all elements of the table
			$query = sql_prev_inj(sprintf('SELECT * FROM %s', $this->tablename));
		}
		else if ($num_args == 1) {
			$arg = func_get_arg(0);
			$query = sql_prev_inj(sprintf('SELECT * FROM %s WHERE %s', $this->tablename, $arg));
		}
		else {
			throw new Exception('Wrong number of arguments in ' . __METHOD__);
		}
		$result = $this->db->query($query);
		if (!$result) {
			throw new MySQLConnectionException(DB_QUERY_ERROR . $this->db->error . "<br />" . $query);
		}
		while ($buffer = $result->fetch_assoc())
			$res_array[] = $buffer;
		if (!isset($res_array)) {
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
	 * @throws Exception
	 */

	public function addEntry () {
		require_once PATH_INCLUDE . '/constants.php';

		$column_identifier_str = '';
		$column_value_str = '';

		$num_args = func_num_args();

		if (($num_args % 2 == 1)) {
			throw new Exception(ERR_NUMBER_PARAM);
		}

		for ($i = 1; $i <= $num_args; $i++) {
			if ($i % 2 == 1) {
				//identifier

				$column_identifier_str .= func_get_arg($i - 1) . ',';
			}
			else { //value
				if (func_get_arg($i - 1) == 'CURRENT_TIMESTAMP') {
					// some mysql-constants, that shouldnt need quotation marks
					$column_value_str .= func_get_arg($i - 1) . ',';
				}
				//is_numeric killed the zeros leading numbers, problem with telephonenumber. (0581/642 etc. ftw)
				else if (/*!is_numeric*/(func_get_arg($i - 1))) {
					//MySQL needs quotation marks for strings
					$column_value_str .= '"' . func_get_arg($i - 1) . '",';
				}
				else {
					$column_value_str .= func_get_arg($i - 1) . ',';
				}
			}
		}

		//no need for kommata after last entry because of MySQL
		$column_value_str = substr($column_value_str, 0, -1);
		$column_identifier_str = substr($column_identifier_str, 0, -1);

		$query = sql_prev_inj(sprintf('INSERT INTO %s (%s) VALUES (%s);', $this->tablename, $column_identifier_str,
			$column_value_str));
		$result = $this->db->query($query);
		if (!$result) {
			throw new Exception(DB_QUERY_ERROR . $this->db->error);
		}
	}

	/**
	 * Searches for an entry.
	 * the function will return the first item found
	 * @param string $search_str The string of the MySQL-query behind "WHERE"
	 * @throws UnexpectedValueException When one of the parameters has the wrong typ
	 */

	public function searchEntry ($search_str) {
		
		if (!is_string($search_str))
			throw new UnexpectedValueException('One of the Parameters has the wrong format!');

		$result_arr = $this->getTableData($search_str);
		if (!isset($result_arr) || !count($result_arr)) {
			throw new MySQLVoidDataException('MySQL returned void Data');
		}
		$result = $result_arr[0];
		return $result;
	}

	/**
	 * Returns exactly one value for a specific key from MySQL
	 * This function needs the ID of the object and the key to the value it is supposed to return
	 * @param numeric_string $id
	 * @param string $key
	 */

	public function getEntryValue ($id, $key) {
		$query = sql_prev_inj(sprintf('SELECT %s FROM %s WHERE ID=%s', $key, $this->tablename, $id));
		$result = $this->db->query($query);
		if (!$result) {
			throw new MySQLConnectionException(DB_QUERY_ERROR . $this->db->error . "<br />" . $query);
		}
		if ($res_var = $result->fetch_assoc()) {
			return $res_var[$key];
		}
		else {
			throw new MySQLVoidDataException(DB_QUERY_ERROR . $this->db->error . "<br />" . $query);
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
		if (count($args) < 1 || count($args) % 2 != 1) {
			throw new BadMethodCallException('Wrong number of Parameters for Function ' . __FUNCTION__);
		}
		$ID = $args[0];
		$set_str = '';
		//starts with one cause arg[0] is the ID
		for ($i = 1; isset($args[$i]); $i += 1) {
			switch ($i % 2) {
				case 0: // A value
					if (!is_numeric($args[$i])) {
						$args[$i] = '"' . $args[$i] . '"';
					}
					$set_str .= $args[$i] . ',';
					break;
				case 1: // A string describing what value should be set
					$set_str .= $args[$i] . '=';
					break;
			}
		}
		$set_str = substr($set_str, 0, -1);
		$query = sql_prev_inj(sprintf('UPDATE %s SET %s WHERE ID = %s', $this->tablename, $set_str, $ID));
		$result = $this->db->query($query);
		if (!$result) {
			throw new MySQLConnectionException(DB_QUERY_ERROR . $this->db->error);
		}
	}

	/**
	 * Deletes an entry from the table
	 * Delete the entry from the table which owns the given ID
	 *
	 * @param ID The ID of the entry to delete
	 */

	public function delEntry ($ID) {
		require_once PATH_INCLUDE . '/constants.php';

		if (!is_numeric($ID)) {
			//parameter-checking
			throw new UnexpectedValueException(ERR_TYPE_PARAM_ID);
		}
		$query = sql_prev_inj(sprintf('DELETE FROM %s WHERE ID="%s";', $this->tablename, $ID));
		$result = $this->db->query($query);
		if (!$result) {
			throw new MySQLConnectionException(DB_QUERY_ERROR . $this->db->error);
		}
	}

	/**
	 * returns the ID of the next object that would be added (MySQL's Autoincrement)
	 */
	function getNextAutoIncrementID () {
		$query = sql_prev_inj(sprintf('SELECT Auto_increment FROM information_schema.tables WHERE table_name="%s";',
			$this->tablename));
		$result = $this->db->query($query);
		if (!$result)
			throw new MySQLConnectionException(DB_QUERY_ERROR . $this->db->error);
		$nextID = $result->fetch_assoc();
		if (!$nextID || $nextID == '')
			throw new MySQLVoidDataException('MySQL returned no data for last autoincrementID');
		return $nextID['Auto_increment'];
	}
	
	/**
	 * returns the ID of the last inserted Object (only if using Auto_increment either in SQL or manually. If an
	 * entry with an ID lower than the highest ID is added, this function will not work properly for this entry.
	 * 
	 * @param string $idKeyName The name if the ID-Key. If nothing given, the function will assume the name "ID"
	 */
	function getLastInsertedID ($idKeyName = 'ID') {
		
		$query = sql_prev_inj(sprintf('SELECT MAX(%s) AS LastID FROM %s', $idKeyName, $this->tablename));
		$result = $this->db->query($query);
		
		if(!$result) {
			throw new MySQLConnectionException(DB_QUERY_ERROR . $this->db->error);
		}
		$valueRes = $result->fetch_assoc();
		$lastID = $valueRes ['LastID'];
		if(!$lastID || $lastID == '') {
			throw new MySQLVoidDataException('MySQL returned no data for ID of last row added');
		}
		return $lastID;
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

	/**
	 * A logs-object to get things logged in the MySQL-Server
	 * @var Logger-Object (@see logs.php)
	 */
	protected $logs;

}

?>