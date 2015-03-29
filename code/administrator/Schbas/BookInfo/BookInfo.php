<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ADMIN . '/Schbas/Schbas.php';
require_once PATH_INCLUDE . '/Schbas/Book.php';

class BookInfo extends Schbas {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);

		if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['barcode'])) {
			$this->bookinfoShow($_POST['barcode']);
		}
		else{
			$this->displayTpl('form.tpl');
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
		$this->initSmartyVariables();
	}

	private function bookinfoShow($barcodeStr) {

		$invData = $this->dataFetch($barcodeStr);
		if(!empty($invData)) {
			$potentialUsers = $invData->getUsersLent();
			if(count($potentialUsers)) {
				//Current db-Structure only allows book lent to only one user
				$user = $potentialUsers->first();
				//Query made sure to just load the active grade
				$grade = $user->getAttendances()
					->first()
					->getGrade();
				$this->bookinfoTemplateGenerate($invData, $user, $grade);
			}
			else {
				$this->_interface->dieError(
					'Dieses Buch ist keinem Benutzer ausgeliehen.'
				);
			}
		}
		else {
			$this->_interface->dieError('Dieses Buch ist nicht im System.');
		}
	}

	private function dataFetch($barcodeStr) {

		try {
			$bookHelper = new \Babesk\Schbas\Book($this->_dataContainer);
			$barcode = $bookHelper->barcodeParseToArray($barcodeStr);
			unset($barcode['delimiter']); //Not used in query
			$query = $this->_em->createQuery(
				'SELECT i, b, s, u, uigs FROM DM:SchbasInventory i
					INNER JOIN i.book b
						WITH b.class = :class AND b.bundle = :bundle
					INNER JOIN b.subject s
						WITH s.abbreviation = :subject
					LEFT JOIN i.usersLent u
					LEFT JOIN u.attendances uigs
					LEFT JOIN uigs.schoolyear sy WITH sy.active = 1
					LEFT JOIN uigs.grade g
					WHERE i.yearOfPurchase = :purchaseYear
						AND i.exemplar = :exemplar
						AND (sy.id != 0 OR sy.id IS NULL)
			')->setParameters($barcode);
			return $query->getOneOrNullResult();

		} catch (Exception $e) {
			$this->_logger->log('Error fetching the bookinfo', 'Notice',
				Null, json_encode(array('barcode' => $barcode,
					'msg' => $e->getMessage(), 'type' => get_class($e))));
			$this->_interface->dieError('Fehler beim Abrufen des Buches.');
		}
	}

	private function bookinfoTemplateGenerate($exemplar, $user, $grade) {

		$book = $exemplar->getBook();
		$gradeStr = $grade->getGradelevel() . $grade->getLabel();
		$this->_smarty->assign('userID', $user->getId());
		$this->_smarty->assign('name', $user->getName());
		$this->_smarty->assign('forename', $user->getForename());
		$this->_smarty->assign('class', $gradeStr);
		$this->_smarty->assign('locked', $user->getLocked());
		$this->_smarty->assign('bookID', $book->getId());
		$this->_smarty->assign('subject', $book->getSubject()->getName());
		$this->_smarty->assign('class', $book->getClass());
		$this->_smarty->assign('title', $book->getTitle());
		$this->_smarty->assign('author', $book->getAuthor());
		$this->_smarty->assign('publisher', $book->getPublisher());
		$this->displayTpl('result.tpl');
	}
}

?>
