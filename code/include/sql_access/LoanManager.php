<?php
/**
 * Provides a class to manage the booklist of the system
 */

require_once PATH_ACCESS . '/TableManager.php';

/**
 * Manages the lending list, provides methods to add/modify the lending list or to get information from the lending list.
 */
class LoanManager extends TableManager{

	public function __construct() {
		parent::__construct('schbas_lending');
	}
	
	/**
	 * Sorts the lending list for a UserID it gets from MySQL-table and returns them
	 * Enter description here ...
	 */
	function getLoanByID($uid) {
		require_once PATH_ACCESS . '/dbconnect.php';
		$res_array = array();
		$query = sql_prev_inj(sprintf('SELECT * FROM %s WHERE user_id = %s', $this->tablename, $uid));
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