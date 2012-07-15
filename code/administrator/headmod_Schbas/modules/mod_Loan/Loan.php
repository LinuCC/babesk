<?php

require_once PATH_INCLUDE . '/Module.php';

class Loan extends Module {

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
		
		require_once 'AdminLoanInterface.php';
		require_once 'AdminLoanProcessing.php';
		
		$LoanInterface = new AdminLoanInterface($this->relPath);
		$LoanProcessing = new AdminLoanProcessing($LoanInterface);
		if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['card_ID'])) {
			$LoanProcessing->Loan($_POST['card_ID']);
		}
		else{
			$LoanInterface->CardId();
		}
		
	}
}

?>