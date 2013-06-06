<?php

require_once PATH_INCLUDE . '/Module.php';

class Payment extends Module {

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

		defined('_AEXEC') or die('Access denied');

		require_once 'AdminPaymentInterface.php';

		$PaymentInterface = new AdminPaymentInterface($this->relPath);
		
			$PaymentInterface->ShowSelectionFunctionality();
	}
}

?>