<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ADMIN . '/headmod_Schbas/Schbas.php';

class Loan extends Schbas {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);

		require_once 'AdminLoanInterface.php';
		require_once 'AdminLoanProcessing.php';

		$LoanInterface = new AdminLoanInterface($this->relPath);
		$LoanProcessing = new AdminLoanProcessing($LoanInterface);

		if ('GET' == $_SERVER['REQUEST_METHOD'] && isset($_GET['inventarnr'])) {
			if (!$LoanProcessing->LoanBook(urldecode($_GET['inventarnr']),$_GET['uid'])) {
				$LoanInterface->LoanEmpty();
			} else {
				$LoanProcessing->LoanAjax($_GET['card_ID']);
			}
		}
		else if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['card_ID'])) {
			$LoanProcessing->Loan($_POST['card_ID']);
		}
		else{
			// Scan the card-id
			$this->displayTpl('form.tpl');
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
		parent::moduleTemplatePathSet();
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

}

?>
