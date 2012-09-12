<?php
class AdminSpecificProcessing {
	function __construct($SpecificInterface) {

		$this->SpecificInterface = $SpecificInterface;
		global $logger;
		$this->logs = $logger;
		$this->messages = array(
				'error' => array());
	}
	
	var $messages = array();

	/**
	 *@var Logger
	 */
	protected $logs;
	
	/**
	 * Shows Specific
	 * @param unknown_type $filter
	 */
	function ShowSpecific($filter) {
	
		require_once PATH_ACCESS . '/SpecificManager.php';
	
		$specificManager = new SpecificManager();
	
		try {
			$specific = $specificManager->getSpecificSorted();
		} catch (Exception $e) {
			$this->logs
			->log('ADMIN', 'MODERATE',
					sprintf('Error while getting Data from MySQL:%s in %s', $e->getMessage(), __METHOD__));
			$this->specificInterface->dieError($this->messages['error']['get_data_failed']);
		}
		$this->SpecificInterface->ShowSpecific($specific);
	}
	
	/**
	 * Edits an entry in Specific list.
	 * Function to show the template.
	 */
	
	function editSpecific($id) {
	
		require_once PATH_ACCESS . '/SpecificManager.php';
	
		$specificManager = new SpecificManager();
	
		try {
			$specificData = $specificManager->getSpecificDataByID($_GET['ID']);
		} catch (Exception $e) {
			$this->userInterface->dieError($this->messages['error']['uid_get_param'] . $e->getMessage());
		}
	
		$this->SpecificInterface->ShowChangeSpecific($specificData);
	}
	
	/**
	 * Edits an entry in Specific list.
	 * Changes the MySQL entry
	 */
	
	function changeSpecific($id, $subject, $class, $title, $author, $publisher, $isbn, $price, $bundle) {
		require_once PATH_ACCESS . '/SpecificManager.php';
		$specificManager = new SpecificManager();
		try {
			$specificManager->editSpecific($id, $subject, $class, $title, $author, $publisher, $isbn, $price, $bundle);
		} catch (Exception $e) {
			$this->specificInterface->dieError($this->messages['error']['change'] . $e->getMessage());
		}
		$this->SpecificInterface->ShowChangeSpecificFin($id, $subject, $class, $title, $author, $publisher, $isbn, $price, $bundle);
	
	}
	
	
}

?>