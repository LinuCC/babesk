<?php

namespace Babesk\Schbas;

require_once PATH_INCLUDE . '/Schbas/SchbasPdf.php';
require_once PATH_INCLUDE . '/Schbas/Loan.php';

class LoanInfoPdf {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($dataContainer) {

		$this->_em = $dataContainer->getEntityManager();
		$this->_interface = $dataContainer->getInterface();
		$this->_smarty = $dataContainer->getSmarty();
		$this->_dataContainer = $dataContainer;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function setDataByUser($user) {

		$this->_user = $user;
	}

	public function setDataByGradelevel($gradelevel) {

		$this->_gradelevel = $gradelevel;
	}

	public function showPdf() {

		if(!$this->_gradelevel && !$this->_user) {
			die('Either gradelevel or user has to be set');
		}

		$this->_loanHelper = new \Babesk\Schbas\Loan($this->_dataContainer);
		$settingsRepo = $this->_em->getRepository('DM:SystemGlobalSettings');
		$infoRepo = $this->_em->getRepository('DM:SchbasText');
		$userId = ($this->_user) ? $this->_user->getId() : 0;

		if(!$this->_gradelevel) {
			$this->_gradelevel = $this->getGradelevelForUser($this->_user);
		}
		$bankAccount = $settingsRepo->findOneByName('bank_details')
			->getValue();
		$bankData = explode('|', $bankAccount);
		// $letterDateIso = $settingsRepo
		// 	->findOneByName('schbasDateCoverLetter')
		// 	->getValue();
		// $letterDate = date('d.m.Y', strtotime($letterDateIso));
		$letterDate = date('d.m.Y');

		list($feeNormal, $feeReduced) = $this->getFees();
		$books = $this->getBooks();


		$textId = $this->_gradelevel;
		$coverLetter = $infoRepo->findOneByDescription('coverLetter');
		$textOne = $infoRepo->findOneByDescription('textOne' . $textId);
		$textTwo = $infoRepo->findOneByDescription('textTwo' . $textId);
		$textThree = $infoRepo->findOneByDescription('textThree' . $textId);

		$this->_smarty->assign('books', $books);
		$this->_smarty->assign('gradelevel', $this->_gradelevel);
		$this->_smarty->assign('letterDate', $letterDate);
		$this->_smarty->assign('coverLetter', $coverLetter);
		$this->_smarty->assign('textOne', $textOne);
		$this->_smarty->assign('textTwo', $textTwo);
		$this->_smarty->assign('textThree', $textThree);
		$this->_smarty->assign('bankData', $bankData);
		$this->_smarty->assign('feeNormal', $feeNormal);
		$this->_smarty->assign('feeReduced', $feeReduced);
		$html = $this->_smarty->fetch(
			PATH_SMARTY_TPL . '/pdf/schbas-loan-info.pdf.tpl'
		);
		$schbasPdf = new \Babesk\Schbas\SchbasPdf(
			$userId, $this->_gradelevel
		);
		$schbasPdf->create($html);
		$schbasPdf->output();
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function getGradelevelForUser($user) {

		$prepSchoolyear = $this->_loanHelper->schbasPreparationSchoolyearGet();
		$gradeQuery = $this->_em->createQuery(
			'SELECT g FROM DM:SystemGrades g
			INNER JOIN g.attendances a
				WITH a.schoolyear = :schoolyear AND a.user = :user
		');
		$gradeQuery->setParameter('schoolyear', $prepSchoolyear);
		$gradeQuery->setParameter('user', $this->_user);
		$grade = $gradeQuery->getOneOrNullResult();
		if(!$grade) {
			$this->_interface->dieError(
				'Der Schüler ist nicht im nächsten Schuljahr eingetragen. ' .
				'Bitte informieren sie die Schule.'
			);
		}
		return $grade->getGradelevel();
	}

	protected function getFees() {

		if($this->_user) {
			return $this->_loanHelper
				->loanPriceOfAllBookAssignmentsForUserCalculate(
					$this->_user
				);
		}
		else {
			return [0.00, 0.00];
		}
	}

	protected function getBooks() {

		if($this->_user) {
			return $this->_loanHelper->loanBooksOfUserGet(
				$this->_user, ['includeAlreadyLend' => true]
			);
		}
		else {
			return $this->_loanHelper->booksInGradelevelToLoanGet(
				$this->_gradelevel
			);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_em;
	protected $_interface;
	protected $_dataContainer;

	protected $_loanHelper;

	protected $_user;
	protected $_gradelevel;
}


?>