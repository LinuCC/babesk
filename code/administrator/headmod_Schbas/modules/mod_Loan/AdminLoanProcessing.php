<?php
class AdminLoanProcessing {


	var $messages = array(
			'error' => array('no_inv' => 'Es konnte kein Inventar mit diesem Barcode gefunden werden.',
							'duplicate' => 'Dieses Buch ist bereits vergeben'));
	private $loanInterface;

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
		$this->msg = array('err_card_id' => 'Dies ist keine gĂźltige Karten-ID ("%s")',
				'err_get_user_by_card' => 'Anhand der Kartennummer konnte kein Benutzer gefunden werden.');
	}

	/**
	 * Ausleihtabelle anzeigen
	 */
	function Loan($card_id) {

		$alert="<font color=#ff0000>";

		if (!$this->cardManager->valid_card_ID($card_id)) {
			$this->loanInterface->dieError(sprintf($this->msg['err_card_id'], $card_id));
		}

		$uid = $this->GetUser($card_id);
		// eingefügt
		$hasForm = TableMng::query(sprintf('SELECT COUNT(*) FROM SchbasAccounting WHERE UID = "%s"',$uid));
		if ($hasForm[0]['COUNT(*)']=="0") {
			$alert .= "<u>Buchhaltungshinweis:</u><br><li><h5>Formular zur Buchausleihe wurde nicht abgegeben!</h5></li><br>";
		}
		$schoolyearDesired = TableMng::query(
			'SELECT ID FROM SystemSchoolyears WHERE active = 1'
		);
		$schoolyearID = $schoolyearDesired[0]['ID'];
		$gradeID = TableMng::query(sprintf(
			'SELECT GradeID
				FROM SystemAttendants
				WHERE UserID = "%s" AND schoolyearID ="%s"',
				$uid,$schoolyearID)
		);
		$grade = TableMng::query(sprintf(
			'SELECT gradelevel FROM SystemGrades WHERE ID = %s',
			$gradeID[0]['GradeID'])
		);
		$payed = TableMng::query(sprintf(
			'SELECT slc.abbreviation AS loanchoice, payedAmount,amountToPay
				FROM SchbasAccounting sa
				LEFT JOIN SchbasLoanChoices slc ON slc.ID = sa.loanChoiceId
				WHERE UID="%s"',
			$uid)
		);
		if (($payed[0]['loanchoice']=="ln" ||
				$payed[0]['loanchoice'] == "lr" ) &&
			strcmp($payed[0]['payedAmount'],$payed[0]['amountToPay']) < 0) {
			$alert .="<u>Buchhaltungshinweis:</u><br><li><h5>Geld wurde noch nicht (ausreichend) gezahlt.</h5></li><li><h5>Es sind bisher ".$payed[0]['payedAmount']."&euro; von ".$payed[0]['amountToPay']."&euro; eingegangen!</h5></li><br>";
		}

		if($payed[0]['loanchoice']=="nl") {
			$alert .= "<u>Buchhaltungshinweis:</u><br><li><h5>Achtung: Selbstzahler! Keine B&uuml;cher ausleihen!</h5></li><br>";
		}
		$hasBooks = TableMng::query(
			sprintf('SELECT COUNT(*) FROM SchbasLending WHERE user_id = "%s"',
			$uid)
		);

		if ($hasBooks[0]['COUNT(*)']!="0"){
			$hasBooksID = TableMng::query(sprintf('SELECT inventory_id FROM SchbasLending WHERE user_id = "%s"',$uid));
			$alert .= "<u>Es sind noch B&uuml;cher ausgeliehen:</u><br>";
			foreach ($hasBooksID as $hasBook){
				$book_id = TableMng::query(sprintf("SELECT book_id FROM SchbasInventory WHERE id = %s",$hasBook['inventory_id']));
				$book_title = TableMng::query(sprintf("SELECT title FROM SchbasBooks WHERE id = %s",$book_id[0]['book_id']));
				$alert .= "<li><h5>".$book_title[0]['title']."</h5></li>";
			}
			$alert.="<br>";
		}

		$loanbooks = $this->loanManager->getLoanByUID($uid, false);

		$loanbooksSelfBuy = TableMng::query("SELECT BID FROM SchbasSelfpayer WHERE UID=".$uid);

		if (count($loanbooksSelfBuy)!=0){
			$alert .= "<u>Folgende B&uuml;cher werden selbst angeschafft:</u><br>";
			foreach ($loanbooksSelfBuy as $selfBook){

				$book_title = TableMng::query(sprintf("SELECT title FROM SchbasBooks WHERE id = %s",$selfBook['BID']));
				$alert .= "<li><h5>".$book_title[0]['title']."</h5></li>";
			}
			$alert.="<br>";

		}
		$alert .="</font>";

		$loanbooksSelfBuy = array_map('current',$loanbooksSelfBuy);


		$checkedBooks = array();
		foreach ($loanbooks as $book) {
			if (!in_array($book['id'],$loanbooksSelfBuy)) $checkedBooks[] = $book;

		}

		$class = $this->fetchUserDetails($uid);
		// $class = $this->userManager->getUserDetails($uid);
		$class = $class['class'];
		$fullname = $this->userManager->getForename($uid)." ".$this->userManager->getName($uid)." (".$class.")";

		if (!isset($checkedBooks)) {
			$this->loanInterface->dieMsg("Keine B&uuml;cher mehr auszuleihen!");
		}else{
			$this->loanInterface->ShowLoanBooks($checkedBooks, $card_id, $uid,$fullname,$alert);
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
		//
		$loanbooksSelfBuy = TableMng::query("SELECT BID FROM SchbasSelfpayer WHERE UID=".$_uid);
		$loanbooksSelfBuy = array_map('current',$loanbooksSelfBuy);

		$checkedBooks = array();
		foreach ($loanbooks as $book) {
			if (!in_array($book['id'],$loanbooksSelfBuy)) $checkedBooks[] = $book;

		}
		//

		$class = $this->fetchUserDetails($uid);
		// $class = $this->userManager->getUserDetails($uid);
		$class = $class['class'];
		$fullname = $this->userManager->getForename($uid)." ".$this->userManager->getName($uid)." (".$class.")";
		if (!isset($loanbooks)) {
			$this->loanInterface->showMsg("Keine B&uuml;cher mehr auszuleihen!");
		} else {
			$this->loanInterface->ShowLoanBooksAjax($checkedBooks,$card_id,$uid,$fullname);
		}
	}


	/**
	 * Ein Buch ausleihen
	 */
	function LoanBook($barcode,$uid) {

		try {
			$inv_id = $this->inventoryManager->getInvIDByBarcode($barcode);
		} catch (Exception $e) {
			$this->logs
					->log('ADMIN', 'MODERATE',
							sprintf('Error while getting Data from MySQL:%s in %s', $e->getMessage(), __METHOD__));
			$this->loanInterface->dieErrorAjax($this->messages['error']['no_inv']);
		}
		if (isset($inv_id)){
			$duplicate = $this->loanManager->isEntry($inv_id);
			if(!$duplicate){
			$this->loanManager->AddLoanByIDs($inv_id, $uid);
			}else{
				$this->loanInterface->dieErrorAjax($this->messages['error']['duplicate']);
			}
		return true;
		}else{
			$this->loanInterface->dieErrorAjax($this->messages['error']['no_inv']);
		}
	}

	/**
	 * Fetches all of the Userdata from the database and returns them
	 * @param  $userId The Id of the User whose data should be fetched
	 * @return array(...)
	 */
	public function fetchUserDetails($userId) {

		try {
			$userDetails = TableMng::query(sprintf(
				'SELECT u.*,
				(SELECT CONCAT(g.gradelevel, g.label) AS class
					FROM SystemAttendants uigs
					LEFT JOIN SystemGrades g ON uigs.gradeId = g.ID
					WHERE uigs.userId = u.ID AND
						uigs.schoolyearId = @activeSchoolyear) AS class
				FROM SystemUsers u WHERE `ID` = %s', $userId));

		} catch (Exception $e) {
			$this->loanInterface->dieError('Konnte die Benutzer-details nicht laden');
		}

		return $userDetails[0];
	}
}

?>
