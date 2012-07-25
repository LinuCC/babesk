<?php

require_once PATH_INCLUDE . '/Module.php';

class Retour extends Module {

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
		
		require_once 'AdminRetourInterface.php';
		require_once 'AdminRetourProcessing.php';
		
		$RetourInterface = new AdminRetourInterface($this->relPath);
		$RetourProcessing = new AdminRetourProcessing($RetourInterface);
		if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['card_ID'])) {
			$RetourProcessing->Retour($_POST['card_ID']);
		}
		else{
			$RetourInterface->CardId();
		}
		
	}
}

?>