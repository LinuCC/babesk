<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ADMIN . '/headmod_Schbas/Schbas.php';
require_once PATH_INCLUDE . '/orm-entities/SchbasBooks.php';
require_once PATH_INCLUDE . '/orm-entities/SchbasSubjects.php';

class Booklist extends Schbas {


	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		defined('_AEXEC') or die('Access denied');

		require_once 'AdminBooklistInterface.php';
		require_once 'AdminBooklistProcessing.php';

		$BookInterface = new AdminBooklistInterface($this->relPath);
		$BookProcessing = new AdminBooklistProcessing($BookInterface);

		parent::entryPoint($dataContainer);
		parent::moduleTemplatePathSet();

		$action_arr = array('show_booklist' => 1,
							'add_book' => 4,
							'del_book' => 6);

		if (isset($_GET['action'])) {
			$action = $_GET['action'];
			switch ($action) {
				case 2: //edit a book
					if (isset ($_POST['isbn_search'])) {
						$bookID = $BookProcessing->getBookIdByISBN($_POST['isbn_search']);
						$BookProcessing->editBook($bookID);
					}
					else if (!isset ($_POST['subject'], $_POST['class'],$_POST['title'],$_POST['author'],$_POST['publisher'],$_POST['isbn'],$_POST['price'],$_POST['bundle'])){
						$BookProcessing->editBook($_GET['ID']);
					}else{
						$BookProcessing->changeBook($_GET['ID'],$_POST['subject'], $_POST['class'],$_POST['title'],$_POST['author'],$_POST['publisher'],$_POST['isbn'],$_POST['price'],$_POST['bundle']);
					}
					break;
				case 3: //delete an entry

					if (isset($_POST['barcode'])) {
						try {

							$BookProcessing->DeleteEntry($BookProcessing->GetIDFromBarcode($_POST['barcode']));
						} catch (Exception $e) {
						}
					}
					else if (isset($_POST['delete'])) {
						$BookProcessing->DeleteEntry($_GET['ID']);
					} else if (isset($_POST['not_delete'])) {
						$BookInterface->ShowSelectionFunctionality($action_arr);
					} else {

						if (!$BookProcessing->isInvForBook($_GET['ID'])){
							$BookProcessing->DeleteConfirmation($_GET['ID']);
						}else{
							$BookInterface->dieError("Es ist noch Inventar zu diesem Buch vorhanden! Bitte l&ouml;schen Sie dies zuerst!");
						}
					}
					break;
				case 4: //add an entry
					if (!isset($_POST['title'])) {
						$BookProcessing->AddEntry();
					} else {
						$BookProcessing->AddEntryFin($_POST['subject'], $_POST['class'],$_POST['title'],$_POST['author'],$_POST['publisher'],$_POST['isbn'],$_POST['price'],$_POST['bundle']);
					}
					break;
				case 5: //filter
					$BookProcessing->ShowBooklist("search", $_POST['search']);
					break;

				case 6: //search an entry for deleting

					$BookProcessing->ScanForDeleteEntry();

					break;
				case 'showBooksFNY':
					$BookProcessing->showBooksForNextYear();
					break;
				case 'showBooksBT':
					$BookProcessing->showBooksByTopic();
					break;
				break;
			}
		}
		else {
			$BookInterface->ShowSelectionFunctionality($action_arr);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function showBooklist() {

		$this->displayTpl('show-booklist.tpl');
	}

	protected function getBooklist() {

		// $pagenum = $_POST['pagenumber'];
		// $usersPerPage = $_POST['usersPerPage'];
		// $sortFor = $_POST['sortFor'];
		// $filterFor = $_POST['filterFor'];

		$query = $this->_entityManager->createQueryBuilder()
			->select(array('b, s'))
			->from('Babesk\ORM\SchbasBooks', 'b')
			->leftJoin('b.subject', 's')
			->where('b.title = ""')
			->setFirstResult($_POST['pagenumber'] * $_POST['booksPerPage'])
			->setMaxResults($_POST['booksPerPage'])
			;

		// if($_POST['sortFor']) {
		// 	$query->orderBy($_POST['sortFor']);
		// }
		// if($_POST['filterFor']) {
		// 	$query->where()
		// }

		$paginator = new \Doctrine\ORM\Tools\Pagination\Paginator(
			$query, $fetchJoinCollection = true
		);

		$books = array();
		foreach($paginator as $book) {
			$bookAr = array(
				'id' => $book->getId(),
				'title' => $book->getTitle(),
				'author' => $book->getAuthor(),
				'gradelevel' => $book->getClass(),
				'bundle' => $book->getBundle(),
				'price' => $book->getPrice(),
				'isbn' => $book->getIsbn(),
				'publisher' => $book->getPublisher()
			);
			$bookAr['subject'] = ($book->getSubject()) ?
				$book->getSubject()->getName() : '';
			$books[] = $bookAr;
		}

		$bookcount = count($paginator);
		// No division by zero, never show zero sites
		if($_POST['booksPerPage'] != 0 && $bookcount > 0) {
			$pagecount = ceil($bookcount / (int)$_POST['booksPerPage']);
		}
		else {
			$pagecount = 1;
		}

		die(json_encode(array(
			'pagecount' => $pagecount, 'books' => $books
		)));
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

}

?>