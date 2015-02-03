<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ADMIN . '/headmod_Schbas/Schbas.php';

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
					$this->addBook();
					die();
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

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

}

?>