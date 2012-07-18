<?php
class AdminLoanProcessing {
	

	var $messages = array();
	private $LoanInterface;

	protected $logs;	
	
	function __construct($LoanInterface) {

		
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
		$this->LoanInterface = $LoanInterface;
		global $logger;
		$this->logs = $logger;
		$this->msg = array('err_card_id' => 'Dies ist keine gültige Karten-ID ("%s")',
				'err_get_user_by_card' => 'Anhand der Kartennummer konnte kein Benutzer gefunden werden.');
	}
	
	/**
	 * Ausleihtabelle anzeigen
	 */
	function Loan($card_id) {
		
		if (!$this->cardManager->valid_card_ID($card_id))
			$this->LoanInterface->dieError(sprintf($this->msg['err_card_id'], $card_id));
		
		$uid = $this->GetUser($card_id);
		$loanbooks = $this->loanManager->getLoanByID($uid);
		
		foreach ($loanbooks as $loanbook){
			$invData = $this->inventoryManager->getInvDataByID($loanbook['inventory_id']);
			$bookdata = $this->bookManager->getBookDataByID($invData['book_id']);
			$datatmp = array_merge($loanbook, $invData, $bookdata);
			$data[] = $datatmp;
			$datatmp = null;
		}
		$this->LoanInterface->ShowLoanBooks($data);
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
				$this->LoanInterface->CardLocked();
			}
		} catch (Exception $e) {
			$this->LoanInterface->dieError($this->msg['err_get_user_by_card'] . ' Error:' . $e->getMessage());
		}
		return $uid;
	}
}

?>