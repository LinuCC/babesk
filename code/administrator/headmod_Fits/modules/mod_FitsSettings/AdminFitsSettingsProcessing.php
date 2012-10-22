<?php

class AdminFitsSettingsProcessing {
	public function __construct ($FitsSettingsInterface) {
		require_once PATH_ACCESS . '/GlobalSettingsManager.php';

		
		
		require_once 'AdminFitsSettingsInterface.php';
		$this->fitsInterface = $FitsSettingsInterface;
	


		$this->msg = array(
			//create meal
			'err_key'			 => 'Fehler mit Freischaltpasswort',
			'err_schoolyear'	 => 'Fehler mit Schuljahr',
			'err_class'			 => 'Fehler mit Klassenzuordnung',
			'err_all_classes'	 => 'Fehler mit Suchbedingung',
		);
	}

	/**
	 * show settings form
	 */
	function ShowForm() {
		$gsManager = new GlobalSettingsManager();
		$key = $gsManager->getFitsKey();
		$year = $gsManager->getFitsYear();
		$class = $gsManager->getFitsClass();
		$allClasses = $gsManager->getFitsAllClasses();

		
		$this->fitsInterface->showEditForm($key,$year,$class,$allClasses);
		
	}
	
	/**
	 * Save the Fits settings
	 */
	function SaveSettings ($password,$schoolyear,$class,$allClasses) {
		$gsManager = new GlobalSettingsManager();
			try {
				$gsManager->setFitsKey($password);
			} catch (Exception $e) {
				$this->groupInterface->dieError($this->msg['err_key'] . $e->getMessage());
			}
			try {
				$gsManager->setFitsYear($schoolyear);
			} catch (Exception $e) {
				$this->groupInterface->dieError($this->msg['err_schoolyear'] . $e->getMessage());
			}
			try {
				$gsManager->setFitsClass($class);
			} catch (Exception $e) {
				$this->groupInterface->dieError($this->msg['err_class'] . $e->getMessage());
			}
			try {
				$gsManager->setFitsAllClasses($allClasses);
			} catch (Exception $e) {
				$this->groupInterface->dieError($this->msg['err_all_classes'] . $e->getMessage());
			}
			$this->fitsInterface->FinEditSettings();	
	}

	

	/**
	 * Handles the MySQL-table meals
	 * @var GlobalSettingsManager
	 */
	protected $gsManager;

	/**
	 * Messages shown to the user
	 * @var string[]
	 */
	protected $msg;

	/**
	 * Handles the Output shown to the User
	 * @var AdminMealInterface
	 */
	protected $fitsInterface;

}
?>