<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ADMIN . '/Babesk/Babesk.php';

class Checkout extends Babesk {

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

		require_once 'AdminCheckoutProcessing.php';
		require_once 'AdminCheckoutInterface.php';

		$checkoutInterface = new AdminCheckoutInterface($this->relPath);
		$checkoutProcessing = new AdminCheckoutProcessing($checkoutInterface);

		if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['card_ID'])) {
			$checkoutProcessing->Checkout($_POST['card_ID']);
		}
		else{
			$checkoutInterface->CardId();
		}
	}
}

?>
