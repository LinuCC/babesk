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
	function getLoanByUID($uid) {
		require_once PATH_ACCESS . '/dbconnect.php';
		require_once PATH_ACCESS . '/UserManager.php';
		require_once PATH_ACCESS . '/BookManager.php';
		$userManager = new UserManager;
		$bookManager = new BookManager;
		$details = $userManager->getUserDetails($uid);
		$books = $bookManager->getBooksByClass($details['class']);
		return $books;
	}
	
	/**
	 * Remove an entry in the loan list by a given user id and inventory id
	 */
	function RemoveLoanByIDs($inventoryID, $uid) {
		require_once PATH_ACCESS . '/dbconnect.php';
		
		
		$query = sql_prev_inj(sprintf('user_id = %s AND inventory_id = %s' , $uid, $inventoryID));
		$result = parent::delEntryNoID($query);
		return $result;
	}
	
}
?>