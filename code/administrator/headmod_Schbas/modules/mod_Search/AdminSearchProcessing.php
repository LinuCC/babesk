<?php
class AdminSearchProcessing {
	

	var $messages = array();
	private $SearchInterface;

	protected $logs;	
	
	function __construct($SearchInterface) {

		
		//require_once PATH_ACCESS . '/CardManager.php';
		require_once PATH_ACCESS . '/UserManager.php';
		//require_once PATH_ACCESS . '/LoanManager.php';
		//require_once PATH_ACCESS . '/InventoryManager.php';
		//require_once PATH_ACCESS . '/BookManager.php';
		
		$this->cardManager = new CardManager();
		$this->userManager = new UserManager();
		$this->loanManager = new LoanManager();
		$this->inventoryManager = new InventoryManager();
		$this->bookManager = new BookManager();
		$this->SearchInterface = $SearchInterface;
		global $logger;
		$this->logs = $logger;
		$this->msg = array('err_empty_books' => 'Keine ausgeliehenen B&uuml;cher vorhanden!',
							'err_get_user_by_card' => 'Kein Benutzer gefunden!',
							'err_card_id' => 'Die Karten-ID ist fehlerhaft!');
	}
	
	/**
	 * Shows the books for the given search entry
	 */
	public function ShowBooks ($search) {
		try {
			$userID = $this->userManager->getUserID($_POST['search']);
		} catch (Exception $e) {
			$userID =  $e->getMessage();
		}
		if ($userID == 'MySQL returned no data!') {
			try {
				$userID = $um->getUserID($_POST['user_search']);
			} catch (Exception $e) {
				$userInterface->dieError("Benutzer nicht gefunden!");
			}
		}
	}
}

?>