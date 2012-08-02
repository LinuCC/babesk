<?php

require_once PATH_INCLUDE . '/Module.php';

class Religion extends Module {

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
		
		defined('_AEXEC') or die('Access denied');
		
		require_once 'AdminReligionInterface.php';
		require_once 'AdminReligionProcessing.php';
		
		$ReligionInterface = new AdminReligionInterface($this->relPath);
		$ReligionProcessing = new AdminReligionProcessing($ReligionInterface);
		
		if ('POST' == $_SERVER['REQUEST_METHOD']) {
			$action = $_GET['action'];
			switch ($action) {
				case 1: //edit the confession list
					$ReligionProcessing->EditReligions(0);
				break;
				case 2: //save the confession list
					$ReligionProcessing->EditReligions($_POST);
				break;
				case 3: //edit the users
					$ReligionProcessing->ShowUsers(false);	
				break;
				case 4: //save the users
					$ReligionProcessing->SaveUsers($_POST);
				break;
			}
		} else {
			$ReligionInterface->ShowSelectionFunctionality();
		}
	}
}

?>