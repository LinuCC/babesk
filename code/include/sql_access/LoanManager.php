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
		parent::__construct('SchbasLending');
	}


	/** Sorts the lending list for a UserID it gets from MySQL-table and returns them
	 * Used by mod_retour !!
	*/
	function getLoanlistByUID($uid) {

		require_once PATH_ACCESS . '/DBConnect.php';
		$res_array = array();
		$query = sql_prev_inj(sprintf('SELECT * FROM %s WHERE user_id = "%s"', $this->tablename, $uid));
		$result = $this->db->query($query);
		if (!$result) {
			/**
			 * @todo Proper Errorhandling here, not this: (wouldnt even execute)
			 * throw DB_QUERY_ERROR.$this->db->error;
			 */
		}
		while($buffer = $result->fetch_assoc())
			$res_array[] = $buffer;
		return $res_array;


	}
}

?>
