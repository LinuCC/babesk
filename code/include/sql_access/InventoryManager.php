<?php
/**
 * Provides a class to manage the book inventory.
 */

require_once PATH_ACCESS . '/TableManager.php';
require_once PATH_ACCESS . '/BookManager.php';

/**
 * Manages the book inventory, provides methods to add/modify the inventory list or to get the inventory list.
 */
class InventoryManager extends TableManager{

	public function __construct() {
		parent::__construct('schbas_inventory');
	}
	
	/**
	 * Sorts the book inventory it gets from MySQL-table and returns them
	 * Enter description here ...
	 */
	function getInventorySorted() {
		require_once PATH_ACCESS . '/dbconnect.php';
		$res_array = array();
		$query = sql_prev_inj(sprintf('SELECT * FROM %s ORDER BY id', $this->tablename));
		$result = $this->db->query($query);
		if (!$result) {
			throw DB_QUERY_ERROR.$this->db->error;
		}
		while($buffer = $result->fetch_assoc())
			$res_array[] = $buffer;
		return $res_array;
	}
	
	/**
	 * 
	 * 
	 */
	
	function getBookCodes($inventory) {
		$bookmanager = new BookManager;
		require_once PATH_ACCESS . '/dbconnect.php';
		foreach ($inventory as &$inventor) {
			$bookinfos[] = $bookmanager->getBookById($inventor['id']);
		}
		$zaehler = 0;
		foreach ($bookinfos as &$bookinfo) {
			$bookcode[$zaehler]['code']=$bookinfo[0]['subject'].' '.$inventory[$zaehler]['year_of_purchase'].' '.$bookinfo[0]['class'].' '.$bookinfo[0]['bundle'].'-'.$inventory[$zaehler]['exemplar'];
			$bookcode[$zaehler]['id']=$inventory[$zaehler]['id'];
			$zaehler++;
		}
		return $bookcode;
		}
		
}
?>