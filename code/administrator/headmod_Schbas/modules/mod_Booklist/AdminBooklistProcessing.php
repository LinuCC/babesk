<?php
class AdminBooklistProcessing {
	function __construct($BookInterface) {

		$this->BookInterface = $BookInterface;
		global $logger;
		$this->logs = $logger;
		$this->messages = array(
				'error' => array('no_books' => 'Keine B&uuml;cher gefunden.',));
	}
	
	var $messages = array();
	private $userInterface;

	/**
	 *@var Logger
	 */
	protected $logs;
	
	/**
	 * Shows booklist
	 * @param unknown_type $filter
	 */
	function ShowBooklist($filter) {
	
		require_once PATH_ACCESS . '/BookManager.php';
	
		$booklistManager = new BookManager();
	
		try {
			$booklist = $booklistManager->getBooklistSorted();
		} catch (Exception $e) {
			$this->logs
			->log('ADMIN', 'MODERATE',
					sprintf('Error while getting Data from MySQL:%s in %s', $e->getMessage(), __METHOD__));
			$this->booklistInterface->dieError($this->messages['error']['get_data_failed']);
		}
		$this->BookInterface->ShowBooklist($booklist);
	}
	
	/**
	 * Edits an entry in book list.
	 * Function to show the template.
	 */
	
	function editBook($id) {
	
		require_once PATH_ACCESS . '/BookManager.php';
	
		$bookManager = new BookManager();
	
		try {
			$bookData = $bookManager->getBookDataByID($_GET['ID']);
		} catch (Exception $e) {
			$this->userInterface->dieError($this->messages['error']['uid_get_param'] . $e->getMessage());
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
			$this->booklistInterface->dieError($this->messages['error']['change'] . $e->getMessage());
		}
		$this->BookInterface->ShowChangeBookFin($id, $subject, $class, $title, $author, $publisher, $isbn, $price, $bundle);
	
	}
	
	
}

?>