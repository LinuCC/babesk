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
	 * Sorts the book inventory it gets from MySQL-table and returns them.
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
	 * Returns the specify bookdata foreach element of an array with the inventory data.
	 * @param $inventory
	 */
	
	function getBookCodesByInvData($inventory) {
		$bookmanager = new BookManager;
		require_once PATH_ACCESS . '/dbconnect.php';
		foreach ($inventory as &$inventor) {
			$bookinfos[] = $bookmanager->getBookDataById($inventor['book_id']);
		}
		$counter = 0;
		if (isset($bookinfos)){
		foreach ($bookinfos as &$bookinfo) {
			$bookcode[$counter]['id']=$inventory[$counter]['id'];
			$bookcode[$counter]['code']=$bookinfo['subject'].' '.$inventory[$counter]['year_of_purchase'].' '.$bookinfo['class'].' '.$bookinfo['bundle'].' / '.$inventory[$counter]['exemplar'];
			$counter++;
		}
		return $bookcode;
		}
		}
		
		/**
		 * Get inventory data by inventory id.
		 * @param $id
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
	
	function getInvIDByBarcode($barcode) {
		require_once PATH_ACCESS . '/dbconnect.php';
		$bookmanager = new BookManager;
		$bookid = $bookmanager->getBookIDByBarcode($barcode);
		try {
			$barcode_exploded = explode(' ', $barcode);
		} catch (Exception $e) {
		}
		$query = sql_prev_inj(sprintf('book_id = %s AND year_of_purchase = %s AND exemplar = %s' , $bookid["id"], $barcode_exploded[1], $barcode_exploded[5]));
		//$result = $this->db->query($query);
		$result = parent::searchEntry($query);
		return $result;
	}
	
}
?>