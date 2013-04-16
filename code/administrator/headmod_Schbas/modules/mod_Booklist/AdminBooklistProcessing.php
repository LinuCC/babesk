<?php
class AdminBooklistProcessing {
	function __construct($BookInterface) {

		$this->BookInterface = $BookInterface;
		global $logger;
		$this->logs = $logger;
		$this->messages = array(
				'error' => array('no_books' => 'Keine B&uuml;cher gefunden.','notFound' => 'Buch nicht gefunden!'));
	}
	
	var $messages = array();
	private $bookInterface;

	/**
	 *@var Logger
	 */
	protected $logs;
	
	/**
	 * Shows booklist
	 * @param $filter
	 */
	function ShowBooklist($option, $filter) {
	
		require_once PATH_ACCESS . '/BookManager.php';
		require_once PATH_ACCESS . '/UserManager.php';
	
		$booklistManager = new BookManager();
		$userManager = new UserManager();
	
		try {
			isset($_GET['sitePointer'])?$showPage = $_GET['sitePointer'] + 0:$showPage = 1;
			$nextPointer = $showPage*10-10;
			if ($option == "filter"){
				$booklist = $booklistManager->getBooklistSorted($nextPointer, $filter);
			}elseif ($option == "search"){
				try {
					$class = $userManager->getClassByUsername($filter);
					$booklist = $booklistManager->getBooksByClass($class);
				} catch (Exception $e) {
					$booklist = $e->getMessage();
				}
				if ($booklist == 'MySQL returned no data!'){
					try {
						$booklist = $booklistManager->getBooksByClass($filter);
					} catch (Exception $e) {
						$this->BookInterface->dieError("Keine Einträge gefunden!");
					}
				}
			}
		} catch (Exception $e) {
			$this->logs
			->log('ADMIN', 'MODERATE',
					sprintf('Error while getting Data from MySQL:%s in %s', $e->getMessage(), __METHOD__));
			$this->booklistInterface->dieError($this->messages['error']['get_data_failed']);
		}
		$navbar = navBar($showPage, 'schbas_books', 'Schbas', 'Booklist', '1',$filter);
		$this->BookInterface->ShowBooklist($booklist,$navbar);
	}
	
	/**
	 * Edits an entry in book list.
	 * Function to show the template.
	 */
	
	function editBook($id) {
	
		require_once PATH_ACCESS . '/BookManager.php';
	
		$bookManager = new BookManager();
	
		try {
			$bookData = $bookManager->getBookDataByID($id);
		} catch (Exception $e) {
			$this->BookInterface->dieError($this->messages['error']['uid_get_param'] . $e->getMessage());
		}
	
		$this->BookInterface->ShowChangeBook($bookData);
	}
	
	/**
	 * Edits an entry in book list.
	 * Changes the MySQL entry
	 */
	
	function changeBook($id, $subject, $class, $title, $author, $publisher, $isbn, $price, $bundle) {
		require_once PATH_ACCESS . '/BookManager.php';
		$bookManager = new BookManager();
		try {
			$bookManager->editBook($id, $subject, $class, $title, $author, $publisher, $isbn, $price, $bundle);
		} catch (Exception $e) {
			$this->BookInterface->dieError($this->messages['error']['change'] . $e->getMessage());
		}
		$this->BookInterface->ShowChangeBookFin($id, $subject, $class, $title, $author, $publisher, $isbn, $price, $bundle);
	}
	
	/**
	 * Returns the book ID by a given ISBN
	 */
	function getBookIdByISBN($isbn_search) {
		require_once PATH_ACCESS . '/BookManager.php';
		$bookManager = new BookManager();
		try {
			$book_id = $bookManager->getBookIDByISBN($isbn_search);
		} catch (Exception $e) {
			$this->BookInterface->dieError($this->messages['error']['notFound'] . $e->getMessage());
		}
		return $book_id['id'];
	}

	/**
	 * Show template for adding an entry in Book list.
	 */
	function AddEntry() {
		$this->BookInterface->ShowAddEntry();
	}
	
	/**
	 * Adds an entry into Book list.
	 * @param $barcode
	 */
	function AddEntryFin($subject, $class, $title, $author, $publisher, $isbn, $price, $bundle) {
		require_once PATH_ACCESS . '/BookManager.php';
		$bookManager = new BookManager();
		try {
			$search = $bookManager->searchEntry('subject='.$subject.' AND class='.$class.' AND bundle='.$bundle);
		}catch (Exception $e){
			$search = 0;
		}
		if($search) {
			$this->BookInterface->dieError($this->messages['error']['duplicate']);
		} else {
			try {
				$bookManager->addEntry('subject',$subject,'class', $class,'title', $title,'author', $author,'publisher', $publisher,'isbn', $isbn, 'price', $price,'bundle', $bundle);
			}catch (Exception $e) {
				$this->logs
				->log('ADMIN', 'MODERATE',
						sprintf('Error while getting Data from MySQL:%s in %s', $e->getMessage(), __METHOD__));
				$this->BookInterface->dieError($this->messages['error']['get_data_failed']);
			}
		}
	
		$this->BookInterface->showAddEntryFin($subject, $class, $title, $author, $publisher, $isbn, $price, $bundle);
	}
	
	/**
	 * Shows the template for confirmation of an delete request.
	 * @param $id
	 */
	function DeleteConfirmation($id) {
		$this->BookInterface->ShowDeleteConfirmation($id);
	}
	
	
	/**
	 * Deletes an entry from MySQL.
	 * @param $id
	 */
	function DeleteEntry($id) {
		require_once PATH_ACCESS . '/BookManager.php';
		$BookManager = new BookManager();
	
	
		try {
			$bookManager->delEntry($id);
		} catch (Exception $e) {
			$this->logs
			->log('ADMIN', 'MODERATE',
					sprintf('Error while deleting Data from MySQL:%s in %s', $e->getMessage(), __METHOD__));
			$this->BookInterface->dieError($this->messages['error']['delete'] . $e->getMessage());
		}
		$this->BookInterface->ShowDeleteFin();
	}
	
	function isInvForBook($book_id) {
		require_once PATH_ACCESS . '/BookManager.php';
		require_once PATH_ACCESS . '/InventoryManager.php';
		$bookManager = new BookManager();
		$inventoryManager = new InventoryManager();
		
		$existEntry = $inventoryManager->existsEntry('book_id', $book_id);
		return $existEntry;
		
	}
}

?>