<?php
class AdminLoanProcessing {
	
	
	var $messages = array();
	private $LoanInterface;
	private $cardManager;
	private $userManager;
	protected $logs;	
	
	function __construct($LoanInterface) {

		$this->LoanInterface = $LoanInterface;
		global $logger;
		$this->logs = $logger;
		$this->messages = array(
				'error' => array('no_books' => 'Keine B&uuml;cher gefunden.',));
	}
	

	
	/**
	 * Shows booklist
	 * @param unknown_type $filter
	 */
	function ShowBooklist($filter) {
	
		require_once PATH_ACCESS . '/BookManager.php';
		require_once PATH_ACCESS . '/CardManager.php';
		require_once PATH_ACCESS . '/UserManager.php';
	
		$booklistManager = new BookManager();
		$this->cardManager = new CardManager();
		$this->userManager = new UserManager();
	
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
}

?>