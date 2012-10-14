<?php

require_once PATH_INCLUDE . '/Module.php';

class FitsSettings extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	
	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
	}
	
	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute() {
		//no direct access
		defined('_AEXEC') or die("Access denied");
		
		require_once 'AdminFitsSettingsProcessing.php';
		require_once 'AdminFitsSettingsInterface.php';
		
		$fitsSettingsInterface = new AdminfitsSettingsInterface($this->relPath);
		$fitsSettingsProcessing = new AdminFitsSettingsProcessing($fitsSettingsInterface);
		
		if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['password']) && isset($_POST['schoolyear'])  && isset($_POST['class'])) {
			$fitsSettingsProcessing->SaveSetting($_POST['password'],$_POST['schoolyear'],$_POST['class']);
		}
		else{
			$fitsSettingsProcessing->ShowForm();
		}
	}
}
      
?>