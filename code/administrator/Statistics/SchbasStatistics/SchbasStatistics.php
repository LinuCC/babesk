<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_INCLUDE . '/Schbas/Loan.php';
require_once PATH_ADMIN . '/Statistics/Statistics.php';
require_once PATH_INCLUDE . '/pdf/GeneralPdf.php';

class SchbasStatistics extends Statistics {

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		$this->initSmartyVariables();
		if(isset($_GET['action'])) {
			switch($_GET['action']) {
				case 'pdf':
					$this->outputPdf();
					break;
			}
		}
		else {
			$this->displayTpl('statistics.tpl');
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function calculateData() {

		$loanHelper = new \Babesk\Schbas\Loan($this->_dataContainer);
		$schoolyear = $loanHelper->schbasPreparationSchoolyearGet();
		$schoolyearId = $schoolyear->getId();
		$stmt = $this->_pdo->query(
			"SELECT lc.name AS loanChoiceName, COUNT(*) AS count
				FROM SchbasAccounting sa
				INNER JOIN SchbasLoanChoices lc ON lc.ID = sa.loanChoiceId
				WHERE sa.schoolyearId = $schoolyearId
				GROUP BY loanChoiceId
		");
		$usercount = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

		$stmt = $this->_pdo->query(
			"SELECT lc.name AS loanChoiceName,
					CONCAT(SUM(payedAmount), '€') AS payedAmount
				FROM SchbasAccounting sa
				INNER JOIN SchbasLoanChoices lc ON lc.ID = sa.loanChoiceId
				WHERE lc.abbreviation IN('ln', 'lr')
					AND sa.schoolyearId = $schoolyearId
				GROUP BY loanChoiceId
		");
		$payedAmount = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

		$data = array(
			'usercount' => $usercount,
			'payedAmount' => $payedAmount
		);
		return $data;
	}

	protected function outputPdf() {

		$data = $this->calculateData();
		$this->_smarty->assign('data', $data);
		$pdf = new GeneralPdf($this->_pdo);
		$today = date('d.m.Y H:i');
		$html = $this->_smarty->fetch(
			PATH_SMARTY_TPL . '/pdf/schbas-statistics.pdf.tpl'
		);
		$pdf->create("Schbas Statistik ($today)", $html);
		$pdf->output();
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////
}

?>