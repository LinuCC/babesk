<?php

require_once PATH_INCLUDE . '/Module.php';

class FitsCheck extends Module {

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

		require_once 'AdminFitsCheckProcessing.php';
		require_once 'AdminFitsCheckInterface.php';

		$fitsCheckInterface = new AdminfitsCheckInterface($this->relPath);
		$fitsCheckProcessing = new AdminFitsCheckProcessing($fitsCheckInterface);

		if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['card_ID'])) {
			$fitsCheckProcessing->CheckCard($_POST['card_ID']);
		}
		else{
			$fitsCheckInterface->CardId();
		}
	}
}

?>