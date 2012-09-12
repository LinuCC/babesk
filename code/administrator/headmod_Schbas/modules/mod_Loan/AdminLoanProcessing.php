<?php
class AdminLoanProcessing {
	

	var $messages = array(
			'error' => array('no_inv' => 'Es konnte kein Inventar mit diesem Barcode gefunden werden.',
							'duplicate' => 'Dieses Buch ist bereits vergeben'));
	private $loanInterface;

	protected $logs;	
	
	function __construct($loanInterface) {

		
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
		$this->loanInterface = $loanInterface;
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
		$loanbooks = $this->loanManager->getLoanByUID($uid, false);
		$class = $this->userManager->getUserDetails($uid);
		$class = $class['class'];
		$fullname = $this->userManager->getForename($uid)." ".$this->userManager->getName($uid)." (".$class.")";
		
		if (!isset($loanbooks)) {
			$this->loanInterface->dieMsg("Keine B&uuml;cher mehr auszuleihen!");
		}else{
			$this->loanInterface->ShowLoanBooks($loanbooks, $card_id, $uid,$fullname);
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
				$this->LoanInterface->CardLocked();
			}
		} catch (Exception $e) {
			$this->loanInterface->dieError($this->msg['err_get_user_by_card'] . ' Error:' . $e->getMessage());
		}
		return $uid;
	}

	/**
	 * Ausleihtabelle per Ajax anzeigen
	 */
	function LoanAjax($card_id) {
		$uid = $this->GetUser($card_id);
		$loanbooks = $this->loanManager->getLoanByUID($uid,true);
		$class = $this->userManager->getUserDetails($uid);
		$class = $class['class'];
	    $fullname = $this->userManager->getForename($uid)." ".$this->userManager->getName($uid)." (".$class.")";
		if (!isset($loanbooks)) {
			$this->loanInterface->showMsg("Keine B&uuml;cher mehr auszuleihen!");
		} else {
			$this->loanInterface->ShowLoanBooksAjax($loanbooks,$card_id,$uid,$fullname);
		}
	}
	
	
	/**
	 * Ein Buch ausleihen
	 */
	function LoanBook($inventarnr,$uid) {
	
		try {
			$inv_nr = $this->inventoryManager->getInvIDByBarcode($inventarnr);
		} catch (Exception $e) {
			$this->logs
					->log('ADMIN', 'MODERATE',
							sprintf('Error while getting Data from MySQL:%s in %s', $e->getMessage(), __METHOD__));
			$this->loanInterface->dieErrorAjax($this->messages['error']['no_inv']);
		}
			$duplicate = $this->loanManager->isEntry($inv_nr['id']);
			if(!$duplicate){
			$this->loanManager->AddLoanByIDs($inv_nr["id"], $uid);
			}else{
				$this->loanInterface->dieErrorAjax($this->messages['error']['duplicate']);
			}
		return true;
	}
}

?>