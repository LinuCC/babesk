<?php

require_once PATH_INCLUDE . '/Module.php';

class Inventory extends Module {

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
		
		require_once 'AdminInventoryInterface.php';
		require_once 'AdminInventoryProcessing.php';
		
		$inventoryInterface = new AdminInventoryInterface($this->relPath);
		$inventoryProcessing = new AdminInventoryProcessing($inventoryInterface);
		
		$action = array('show_inventory' => 1,);
			
			if ('POST' == $_SERVER['REQUEST_METHOD']) {
			$action = $_GET['action'];
			switch ($action) {
				case 1: //show the inventory
					$inventoryProcessing->ShowInventory(false);
					break;
			}
		} else {
			$inventoryInterface->ShowSelectionFunctionality($action);
		}
	}
}

?>