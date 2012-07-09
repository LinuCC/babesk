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
	
	function getBookCodesByInvData($inventory) {
		$bookmanager = new BookManager;
		require_once PATH_ACCESS . '/dbconnect.php';
		foreach ($inventory as &$inventor) {
			$bookinfos[] = $bookmanager->getBookDataById($inventor['id']);
		}
		$counter = 0;
		foreach ($bookinfos as &$bookinfo) {
			$bookcode[$counter]['id']=$inventory[$counter]['id'];
			$bookcode[$counter]['code']=$bookinfo['subject'].' '.$inventory[$counter]['year_of_purchase'].' '.$bookinfo['class'].' '.$bookinfo['bundle'].'-'.$inventory[$counter]['exemplar'];
			$counter++;
		}
		return $bookcode;
		}
		
		/**
		 * 
		 * 
		 */
		
	function getInvDataByID($id) {
		require_once PATH_ACCESS . '/dbconnect.php';
		$query = sql_prev_inj(sprintf('SELECT * FROM %s WHERE id=%s', $this->tablename, $id));
		$result = $this->db->query($query);
		if (!$result) {
			throw DB_QUERY_ERROR.$this->db->error;
		}
		while($buffer = $result->fetch_assoc())
			$res_array = $buffer;
		return $res_array;
	}
	
	function editUser($old_id, $id, $purchase, $exemplar){
		var_dump($old_id);
		var_dump($id);
		var_dump($purchase);
		var_dump($exemplar);
		parent::alterEntry($old_id, 'id', $id, 'year_of_purchase', $purchase, 'exemplar', $exemplar);
	}
}
?>