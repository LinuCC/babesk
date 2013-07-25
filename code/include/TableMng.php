<?php

require_once PATH_ACCESS . '/DBConnect.php';

/**
 *
 */
class TableMng {
	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	private function __construct () {
		//singleton-class
	}

	/**
	 * Initializes this singleton-class
	 */
	public static function init () {
		self::dbInit ();
	}

	public static function getDb() {
		return self::$db;
	}

	/**
	 * Passes th param as reference and mask it with mysqli::real_escape_string
	 * @param  string $param The string to mask
	 * @return void The parameter gets changed as pass-by-reference
	 */
	public static function sqlEscape(&$param) {
		$param = self::$db->real_escape_string($param);
	}

	/**
	 * Escapes the Elements of an array with real_escape_string
	 *
	 * The Values of the Array need to be references to allow direct change
	 * of the original values, for example
	 * array(&$val1, &$val2, ...)
	 * or you just use the whole array, like
	 * sqlEscapeByArray($_POST)
	 * Theres no return-value, the array is passed as reference
	 */
	public static function sqlEscapeByArray(&$array) {
		foreach($array as &$param) {
			$param = self::$db->real_escape_string($param);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/**
	 * Executes a query
	 * @param $query the SQL-Query to execute
	 * @param $hasData false if the query returns no data, true if the query returns data and this function should also return it. Standard is false
	 * @param $isMultiple true if the query does contain multiple SQL-Querys, else false. Standard is false
	 * @return Array () if $hasData has been set to true, it returns the fetched data as an Array
	 */
	public static function query ($query, $hasData = false, $isMultiple = false) {
		if (!isset (self::$db)) {
			throw new Exception ('TableMng hasnt been initialized yet!');
		}
		if (!$isMultiple) {
			$result = self::queryExecute ($query, $isMultiple);
			if ($hasData) {
				$content = self::getResults ($result);
			}
		}
		else {
			$content = self::queryMultiExecute ($query);
		}
		if (isset ($content)) {
			return $content;
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Inits the Database of this Class
	 */
	protected static function dbInit () {
		if (!isset(self::$db)) {
			$dbObject = new DBConnect();
			$dbObject->initDatabaseFromXML();
			self::$db = $dbObject->getDatabase();
			self::$db->query('set names "utf8";');
		}
	}

	/**
	 * Executes the Query
	 */
	protected static function queryExecute ($query) {
		$result = self::$db->query($query);
		if (!$result) {
			throw new MySQLConnectionException(DB_QUERY_ERROR . self::$db->error . "<br />" . $query);
		}
		return $result;
	}

	/**
	 * Executes multiple Querys and fetches the result
	 */
	protected static function queryMultiExecute ($query) {
		$result = NULL;
		if (self::$db->multi_query ($query)) {
			$rows = array();
			do {
				if($result = self::$db->store_result ()) {
					while ($row = $result->fetch_assoc ()) {
						$rows [] = $row;
					}
					$result->free ();
				}
				if (self::$db->errno) {
					throw new MySQlException ('Error:' . self::$db->error);
				}
				if(!self::$db->more_results ()) {
					break;
				}
			} while (self::$db->next_result ());
		}
		else {
			throw new MySQLException (sprintf('Error executing a MySQL-Command: %s, because: %s', $query, self::$db->error));
		}

		if(self::$db->errno) {
			throw new MySQLException (sprintf('Error executing a MySQL-Command: %s, because: %s', $query, self::$db->error));
		}

		return $rows;
	}

	/**
	 * fetches the Data of the Result of a MySQL-Query
	 */
	protected static function getResults ($result) {

		while($buffer = $result->fetch_assoc()) {
			$content [] = $buffer;
		}
		if(empty($content)) {
			return array();
		}
		else {
			return $content;
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	public static $db;

}

?>
