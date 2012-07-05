<?php
/**
 * Provides a class to manage the users of the system
 */

require_once PATH_ACCESS . '/TableManager.php';

/**
 * Manages the users, provides methods to add/modify users or to get user data
 */
class InventoryManager extends TableManager{

	public function __construct() {
		parent::__construct('users');
	}
	
	/**
	 * Sorts the users it gets from MySQL-table and returns them
	 * Enter description here ...
	 */
	function getInventorySorted() {
		//@todo:need refactoring
		require_once PATH_ACCESS . '/dbconnect.php';
		$res_array = array();
		$query = sql_prev_inj(sprintf('SELECT * FROM %s ORDER BY name', $this->tablename));
		$result = $this->db->query($query);
		if (!$result) {
			throw DB_QUERY_ERROR.$this->db->error;
		}
		while($buffer = $result->fetch_assoc())
			$res_array[] = $buffer;
		return $res_array;
	}
	
}
?>