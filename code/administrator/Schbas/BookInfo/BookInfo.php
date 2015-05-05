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

		$invData = $this->invDataFetch($barcodeStr);
		if(!empty($invData)) {
			$this->bookinfoTemplateGenerate($invData);
		}
		else {
			$this->_interface->dieError('Dieses Buch ist nicht im System.');
		}
	}

	private function invDataFetch($barcodeStr) {

		try {
			$bookHelper = new \Babesk\Schbas\Book($this->_dataContainer);
			$barcode = $bookHelper->barcodeParseToArray($barcodeStr);
			unset($barcode['delimiter']); //Not used in query
			$query = $this->_em->createQuery(
				'SELECT i, b, s, u FROM DM:SchbasInventory i
					INNER JOIN i.book b
						WITH b.class = :class AND b.bundle = :bundle
					INNER JOIN b.subject s
						WITH s.abbreviation = :subject
					LEFT JOIN i.usersLent u
					WHERE i.yearOfPurchase = :purchaseYear
						AND i.exemplar = :exemplar
			')->setParameters($barcode);

			return $query->getOneOrNullResult();

		} catch (Exception $e) {
			$this->_logger->log('Error fetching the bookinfo', 'Notice',
				Null, json_encode(array('barcode' => $barcode,
					'msg' => $e->getMessage(), 'type' => get_class($e))));
			$this->_interface->dieError('Fehler beim Abrufen des Buches.');
		}
	}

	private function bookinfoTemplateGenerate($exemplar) {

		$book = $exemplar->getBook();
		if(!$book) {
			$this->_logger->logO('Book for exemplar not found', ['sev' => 'error', 'moreJson' => ['id' => $exemplar->getId()]]);
			$this->_interface->dieError('Buch zum Exemplar nicht gefunden!');
		}
		$user = false;
		if($exemplar->getUsersLent()) {
			$user = $exemplar->getUsersLent()->first();
			$activeGrade = $this->_em->getRepository('DM:SystemUsers')
				->getActiveGradeByUser($user);
		}
		$this->_smarty->assign('activeGrade', $activeGrade);
		$this->_smarty->assign('user', $user);
		$this->_smarty->assign('book', $book);
		$this->displayTpl('result.tpl');
	}
}

?>
