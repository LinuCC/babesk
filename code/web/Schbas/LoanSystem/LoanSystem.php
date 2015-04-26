<?php

use Doctrine\Common\Collections\ArrayCollection;

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_INCLUDE . '/Schbas/Loan.php';
require_once PATH_WEB . '/WebInterface.php';
require_once PATH_WEB . '/Schbas/Schbas.php';

class LoanSystem extends Schbas {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	protected $_smartyPath;
	protected $_smarty;
	protected $_interface;

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
		$this->_smartyPath = PATH_SMARTY_TPL . '/web' . $path;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute($dataContainer) {

		$this->init($dataContainer);

		$schbasEnabled = TableMng::query("SELECT value FROM SystemGlobalSettings WHERE name='isSchbasClaimEnabled'");
		if ($schbasEnabled[0]['value']=="0") {
			$this->showLoanList();
		}
		else {

			if (isset($_GET['action'])) {
				$action=$_GET['action'];
				switch ($action) {
					case 'showPdf':
						$this->showPdf();
						break;
					case 'showFormPdf':
						$this->showParticipationConfirmation();
						break;
					case 'loanShowBuy':
						$this->saveSelfBuy();
						break;
					default:
						die('wrong Action-value');
						break;
				}
			}
			else {
				$this->showMainMenu();
			}
		}
	}

	private function init($dataContainer) {

		defined('_WEXEC') or die("Access denied");
		$this->entryPoint($dataContainer);

		$this->_smarty = $dataContainer->getSmarty();
		$this->_interface = new WebInterface($this->_smarty);

		require_once PATH_INCLUDE . '/TableMng.php';
		TableMng::init();

	}

	private function searchInMultiDimArray($search, $array)
	{
		foreach($array as $key => $values)
		{
			if(in_array($search, $values))
			{
				return $key;
			}
		}
		return false;
	}

	private function showMainMenu() {

		$schbasYear = $this->_em->getRepository(
			'DM:SystemGlobalSettings'
		)->findOneByName('schbas_year')->getValue();
		$user = $this->_em->getReference('DM:SystemUsers', $_SESSION['uid']);
		//$schbasYear = TableMng::query("SELECT value FROM SystemGlobalSettings WHERE name='schbas_year'");
		//get gradeValue ("Klassenstufe")
		//$gradelevel = TableMng::query(
		//	"SELECT gradelevel FROM SystemGrades
		//		WHERE id = (
		//			SELECT gradeID from SystemAttendances
		//				WHERE schoolyearId = (
		//					SELECT ID from SystemSchoolyears WHERE active=1
		//				)
		//			AND UserID='".$_SESSION['uid']."'
		//		)
		//");
		$gradelevelStmt = $this->_pdo->prepare(
			"SELECT gradelevel FROM SystemGrades g
				LEFT JOIN SystemAttendances uigs
					ON uigs.gradeId = g.ID
				WHERE uigs.userId = ? AND uigs.schoolyearId = @activeSchoolyear
		");
		$gradelevelStmt->execute(array($_SESSION['uid']));
		$gradelevel = $gradelevelStmt->fetchColumn();
		//Check if we got an entry back
		if($gradelevel === false) {
			$this->_logger->log('User accessing Schbas not in a grade!',
				'Notice', Null, json_encode(array('uid' => $_SESSION['uid'])));
			$this->_interface->dieError(
				'Du bist in keiner Klasse eingetragen!'
			);
		}
		$gradelevel = strval(intval($gradelevel)+1);
		// Filter fuer Abijahrgang

		if($gradelevel=="13") $this->_smarty->display($this->_smartyPath . 'lastGrade.tpl');;
		;

		$loanHelper = new \Babesk\Schbas\Loan($this->_dataContainer);
		$fees = $loanHelper->loanPriceOfAllBookAssignmentsForUserCalculate(
			$user
		);
		list($feeNormal, $feeReduced) = $fees;

		$loanbooksTest = $loanHelper->loanBooksOfUserGet(
			$user, ['ignoreSelfpay' => true]
		);
		$query = $this->_em->createQuery(
			'SELECT b FROM DM:SchbasBook b
			INNER JOIN b.selfpayingUsers u WITH u = :user
		');
		$query->setParameter('user', $user);
		$selfpayingBooks = $query->getResult();
		// [ {
		//     'book': '<book>',
		//     'selfpaying': '<boolean>'
		// } ]
		$booksWithSelfpayingStatus = array();
		$selfpayingBooksColl = new ArrayCollection($selfpayingBooks);
		foreach($loanbooksTest as $book) {
			$isSelfpaying = $selfpayingBooksColl->contains($book);
			$booksWithSelfpayingStatus[] = [
				'book' => $book,
				'selfpaying' => $isSelfpaying
			];
		}

		$this->_smarty->assign('booksWithStatus', $booksWithSelfpayingStatus);
		$this->_smarty->assign('feeNormal', $feeNormal);
		$this->_smarty->assign('feeReduced', $feeReduced);
		$this->_smarty->assign('schbasYear', $schbasYear);
		$this->_smarty->assign('BaBeSkTerminal', $this->checkIsKioskMode());
		$this->_smarty->assign('loanShowForm', isset($_POST['loanShowForm']));
		$this->_smarty->assign('loanShowBuy', isset($_POST['loanShowBuy']));


		$this->_smarty->display($this->_smartyPath . 'menu.tpl');
	}



	private function showLoanList() {
		require_once PATH_ACCESS . '/LoanManager.php';
		require_once PATH_ACCESS . '/InventoryManager.php';
		require_once PATH_ACCESS . '/BookManager.php';
		$this->loanManager = new LoanManager();
		$this->inventoryManager = new InventoryManager();
		$this->bookManager = new BookManager();

		$loanbooks = $this->loanManager->getLoanlistByUID($_SESSION['uid']);
		$data = array();
		foreach ($loanbooks as $loanbook){
			$invData = $this->inventoryManager->getInvDataByID($loanbook['inventory_id']);
			$bookdata = $this->bookManager->getBookDataByID($invData['book_id']);
			$datatmp = array_merge($loanbook, $invData, $bookdata);
			$data[] = $datatmp;

		}
		if (empty($data)) {
			$this->_interface->dieError('Keine ausgeliehenen B&uuml;cher vorhanden!');
		} else {
			$this->_smarty->assign('data', $data);
			$this->_smarty->display($this->_smartyPath . 'loanList.tpl');
		}


	}

	/**
	 * Checks if the Client runs in Kioskmode
	 * We dont want to let the user circumvent the Kioskmode (for example if he
	 * opens PDF-files, another program gets opened up, which can break the
	 * kiosk-mode)
	 */
	private function checkIsKioskMode() {
		return preg_match("/BaBeSK/i", $_SERVER['HTTP_USER_AGENT']);
	}

	private function saveSelfBuy() {
		TableMng::query("DELETE FROM SchbasSelfpayer WHERE UID=".$_SESSION['uid']);
		if (isset($_POST['bookID'])) {
			foreach ($_POST['bookID'] as $book) {
				TableMng::query("INSERT IGNORE INTO SchbasSelfpayer (UID, BID) VALUES (".$_SESSION['uid'].",".$book.")");
			}
		}
		$this->_smarty->display($this->_smartyPath . 'saved.tpl');
	}

	protected function preparationSchoolyearGet() {

		$schoolyear = false;
		$entry = $this->_em->getRepository('DM:SystemGlobalSettings')
			->findOneByName('schbasPreparationSchoolyearId');
		if($entry) {
			$schoolyear = $this->_em->find(
				'DM:SystemSchoolyears', $entry->getValue()
			);
		}
		if(!$schoolyear) {
			$this->_logger->logO('Could not fetch PreparationSchoolyear',
				['sev' => 'error']);
			$this->SchbasAccountingInterface->dieError('Ein Fehler ist ' .
				'beim Abrufen des Vorbereitungs-Schuljahres aufgetreten.');
		}
		return $schoolyear;
	}

	private function showParticipationConfirmation() {

		$settingsRepo = $this->_em->getRepository('DM:SystemGlobalSettings');
		$user = $this->_em->find('DM:SystemUsers', $_SESSION['uid']);
		$prepSchoolyear = $this->preparationSchoolyearGet();
		$gradeQuery = $this->_em->createQuery(
			'SELECT g FROM DM:SystemGrades g
			INNER JOIN g.attendances a
				WITH a.schoolyear = :schoolyear AND a.user = :user
		');
		$gradeQuery->setParameter('schoolyear', $prepSchoolyear);
		$gradeQuery->setParameter('user', $user);
		$grade = $gradeQuery->getOneOrNullResult();
		if(!$grade) {
			$this->_interface->dieError(
				'Der Schüler ist nicht im nächsten Schuljahr eingetragen. ' .
				'Bitte informieren sie die Schule.'
			);
		}
		$schbasYear = $prepSchoolyear->getLabel();
		$letterDateIso = $settingsRepo
			->findOneByName('schbasDateCoverLetter')
			->getValue();
		$letterDate = date('d.m.Y', strtotime($letterDateIso));
		$schbasDeadlineTransferIso = $settingsRepo
			->findOneByName('schbasDeadlineTransfer')
			->getValue();
		$schbasDeadlineTransfer = date(
			'd.m.Y', strtotime($schbasDeadlineTransferIso)
		);
		$schbasDeadlineClaimIso = $settingsRepo
			->findOneByName('schbasDeadlineClaim')
			->getValue();
		$schbasDeadlineClaim = date(
			'd.m.Y', strtotime($schbasDeadlineClaimIso)
		);
		$bankAccount = $settingsRepo->findOneByName('bank_details')
			->getValue();
		$bankData = explode('|', $bankAccount);

		//get loan fees
		$loanHelper = new \Babesk\Schbas\Loan($this->_dataContainer);
		$fees = $loanHelper->loanPriceOfAllBookAssignmentsForUserCalculate(
			$user
		);
		list($feeNormal, $feeReduced) = $fees;

		$feedback = '';
		$loanChoice = filter_input(INPUT_POST, 'loanChoice');
		$loanFee = filter_input(INPUT_POST, 'loanFee');
		$siblings = filter_input(INPUT_POST, 'siblings');
		$eb_name = filter_input(INPUT_POST, 'eb_name');
		$eb_vorname = filter_input(INPUT_POST, 'eb_vorname');
		$eb_adress = filter_input(INPUT_POST, 'eb_adress');
		$eb_tel = filter_input(INPUT_POST, 'eb_tel');
		if($loanChoice == 'noLoan') { $feedback = 'nl'; }
		else if($loanFee == 'loanSoli') { $feedback = 'ls'; }
		else if($loanFee == 'loanNormal') { $feedback = 'ln'; }
		else if($loanFee == 'loanReduced') { $feedback = 'lr'; }

		$this->_smarty->assign('user', $user);
		$this->_smarty->assign('grade', $grade);
		$this->_smarty->assign('schoolyear', $schbasYear);
		$this->_smarty->assign('letterDate', $letterDate);
		$this->_smarty->assign('schbasDeadlineClaim', $schbasDeadlineClaim);
		$this->_smarty->assign('bankData', $bankData);
		$this->_smarty->assign('feeNormal', $feeNormal);
		$this->_smarty->assign('feeReduced', $feeReduced);
		$this->_smarty->assign('loanFee', $loanFee);
		$this->_smarty->assign('siblings', $siblings);
		$this->_smarty->assign('loanChoice', $loanChoice);
		$this->_smarty->assign('parentName', $eb_name);
		$this->_smarty->assign('parentForename', $eb_vorname);
		$this->_smarty->assign('parentAddress', $eb_adress);
		$this->_smarty->assign('parentTelephone', $eb_tel);
		$this->_smarty->assign(
			'schbasDeadlineTransfer', $schbasDeadlineTransfer
		);
		$content = $this->_smarty->fetch(
			PATH_SMARTY_TPL . '/pdf/schbas-participation-confirmation.pdf.tpl'
		);
		$this->createPdf(
			'Anmeldeformular',
			$content,
			'', '', '', '',
			$grade->getGradelevel(),
			true,
			$feedback,
			$_SESSION['uid']
		);
	}

	private function showPdf() {
		require_once PATH_ACCESS. '/BookManager.php';

		//get cover letter date
		$letter_date =  TableMng::query("SELECT value FROM SystemGlobalSettings WHERE name='schbasDateCoverLetter'");
		$letter_date = date('d.m.Y', strtotime($letter_date[0]['value']));

		$booklistManager = new BookManager();
		$loanHelper = new \Babesk\Schbas\Loan($this->_dataContainer);

		//get gradelevel ("Klassenstufe")
		$gradelevel = TableMng::query("SELECT gradelevel FROM SystemGrades WHERE id=(SELECT gradeID from SystemAttendances WHERE schoolyearId=(SELECT ID from SystemSchoolyears WHERE active=1) AND UserID='".$_SESSION['uid']."')");
				$gradelevel[0]['gradelevel'] = strval(intval($gradelevel[0]['gradelevel'])+1);


		// get cover letter ("Anschreiben")
		$coverLetter = TableMng::query("SELECT title, text FROM SchbasTexts WHERE description='coverLetter'");

		// get first infotext
		$textOne = TableMng::query("SELECT title, text FROM SchbasTexts WHERE description='textOne".$gradelevel[0]['gradelevel']."'");

		// get second infotext
		$textTwo = TableMng::query("SELECT title, text FROM SchbasTexts WHERE description='textTwo".$gradelevel[0]['gradelevel']."'");

		// get third infotext
		$textThree = TableMng::query("SELECT title, text FROM SchbasTexts WHERE description='textThree".$gradelevel[0]['gradelevel']."'");

		// get booklist
		//$booklist = $booklistManager->getBooksByClass($gradelevel[0]['gradelevel']);
		$user = $this->_em->getReference('DM:SystemUsers', $_SESSION['uid']);
		$booklist = $loanHelper->loanBooksOfUserGet($user);

		$books = '<table border="0" bordercolor="#FFFFFF" style="background-color:#FFFFFF" width="100%" cellpadding="0" cellspacing="1">
				<tr style="font-weight:bold; text-align:center;"><th>Fach</th><th>Titel</th><th>Verlag</th><th>ISBN-Nr.</th><th>Preis</th></tr>';

		//$bookPrices = 0;
		foreach ($booklist as $book) {
			//$bookPrices += $book['price'];
			$books .= '<tr><td>'.$book->getSubject()->getName().'</td><td>'.$book->getTitle().'</td><td>'.$book->getPublisher().'</td><td>'.$book->getIsbn().'</td><td align="right">'.$book->getPrice().' &euro;</td></tr>';
		}
		//$books .= '<tr><td></td><td></td><td></td><td style="font-weight:bold; text-align:center;">Summe:</td><td align="right">'.$bookPrices.' &euro;</td></tr>';
		$books .= '</table>';
		$books = str_replace('ä', '&auml;', $books);
		$books = str_replace('é', '&eacute;', $books);



		$user = $this->_em->getReference('DM:SystemUsers', $_SESSION['uid']);
		$fees = $loanHelper->loanPriceOfAllBookAssignmentsForUserCalculate(
			$user
		);
		list($feeNormal, $feeReduced) = $fees;

		//get bank account
		$bank_account =  TableMng::query("SELECT value FROM SystemGlobalSettings WHERE name='bank_details'");
		$bank_account = explode("|", $bank_account[0]['value']);

		//textOne[0]['title'] wird nicht ausgegeben, unter admin darauf hinweisen!
		$pageTwo = $books.'<br/>'.$textOne[0]['text'].'<br/><br/>'.
				'<table style="border:solid" width="75%" cellpadding="2" cellspacing="2">
				<tr><td>Leihgeb&uuml;hr: </td><td>'.$feeNormal.' Euro</td></tr>
						<tr><td>(3 und mehr schulpflichtige Kinder:</td><td>'.$feeReduced.' Euro)</td></tr>
								<tr><td>Kontoinhaber:</td><td>'.$bank_account[0].'</td></tr>
								<tr><td>Kontonummer:</td><td>'.$bank_account[1].'</td></tr>
								<tr><td>Bankleitzahl:</td><td>'.$bank_account[2].'</td></tr>
								<tr><td>Kreditinstitut:</td><td>'.$bank_account[3].'</td></tr>
								</table>';




		$pageThree = "<h3>".$textTwo[0]['title']."</h3>".$textTwo[0]['text']."<br/><h3>".$textThree[0]['title']."</h3>".$textThree[0]['text'];

		$daterow = '<p style="text-align: right;">'.$letter_date."</p>";

		$this->createPdf($coverLetter[0]['title'],$daterow.$coverLetter[0]['text'],"Lehrb&uuml;cher Jahrgang ".$gradelevel[0]['gradelevel'],$pageTwo,
				'Weitere Informationen',$pageThree,$gradelevel[0]['gradelevel'],false,"",$_SESSION['uid']);
	}

	/**
	 * Creates a PDF for the Participation Confirmation and returns its Path
	 */
	private function createPdf ($page1Title,$page1Text,$page2Title,$page2Text,$page3Title,$page3Text,$gradeLevel,$msgReturn,$loanChoice,$uid) {

		require_once 'LoanSystemPdf.php';

		try {
			$pdfCreator = new LoanSystemPdf($page1Title,$page1Text,$page2Title,$page2Text,$page3Title,$page3Text,$gradeLevel,$msgReturn,$loanChoice,$uid);
			$pdfCreator->create();
			$pdfCreator->output();

		} catch (Exception $e) {
			$this->_interface->DieError('Konnte das PDF nicht erstellen!');
		}
	}
}
?>
