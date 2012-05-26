<?php

require_once PATH_INCLUDE . '/Module.php';

class Checkout extends Module {

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
		
		require_once 'AdminCheckoutProcessing.php';
		require_once 'AdminCheckoutInterface.php';
		
		$checkoutProcessing = new AdminCheckoutProcessing();
		$checkoutInterface = new AdminCheckoutInterface($this->path);
		
		if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['card_ID'])) {
			$checkoutProcessing->Checkout($_POST['card_ID']);
		}
		else{
			$checkoutInterface->CardId();
		}
	}
}
      
?>