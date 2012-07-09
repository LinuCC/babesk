<?php
class AdminInventoryProcessing {
	function __construct($inventoryInterface) {

		$this->inventoryInterface = $inventoryInterface;
		global $logger;
		$this->logs = $logger;
		$this->messages = array(
				'error' => array('get_data_failed' => 'Ein Fehler ist beim fetchen der Daten aufgetreten',
								'input1' => 'Ein Feld wurde falsch mit ', 'input2' => ' ausgefüllt',
								'change' => 'Konnte den Benutzer nicht ändern!'),
				'notice' => array());
	}
	
	//////////////////////////////////////////////////
	//--------------------Show inventory--------------------
	//////////////////////////////////////////////////
	function ShowInventory($filter) {
		
		require_once PATH_ACCESS . '/InventoryManager.php';

		$inventoryManager = new InventoryManager();

		try {
			$inventory = $inventoryManager->getInventorySorted();
		} catch (Exception $e) {
			$this->logs
					->log('ADMIN', 'MODERATE',
							sprintf('Error while getting Data from MySQL:%s in %s', $e->getMessage(), __METHOD__));
			$this->inventoryInterface->dieError($this->messages['error']['get_data_failed']);
		}
		
		try {
			$bookcodes = $inventoryManager->getBookCodesByInvData($inventory);
		} catch (Exception $e) {
			$this->logs
					->log('ADMIN', 'MODERATE',
							sprintf('Error while getting Data from MySQL:%s in %s', $e->getMessage(), __METHOD__));
			$this->inventoryInterface->dieError($this->messages['error']['get_data_failed']);
		}
		
		$this->inventoryInterface->ShowInventory($bookcodes);
	}
	
	/**
	 * Edits an entry in inventory list.
	 * Function to show the template.
	 */
	
	function editEntry($id) {
		
		require_once PATH_ACCESS . '/InventoryManager.php';
		require_once PATH_ACCESS . '/BookManager.php';
		
		$inventoryManager = new InventoryManager();
		$bookManager = new BookManager();
		
		$bookdata = $bookManager->getBookDataByID($_GET['ID']);
		try {
			$invData = $inventoryManager->getInvDataByID($_GET['ID']);
		} catch (Exception $e) {
			$this->userInterface->dieError($this->messages['error']['uid_get_param'] . $e->getMessage());
		}
		var_dump ($invData);

		$this->inventoryInterface->ShowChangeInv($bookdata, $invData);
	}
	
	/**
	 * Edits an entry in inventory list.
	 * Changes the MySQL entry
	 */
	
	function changeUser($old_id, $id, $purchase, $exemplar) {
		require_once PATH_ACCESS . '/InventoryManager.php';
		$inventoryManager = new InventoryManager();
		
	try {
			inputcheck($id, 'id');
		} catch (Exception $e) {
			$this->inventoryInterface
					->dieError(
							$this->messages['error']['input1'] . '"' . $e->getMessage() . '"'
									. $this->messages['error']['input2']);
		}
	try {
		$inventoryManager->editUser($old_id, $id, $purchase, $exemplar);
	} catch (Exception $e) {
		$this->inventoryInterface->dieError($this->messages['error']['change'] . $e->getMessage());
	}
	}
	/**
	 * 
	 * @var unknown_type
	 */

	function deleteEntry($id) {
		echo 'Whats up?';
	}
	
	var $messages = array();
	private $inventoryInterface;

	/**
	 *@var Logger
	 */
	protected $logs;
}

?>