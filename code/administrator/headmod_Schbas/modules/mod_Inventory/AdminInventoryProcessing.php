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
								'delete' => 'Ein Fehler ist beim löschen des Inventars aufgetreten:',
								'duplicate' => 'Eintrag bereits vorhanden!'),
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
	
	function changeInventory($id, $purchase, $exemplar) {
		require_once PATH_ACCESS . '/InventoryManager.php';
		$inventoryManager = new InventoryManager();
	try {
		$inventoryManager->editInv($id, $purchase, $exemplar);
	} catch (Exception $e) {
		$this->inventoryInterface->dieError($this->messages['error']['change'] . $e->getMessage());
	}
	$this->inventoryInterface->ShowChangeInvFin($id, $purchase, $exemplar);
	
	}
	
	/**
	 * 
	 * @param unknown_type $id
	 */
	function DeleteConfirmation($id) {
		$this->inventoryInterface->ShowDeleteConfirmation($id);
	}
	
	/**
	 * Show template for adding an entry in inventory list.
	 */
	function AddEntry() {
		$this->inventoryInterface->showAddEntry();
	}
	
	/**
	 * Adds an entry in inventory list.
	 */
	function AddEntryFin($barcode) {
		require_once PATH_ACCESS . '/InventoryManager.php';
		require_once PATH_ACCESS . '/BookManager.php';
		$inventoryManager = new InventoryManager();
		$bookManager = new BookManager();
		try {
			$barcode_exploded = explode(' ', $barcode);
		} catch (Exception $e) {
		}
		try {
			$book_info = $bookManager->getBookIDByBarcode($barcode);
		} catch (Exception $e) {
		}
		try {
			$search = $inventoryManager->searchEntry('book_id',$book_info['id'],'year_of_purchase',$barcode_exploded[1],'exemplar',$barcode_exploded[5]);
			if($search) {
				$this->inventoryInterface->dieError($this->messages['error']['duplicate']);
			} else {			
			$inventoryManager->addEntry('book_id',$book_info['id'],'year_of_purchase',$barcode_exploded[1],'exemplar',$barcode_exploded[5]);
			}
		} catch (Exception $e) {
		}
		
		$this->inventoryInterface->showAddEntryFin($book_info,$barcode_exploded[1],$barcode_exploded[5]);
	}
	/**
	 * 
	 * @param unknown_type $id
	 */
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