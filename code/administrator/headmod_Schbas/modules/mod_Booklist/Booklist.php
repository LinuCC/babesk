<?php

require_once PATH_INCLUDE . '/Module.php';

class Booklist extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute($dataContainer) {

		defined('_AEXEC') or die('Access denied');

		require_once 'AdminBooklistInterface.php';
		require_once 'AdminBooklistProcessing.php';

		$BookInterface = new AdminBooklistInterface($this->relPath);
		$BookProcessing = new AdminBooklistProcessing($BookInterface);

		$action_arr = array('show_booklist' => 1,
							'add_book' => 4);

		if (isset($_GET['action'])) {
			$action = $_GET['action'];
			switch ($action) {
				case 1: //show booklist
					if (isset($_POST['filter'])){
						$BookProcessing->ShowBooklist($_POST['filter']);
					}else{
						$BookProcessing->ShowBooklist("name");
					}
					break;
				case 2: //edit a book
					if (isset ($_POST['isbn_search'])) {
						$bookID = $BookProcessing->getBookIdByISBN($_POST['isbn_search']);
						$BookProcessing->editBook($bookID);
					}
					if (!isset ($_POST['subject'], $_POST['class'],$_POST['title'],$_POST['author'],$_POST['publisher'],$_POST['isbn'],$_POST['price'],$_POST['bundle'])){
						$BookProcessing->editBook($_GET['ID']);
					}else{
						$BookProcessing->changeBook($_GET['ID'],$_POST['subject'], $_POST['class'],$_POST['title'],$_POST['author'],$_POST['publisher'],$_POST['isbn'],$_POST['price'],$_POST['bundle']);
					}
					break;
				case 3: //delete an entry
					if (isset($_POST['delete'])) {
						$BookProcessing->DeleteEntry($_GET['ID']);
					} else if (isset($_POST['not_delete'])) {
						$BookInterface->ShowSelectionFunctionality($action_arr);
					} else {
						$BookProcessing->DeleteConfirmation($_GET['ID']);
					}
					break;
				case 4: //add an entry
					if (!isset($_POST['title'])) {
						$BookProcessing->AddEntry();
					} else {
						$BookProcessing->AddEntryFin($_POST['subject'], $_POST['class'],$_POST['title'],$_POST['author'],$_POST['publisher'],$_POST['isbn'],$_POST['price'],$_POST['bundle']);
					}
					break;
				break;


			}
		} else {
			$BookInterface->ShowSelectionFunctionality($action_arr);
		}
	}
}

?>