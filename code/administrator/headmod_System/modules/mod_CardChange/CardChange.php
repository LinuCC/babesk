<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ADMIN . '/headmod_System/System.php';

class CardChange extends System {

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

		require_once 'AdminCardChangeProcessing.php';
		require_once 'AdminCardChangeInterface.php';

		$cardChangeInterface = new AdminCardChangeInterface($this->relPath);
		$cardChangeProcessing = new AdminCardChangeProcessing($cardChangeInterface);


			$cardChangeInterface->ShowCardChangeStats($cardChangeProcessing->GetSumOfCardChanges());

	}
}

?>
