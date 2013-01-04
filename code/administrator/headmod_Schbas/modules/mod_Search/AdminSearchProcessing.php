<?php
class AdminSearchProcessing {
	

	var $messages = array();
	private $SearchInterface;

	protected $logs;	
	
	function __construct($SearchInterface) {

		
		require_once PATH_ACCESS . '/CardManager.php';
		require_once PATH_ACCESS . '/UserManager.php';
		require_once PATH_ACCESS . '/LoanManager.php';
		require_once PATH_ACCESS . '/InventoryManager.php';
		require_once PATH_ACCESS . '/BookManager.php';
		
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
	 * Ausleihtabelle anzeigen
	 */
	function SearchTableData($card_id) {
		
		if (!$this->cardManager->valid_card_ID($card_id))
			$this->SearchInterface->dieError(sprintf($this->msg['err_card_id']));
		$uid = $this->GetUser($card_id);
		$loanbooks = $this->loanManager->getLoanlistByUID($uid);
		$data = array();
		foreach ($loanbooks as $loanbook){
			$invData = $this->inventoryManager->getInvDataByID($loanbook['inventory_id']);
			$bookdata = $this->bookManager->getBookDataByID($invData['book_id']);
			$datatmp = array_merge($loanbook, $invData, $bookdata);
			$data[] = $datatmp;
			//$datatmp = null;
			
		}
		if (empty($data)) {
			$this->SearchInterface->dieError(sprintf($this->msg['err_empty_books']));
		} else {
			$this->SearchInterface->ShowSearchBooks($data,$card_id,$uid);
		}	
	}
	
	/**
	 * Ausleihtabelle per Ajax anzeigen
	 */
	function SearchTableDataAjax($card_id) {
		$uid = $this->GetUser($card_id);
		$loanbooks = $this->loanManager->getLoanlistByUID($uid);
	
		foreach ($loanbooks as $loanbook){
			$invData = $this->inventoryManager->getInvDataByID($loanbook['inventory_id']);
			$bookdata = $this->bookManager->getBookDataByID($invData['book_id']);
			$datatmp = array_merge($loanbook, $invData, $bookdata);
			$data[] = $datatmp;
			//$datatmp = null;
				
		}
		//var_dump($data);
		if (!isset($data)) {
			$this->SearchInterface->showMsg("Keine B&uuml;cher ausgeliehen!");
		} else {
			$this->SearchInterface->ShowSearchBooksAjax($data,$card_id,$uid);
		}
	}
	
	
	/**
	 * Ein Buch zurückgeben
	 */
	function SearchBook($inventarnr,$uid) {

		$inv_id = $this->inventoryManager->getInvIDByBarcode($inventarnr);
	    if($this->loanManager->isUserEntry($uid)) {
	    try {
			$this->loanManager->RemoveLoanByIDs($inv_id, $uid);
			return $this->loanManager->isUserEntry($uid);
		} catch (Exception $e) {
			
		}
	    } else {
	    	return false;
	    }
		

	}
	
	/**
	 * Looks the user for the given CardID up, checks if the Card is locked and returns the UserID
	 * @param string $card_id The ID of the Card
	 * @return string UserID
	 */
	public function GetUser ($card_id) {
	
		try {
			$uid = $this->cardManager->getUserID($card_id);
			if ($this->userManager->checkAccount($uid)) {
				$this->SearchInterface->CardLocked();
			}
		} catch (Exception $e) {
			$this->SearchInterface->dieError($this->msg['err_get_user_by_card'] . ' Error:' . $e->getMessage());
		}
		return $uid;
	}
}

?>