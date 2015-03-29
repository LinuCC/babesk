<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ADMIN . '/Fits/Fits.php';

class FitsSettings extends Fits {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute($dataContainer) {
		//no direct access
		defined('_AEXEC') or die("Access denied");


		require_once 'AdminFitsSettingsProcessing.php';
		require_once 'AdminFitsSettingsInterface.php';

		$fitsSettingsInterface = new AdminfitsSettingsInterface($this->relPath);
		$fitsSettingsProcessing = new AdminFitsSettingsProcessing($fitsSettingsInterface);
		$allClasses = 0;
		if (isset($_POST['allClasses'])) $allClasses = 1;
		if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['password']) && isset($_POST['schoolyear'])  && isset($_POST['class'])) {
			$fitsSettingsProcessing->SaveSettings($_POST['password'],$_POST['schoolyear'],$_POST['class'],$allClasses);
		}
		else{
			$fitsSettingsProcessing->ShowForm();
		}
	}
}

?>
