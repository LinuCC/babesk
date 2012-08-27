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
								'duplicate' => 'Eintrag bereits vorhanden!',
								'uid_get'=> 'Konnte keinen Inventareintrag finden'),
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
	 * @param $id
	 */
	
	function editInventory($id) {
		
		require_once PATH_ACCESS . '/InventoryManager.php';
		require_once PATH_ACCESS . '/BookManager.php';
		
		$inventoryManager = new InventoryManager();
		$bookManager = new BookManager();
		
		try {
			$invData = $inventoryManager->getInvDataByID($_GET['ID']);
		} catch (Exception $e) {
			$this->logs->log('ADMIN', 'MODERATE',
					sprintf('Error while getting Data from MySQL:%s in %s', $e->getMessage(), __METHOD__));
			$this->userInterface->dieError($this->messages['error']['uid_get'] . $e->getMessage());
		}
		$bookdata = $bookManager->getBookDataByID($invData['book_id']);

		$this->inventoryInterface->ShowChangeInv($bookdata, $invData);
	}
	
	/**
	 * Edits an entry in inventory list.
	 * Changes the MySQL entry
	 * @param $id
	 * @param $purchase
	 * @param $exemplar
	 */
	
	function changeInventory($id, $purchase, $exemplar) {
		require_once PATH_ACCESS . '/InventoryManager.php';
		$inventoryManager = new InventoryManager();
	try {
		$inventoryManager->alterEntry($id, 'year_of_purchase', $purchase, 'exemplar', $exemplar);
	} catch (Exception $e) {
		$this->logs->log('ADMIN', 'MODERATE',
				sprintf('Error while edit Data in MySQL:%s in %s', $e->getMessage(), __METHOD__));
		$this->inventoryInterface->dieError($this->messages['error']['change'] . $e->getMessage());
	}
	$this->inventoryInterface->ShowChangeInvFin($id, $purchase, $exemplar);
	
	}
	
	
	/**
	 * Show template for adding an entry in inventory list.
	 */
	function AddEntry() {
		$this->inventoryInterface->showAddEntry();
	}
	
	/**
	 * Adds an entry into inventory list.
	 * @param $barcode
	 */
	function AddEntryFin($barcode) {
		require_once PATH_ACCESS . '/InventoryManager.php';
		require_once PATH_ACCESS . '/BookManager.php';
		$inventoryManager = new InventoryManager();
		$bookManager = new BookManager();
		$barcode = preg_replace("/\/([0-9])/", "/ $1", $barcode); //add space after / when it's missing
		$barcode = str_replace("  ", " ", $barcode); // remove two empty spaces
		$barcode_exploded = explode(' ', $barcode);
		try {
			$book_info = $bookManager->getBookIDByBarcode($barcode);
		} catch (Exception $e) {
			$this->logs
			->log('ADMIN', 'MODERATE',
					sprintf('Error while getting Data from MySQL:%s in %s', $e->getMessage(), __METHOD__));
			$this->inventoryInterface->dieError($this->messages['error']['get_data_failed']);
		}
		try {
			$search = $inventoryManager->searchEntry('book_id='.$book_info['id'].' AND year_of_purchase='.$barcode_exploded[1].' AND exemplar='.$barcode_exploded[5]);
		}catch (Exception $e){
			$search = 0;
		}
		if($search) {
				$this->inventoryInterface->dieError($this->messages['error']['duplicate']);
			} else {
				try {			
					$inventoryManager->addEntry('book_id',$book_info['id'],'year_of_purchase',$barcode_exploded[1],'exemplar',$barcode_exploded[5]);
				}catch (Exception $e) {
					$this->logs
					->log('ADMIN', 'MODERATE',
							sprintf('Error while getting Data from MySQL:%s in %s', $e->getMessage(), __METHOD__));
					$this->inventoryInterface->dieError($this->messages['error']['get_data_failed']);
				}
			}
		
		$this->inventoryInterface->showAddEntryFin($book_info,$barcode_exploded[1],$barcode_exploded[5]);
	}
	
	/**
	 * Shows the template for confirmation of an delete request.
	 * @param $id
	 */
	function DeleteConfirmation($id) {
		$this->inventoryInterface->ShowDeleteConfirmation($id);
	}
	
	
	/**
	 * Deletes an entry from MySQL.
	 * @param $id
	 */
	function DeleteEntry($id) {
		require_once PATH_ACCESS . '/InventoryManager.php';
		$inventoryManager = new InventoryManager();
		
		
		try {
			$inventoryManager->delEntry($id);
		} catch (Exception $e) {
			$this->logs
			->log('ADMIN', 'MODERATE',
					sprintf('Error while deleting Data from MySQL:%s in %s', $e->getMessage(), __METHOD__));
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