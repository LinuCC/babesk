<?php

class AdminLogProcessing {
	
	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	private $logManager;
	private $msg;
	private $logInterface;
	/**
	 * @var Logger
	 */
	private $logger;
	
	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($adminInterface) {
		
		require_once PATH_INCLUDE . '/logs.php';
		
		//$logger comes from logs.php
		global $logger;
		$this->logger = $logger;
		$this->logInterface = $adminInterface;
		$this->msg = array(
				'err_no_logs' => 'Es konnten keine Logs mit den angegebenen Eigenschaften gefunden werden.',
				'err_logs' => 'Ein Fehler ist beim Abrufen der Logs aufgetreten.',
				'err_category' => 'Es wurde keine oder eine falsche Log-Kategorie eingegeben.',
				);
	}
	
	////////////////////////////////////////////////////////////////////////////////
	//Methods
	/**
	 * Shows the logs with a specific category and severity
	 * @param string $category The Category of the Logs to show
	 * @param string $severity The Severity of the Logs to show
	 */
	public function ShowLogs($category, $severity) {
		
		$logs = $this->getLogDataByCategoryAndSeverity($category, $severity);
		$this->logInterface->ShowLogs($logs);
	}
	
	/**
	 * Displays a dialog with which the User can choose a category
	 */
	public function ChooseCategory() {
		
		$logs = $this->GetLogs();
		$categories = $this->PoolCategories($logs);
		$this->logInterface->ChooseCategory($categories);
	}
	
	/**
	 * Displays a dialog with which the User can choose a severity
	 * @param string $category The Category which was selected by the user beforehand
	 */
	public function ChooseSeverity($category) {
		
		if(!isset($category) || !$category) {
			$this->logInterface->ShowError($this->msg['err_category']);
		}
		
		$logs = $this->GetLogDataByCategory($category);
		$severitys = $this->PoolSeveritys($logs);
		$this->logInterface->ChooseSeverity($severitys, $category);
	}
	
	/**
	 * Deletes all Logs
	 */
	public function DeleteLogs() {
		$this->logger->clearLogs();
	}
	
	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	/**
	 * returns Logs which are from the Category $category and Severity $severity
	 * @param string $category
	 * @param string $severity
	 * @return array() an Array of logs
	 */
	function getLogDataByCategoryAndSeverity($category, $severity) {
		
		try {
			$logs = $this->logger->getLogDataByCategoryAndSeverity($category, $severity);
		} catch (MySQLVoidDataException $e) {
			$this->logInterface->ShowError($this->msg['err_no_logs']);
		} catch (Exception $e) {
			$this->logInterface->ShowError($this->msg['err_logs'] . $e->getMessage());
		}
		return $logs;
	}
	/**
	 * Pools logs by severitys so that a list of every severity of these logs is returned
	 * @param array() $logs The Logs to pool
	 * @return string[] an Array of severitys
	 */
	function PoolSeveritys($logs) {
		
		$severitys = array();
		foreach($logs as $log) {
			foreach($severitys as $severity) {
				if($severity == $log['severity']) {
					continue 2;
				}
			}
			$severitys[] = $log['severity'];
		}
		return $severitys;
	}
	
	/**
	 * Pools logs by Categories so that a list of every Category of these logs is returned
	 * @param array() $logs The Logs to pool
	 * @return string[] an Array of categories
	 * @Todo templating with PoolSeveritys
	 */
	function PoolCategories($logs) {
		
		$categories = array();
		foreach($logs as $log) {
			foreach($categories as $category) {
				if($category == $log['category'])
					continue 2;
			}
			$categories[] = $log['category'];
		}
		return $categories;
	}
	
	/**
	 * Returns all logs found in the MySQL-table
	 * @return array() all logs
	 */
	function GetLogs() {
		
		try {
			$logs = $this->logger->getLogData();
		} catch (MySQLVoidDataException $e) {
			$this->logInterface->ShowError($this->msg['err_no_logs']);
		} catch (Exception $e) {
			$this->logInterface->ShowError($this->msg['err_logs']);
		}
		return $logs;
	}
	
	/**
	 * Returns Logs which cetegory is $category
	 * @param string $category the Category of the logs
	 * @return array() An array of logs
	 */
	function GetLogDataByCategory($category) {
		try {
			$logs = $this->logger->getLogDataByCategory($category);
		} catch (MySQLVoidDataException $e) {
			$this->logInterface->ShowError($this->msg['err_no_logs']);
		}
		catch (Exception $e) {
			$this->logInterface->ShowError($this->msg['err_logs'] . $e->getMessage());
		}
		return $logs;
	}
}

?>