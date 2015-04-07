<?php

namespace administrator\Schbas\BookAssignments\Generate;

require_once PATH_ADMIN .  '/Schbas/BookAssignments/BookAssignments.php';
require_once PATH_INCLUDE . '/Schbas/Loan.php';

class Generate extends \administrator\Schbas\BookAssignments\BookAssignments {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		if(isset($_GET['overview-infos'])) {
			$this->overviewInfosSend();
		}
		else if(isset($_POST['data'])) {

		}
		else {
			$this->displayTpl('main.tpl');
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
		parent::moduleTemplatePathSet();
	}

	protected function overviewInfosSend() {

		$loanBookMan = new \Babesk\Schbas\Loan($this->_dataContainer);
		$schoolyear = $loanBookMan->schbasPreparationSchoolyearGet();
		$assignmentsExist = $this->assignmentsForSchoolyearExistCheck(
			$schoolyear
		);
		$booksInGradelevels = $this->booksAssignedToGradelevelsGet();
		die(json_encode(array(
			'schoolyear' => $schoolyear->getLabel(),
			'assignmentsForSchoolyearExist' => $assignmentsExist,
			'bookAssignmentsForGrades' => $booksInGradelevels
		)));
	}

	protected function assignmentsForSchoolyearExistCheck($schoolyear) {

		$query = $this->_em->createQuery(
			'SELECT COUNT(usb) FROM DM:SchbasUserShouldLendBook usb
				WHERE usb.schoolyear = :schoolyear
		');
		$query->setParameter('schoolyear', $schoolyear);
		$res = $query->getOneOrNullResult();
		return !empty($res);
	}

	protected function booksAssignedToGradelevelsGet() {

		$loanBookMan = new \Babesk\Schbas\Loan($this->_dataContainer);
		$books = $loanBookMan->booksAssignedToGradelevelsGet();
		// Sort the array by Gradelevel
		// { '<gradelevel>': [{'id': '<bookId>', 'name': '<bookName>'}]}
		$booksInGradelevels = array();
		foreach($books as $data) {
			$book = $data['book'];
			$gradelevels = $data['gradelevels'];
			foreach($gradelevels as $gradelevel) {
				$booksInGradelevels[$gradelevel][] = array(
					'id' => $book->getId(),
					'name' => $book->getTitle()
				);
			}
		}
		// [ { 'gradelevel': '<gradelevel>',
		//     'books':      [{'id': '<bookId>', 'name': '<bookName>'}]
		// } ]
		$booksInGradelevelsWithoutKeys = array();
		foreach($booksInGradelevels as $gradelevel => $books) {
			$booksInGradelevelsWithoutKeys[] = array(
				'gradelevel' => $gradelevel,
				'books' => $books
			);
		}
		return $booksInGradelevelsWithoutKeys;
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

}

?>