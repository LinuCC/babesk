<?php
class AdminInventoryProcessing {
	function __construct($inventoryInterface) {

		$this->inventoryInterface = $inventoryInterface;
		global $logger;
		$this->logs = $logger;
		$this->messages = array(
				'error' => array('get_data_failed' => 'Ein Fehler ist beim fetchen der Daten aufgetreten'),
				'notice' => array());
	}
	
	//////////////////////////////////////////////////
	//--------------------Show inventory--------------------
	//////////////////////////////////////////////////
	function ShowInventory($filter) {

		require_once PATH_ACCESS . '/InventoryManager.php';

		$inventoryManager = new InventoryManager();

		try {
			$inventorys = $inventoryManager->getInventorySorted();
		} catch (Exception $e) {
			$this->logs
					->log('ADMIN', 'MODERATE',
							sprintf('Error while getting Data from MySQL:%s in %s', $e->getMessage(), __METHOD__));
			$this->inventoryInterface->dieError($this->messages['error']['get_data_failed']);
		}

		$this->inventoryInterface->ShowInventory($inventorys);
	}

	var $messages = array();
	private $inventoryInterface;

	/**
	 *@var Logger
	 */
	protected $logs;
}

?>