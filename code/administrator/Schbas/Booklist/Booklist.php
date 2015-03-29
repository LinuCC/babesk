<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ADMIN . '/Schbas/Schbas.php';

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
					$this->editBook();
					die();
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
					$this->deleteBook();
					break;
				case 4: //add an entry
					$this->addBook();
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

	private function editBook() {

		if(isset($_POST['title'])) {
			$this->editBookUpload();
		}
		else if(isset($_GET['ID'])) {
			$book = $this->_em->getReference('DM:SchbasBook', $_GET['ID']);
			$query = $this->_em->createQuery(
				'SELECT b, s FROM DM:SchbasBook b
				LEFT JOIN b.subject s
				WHERE b = :book
			');
			$query->setParameter('book', $book);
			$book = $query->getOneOrNullResult();
			$this->_smarty->assign('book', $book);
			$this->displayTpl('change_book.tpl');
		}
		else if(isset($_POST['isbn_search'])) {
			$book = $this->_em->getRepository('DM:SchbasBook')
				->findOneByIsbn($_POST['isbn_search']);
			$this->_smarty->assign('book', $book);
			$this->displayTpl('change_book.tpl');
		}
	}

	private function editBookUpload() {

		$_POST['price'] = str_replace(',', '.', $_POST['price']);
		$book = $this->_em->find('DM:SchbasBook', $_GET['ID']);
		if(!$book) {
			$this->_interface->dieError('Buch nicht gefunden.');
		}
		$book->setTitle($_POST['title'])
			->setClass($_POST['class'])
			->setAuthor($_POST['author'])
			->setPublisher($_POST['publisher'])
			->setIsbn($_POST['isbn'])
			->setPrice($_POST['price'])
			->setBundle($_POST['bundle']);
		if(!empty($_POST['subject'])) {
			$subject = $this->_em->getRepository('DM:SystemSchoolSubject')
				->findOneByName($_POST['subject']);
			if($subject) {
				$book->setSubject($subject);
			}
			else {
				$this->_interface->dieError(
					"Konnte das Fach $_POST[subject] nicht finden."
				);
			}
		}
		else {
			$book->setSubject();
		}
		$this->_em->persist($book);
		$this->_em->flush();
		$this->_interface->dieSuccess(
			"Das Buch {$book->getTitle()} wurde erfolgreich verändert."
		);
	}

	private function addBook() {

		if(!isset($_POST['title'])) {
			$this->displayTpl('add_entry.tpl');
		}
		else {
			$this->addBookUpload();
		}
	}

	private function addBookUpload() {

		$_POST['price'] = str_replace(',', '.', $_POST['price']);
		$subject = $this->_em->getRepository('DM:SystemSchoolSubject')
			->findOneByName($_POST['subject']);
		if(!$subject) {
			$this->_interface->dieError(
				"Konnte das Fach $_POST[subject] nicht finden."
			);
		}
		$this->addBookCheckDuplicate(
			$subject, $_POST['class'], $_POST['bundle']
		);
		$book = new \Babesk\ORM\SchbasBook();
		$book->setTitle($_POST['title'])
			->setClass($_POST['class'])
			->setAuthor($_POST['author'])
			->setPublisher($_POST['publisher'])
			->setIsbn($_POST['isbn'])
			->setPrice($_POST['price'])
			->setBundle($_POST['bundle'])
			->setSubject($subject);
		$this->_em->persist($book);
		$this->_em->flush();
		$this->_interface->backlink('administrator|Schbas|Booklist');
		$this->_interface->dieSuccess(
			"Das Buch $_POST[title] wurde erfolgreich hinzugefügt."
		);
	}

	private function addBookCheckDuplicate($subject, $class, $bundle) {

		$duplicateBook = $this->_em->getRepository('DM:SchbasBook')
			->findOneBy(
				array(
					'subject' => $subject,
					'class' => $class,
					'bundle' => $bundle
			)
		);
		if($duplicateBook) {
			$this->_interface->dieError(
				"Es gibt bereits ein Buch mit dem Fach {$subject->getName()}" .
				", der Klasse $_POST[class] und dem Bundle $_POST[bundle] " .
				"namens {$duplicateBook->getTitle()} und der ISBN " .
				"{$duplicateBook->getIsbn()}"
			);
		}
	}

	private function deleteBook() {

		if(isset($_POST['barcode'])) {
			//Delete the book by barcode
			$book = $this->_em->getRepository('DM:SchbasBook')
				->findOneByIsbn($_POST['barcode']);
			$this->deleteBookFromDatabase($book);
		}
		else if(isset($_POST['delete'])) {
			//Delete the book
			$book = $this->_em->getReference('DM:SchbasBook', $_GET['ID']);
			$this->deleteBookFromDatabase($book);
		}
		else {
			$this->deleteBookConfirmation();
		}
	}

	private function deleteBookConfirmation() {

		if(isset($_GET['ID'])) {
			$book = $this->_em->find('DM:SchbasBook', $_GET['ID']);
			if($book) {
				$hasInventory = count($book->getExemplars()) > 0;
				$this->_smarty->assign('hasInventory', $hasInventory);
				$this->_smarty->assign('book', $book);
				$this->displayTpl('deletion_confirm.tpl');
			}
			else {
				$this->_interface->dieError(
					'Das Buch konnte nicht gefunden werden.'
				);
			}
		}
		else {
			$this->_interface->dieError(
				'Buch-ID nicht angegeben?!'
			);
		}
	}

	private function deleteBookFromDatabase($book) {

		$query = $this->_em->createQuery(
			'SELECT b, e, l FROM DM:SchbasBook b
			LEFT JOIN b.exemplars e
			LEFT JOIN e.lending l
			WHERE b = :book
		');
		$query->setParameter('book', $book);
		$book = $query->getOneOrNullResult();
		if($book) {
			//delete exemplars and its lending-statuses connected to the books
			foreach($book->getExemplars() as $exemplar) {
				foreach($exemplar->getLending() as $lending) {
					$this->_em->remove($lending);
				}
				$this->_em->remove($exemplar);
			}
			$this->_em->remove($book);
			$this->_em->flush();
			$this->_interface->backlink('administrator|Schbas|Booklist');
			$this->_interface->dieSuccess(
				"Das Buch {$book->getTitle()} wurde erfolgreich gelöscht."
			);
		}
		else {
			$this->_interface->dieError(
				'Das zu löschende Buch konnte nicht gefunden werden.'
			);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

}

?>