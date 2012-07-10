<?php
class AdminInventoryProcessing {
	function __construct($inventoryInterface) {

		$this->inventoryInterface = $inventoryInterface;
		global $logger;
		$this->logs = $logger;
		$this->messages = array(
				'error' => array('get_data_failed' => 'Ein Fehler ist beim fetchen der Daten aufgetreten',
								'input1' => 'Ein Feld wurde falsch mit ', 'input2' => ' ausgefüllt',
								'change' => 'Konnte das Inventar nicht ändern!',
								'delete' => 'Ein Fehler ist beim löschen des Inventars aufgetreten:'),
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
	
	function editInventory($id) {
		
		require_once PATH_ACCESS . '/InventoryManager.php';
		require_once PATH_ACCESS . '/BookManager.php';
		
		$inventoryManager = new InventoryManager();
		$bookManager = new BookManager();
		
		try {
			$invData = $inventoryManager->getInvDataByID($_GET['ID']);
		} catch (Exception $e) {
			$this->userInterface->dieError($this->messages['error']['uid_get_param'] . $e->getMessage());
		}
		$bookdata = $bookManager->getBookDataByID($invData['book_id']);

		$this->inventoryInterface->ShowChangeInv($bookdata, $invData);
	}
	
	/**
	 * Edits an entry in inventory list.
	 * Changes the MySQL entry
	 */
	
	function changeInventory($old_id, $id, $purchase, $exemplar) {
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
		$inventoryManager->editInv($old_id, $id, $purchase, $exemplar);
	} catch (Exception $e) {
		$this->inventoryInterface->dieError($this->messages['error']['change'] . $e->getMessage());
	}
	$this->inventoryInterface->ShowChangeInvFin($id, $purchase, $exemplar);
	
	}
	
	function DeleteConfirmation($id) {
		$this->inventoryInterface->ShowDeleteConfirmation($id);
	}

	function DeleteEntry($id) {
		require_once PATH_ACCESS . '/InventoryManager.php';
		$inventoryManager = new InventoryManager();
		
		try {
			$inventoryManager->delEntry($id);
		} catch (Exception $e) {
			$this->inventoryInterface->dieError($this->messages['error']['delete'] . $e->getMessage());
		}
		$this->inventoryInterface->ShowDeleteFin();
	}
	
	var $messages = array();
	private $inventoryInterface;

	/**
	 *@var Logger
	 */
	protected $logs;
}

?>