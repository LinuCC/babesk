<?php
/**
 * @TODO: deleting things should give feedback if error
 * @TODO: adding things should give feedback if error
 * @TODO: changing things should give feedback if error
 */
class DbAccessMultiple {
	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////
	public function __construct ($db, $tablename) {
		$this->_db = $db;
		$this->_tablename = $tablename;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////
	/**
	 * Adds a Row to change. This Row should already have its searchFields
	 * and processFields filled out.
	 */
	public function rowAdd ($row) {
		$this->_rows [] = $row;
	}

	public function dbExecute ($function) {
		$wholeQuery = '';
		foreach ($this->_rows as $row) {
			$processStr = $this->fieldsAppendAsStr ($row->processFieldGetAll ());
			$searchStr = $this->fieldsAppendAsSearchStr ($row->searchFieldGetAll ());
			$wholeQuery .= $this->queryCommandGet ($function, $searchStr, $processStr, $row);
		}
		return $this->queryExecute ($wholeQuery);
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function queryCommandGet ($function, $searchStr, $processStr, $row) {
		switch ($function) {
			case self::$Delete:
				$query = $this->deleteDbExec ($searchStr);
				break;
			case self::$Insert:
				$query = $this->insertDbExec ($row);
				break;
			case self::$Alter:
				$query = $this->alterDbExec ($processStr, $searchStr);
				break;
			case self::$Fetch:
				$query = $this->fetchDbExec ($searchStr);
				break;
		}
		return $query;
	}

	protected function alterDbExec ($processStr, $searchStr) {
		$query = '';
		$query .= sprintf ('UPDATE %s SET %s WHERE %s; ', $this->_tablename, $processStr, $searchStr);
		return $query;
	}

	protected function insertDbExec ($row) {
		$fields = array ('keys' => '', 'values' => '');
		$insertValues = $row->processFieldGetAll ();
		foreach ($insertValues as $value) {
			$fields ['keys'] .= $value->key . ', ';
			$fields ['values'] .= '"' . $value->value .'", ' ;
		}
		$fields ['keys'] = rtrim($fields ['keys'], ' ,');
		$fields ['values'] = rtrim($fields ['values'], ', ');
		$query = '';
		$query .= sprintf ('INSERT INTO %s (%s) VALUES (%s);', $this->_tablename, $fields ['keys'], $fields ['values']);
		return $query;
	}

	protected function deleteDbExec ($searchStr) {
		$query = '';
		$query .= sprintf ('DELETE FROM %s WHERE %s; ', $this->_tablename,$searchStr);
		return $query;
	}

	protected function fetchDbExec ($searchStr) {
		$query = '';
		$query .= sprintf ('SELECT * FROM %s WHERE %s; ', $this->_tablename,$searchStr);
		return $query;
	}

	protected function queryExecute ($query) {
		sql_prev_inj ($query); // Prevent Sql-Injection
		$result = NULL;
		if ($this->_db->multi_query ($query)) {
			$rows = array();
			do {
				if($result = $this->_db->store_result ()) {
					while ($row = $result->fetch_assoc ()) {
						$rows [] = $row;
					}
					$result->free ();
				}
				if ($this->_db->errno) {
					throw new MySQlException ('Error:' . $this->_db->error);
				}
				if(!$this->_db->more_results ()) {
					break;
				}
			} while ($this->_db->next_result ());
		}
		else {
			throw new MySQlException (sprintf('Error executing a MySQL-Command: %s, because: %s', $query, $this->_db->error));
		}
		return $rows;
	}

	protected function fieldsAppendAsStr ($fields) {
		$fieldsStr = '';
		if (count($fields)) {
			foreach ($fields as $field) {
				$fieldsStr .= sprintf ('%s= "%s", ', $field->key, $field->value);
			}
		}
		$fieldsStr = rtrim ($fieldsStr, ' ,');
		return $fieldsStr;
	}
	protected function fieldsAppendAsSearchStr ($fields) {
		$fieldsStr = '';
		if (count($fields)) {
			foreach ($fields as $field) {
				$fieldsStr .= sprintf ('%s= "%s" AND ', $field->key, $field->value);
			}
		}
		$fieldsStr = rtrim ($fieldsStr, ' AND');
		return $fieldsStr;
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////
	protected $_rows;
	protected $_db;
	public static $Alter = '0';
	public static $Insert = '1';
	public static $Delete = '2';
	public static $Fetch = '3';
}

class DbAMRow {
	public function processFieldAdd ($key, $value) {
		$this->_processFields [] = new DbAMField ($key, $value);
	}

	public function searchFieldAdd ($key, $value) {
		$this->_searchFields [] = new DbAMField ($key, $value);
	}

	public function processFieldGetAll () {
		return $this->_processFields;
	}

	public function searchFieldGetAll () {
		return $this->_searchFields;
	}

	protected $_tablename;
	/**
	 * The fields of the Row that should be processed; either changed or fetched
	 */
	protected $_processFields;
	/**
	 * The fields with which to find the row in the Database
	 */
	protected $_searchFields;
}

/**
 * A field of a row of a table
 */
class DbAMField {
	public function __construct ($key, $value) {
		$this->key = $key;
		$this->value = $value;
	}
	public $key;
	public $value;
}

?>