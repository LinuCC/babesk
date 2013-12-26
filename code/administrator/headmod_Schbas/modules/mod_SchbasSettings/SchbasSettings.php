<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ADMIN . '/headmod_Schbas/Schbas.php';

class SchbasSettings extends Schbas {

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

		require_once 'AdminSchbasSettingsInterface.php';
		require_once 'AdminSchbasSettingsProcessing.php';

		$SchbasSettingsInterface = new AdminSchbasSettingsInterface($this->relPath);
		$SchbasSettingsProcessing = new AdminSchbasSettingsProcessing($SchbasSettingsInterface);

		if (!isset($_GET['action']))
			$SchbasSettingsInterface->InitialMenu();
		else {
			switch ($_GET['action']){
				case 'editBankAccount':	$this->editBankAccount();break;
				case '2':	$SchbasSettingsInterface->LoanSettings($SchbasSettingsProcessing->getLoanSettings(),false);break;
				case '3':	$SchbasSettingsInterface->RetourSettings();break;
				case '4':	TableMng::query(sprintf("UPDATE global_settings SET value = '%s' WHERE name = '%s'", $_POST['owner']."|".$_POST['number']."|".$_POST['blz']."|".$_POST['institute'], 'bank_details'));break;
				case '5':	$this->updateFee();
				$SchbasSettingsInterface->LoanSettings($SchbasSettingsProcessing->getLoanSettings(),true);break;
				case '6':	$claim_date = $_POST['claim_Day'].".". $_POST['claim_Month'].".". $_POST['claim_Year'];
				$transfer_date = $_POST['transfer_Day'].".". $_POST['transfer_Month'].".". $_POST['transfer_Year'];
				TableMng::query(sprintf("UPDATE global_settings SET value = '%s' WHERE name = '%s'", $claim_date,"schbasDeadlineClaim"));
				TableMng::query(sprintf("UPDATE global_settings SET value = '%s' WHERE name = '%s'", $transfer_date,"schbasDeadlineTransfer"));break;
				case '7':	$claimEnabled = TableMng::query(sprintf("SELECT value FROM global_settings WHERE name='isSchbasClaimEnabled'"));
				$SchbasSettingsInterface->enableFormConfirm($claimEnabled[0]['value']);break;
				case '8':   $SchbasSettingsInterface->TextSettings();break;
				case '9':	if (isset($_POST['enable'])){
					TableMng::query(sprintf("UPDATE global_settings SET value = '%s' WHERE name = '%s'", 1, 'isSchbasClaimEnabled'));
				}else{
					TableMng::query(sprintf("UPDATE global_settings SET value = '%s' WHERE name = '%s'", 0, 'isSchbasClaimEnabled'));
				}
				$SchbasSettingsInterface->enableFormConfirmFin();break;
				case '10': $this->saveTexts();
				break;
				case 'editCoverLetter': $this->editCoverLetter();
				break;
				case 'previewInfoDocs': $this->previewInfoDocs();
				break;
				case 'setReminder': if (isset($_POST['templateID']) && isset($_POST['authorID'])){
					TableMng::query(sprintf("UPDATE global_settings SET value = '%s' WHERE name = '%s'", $_POST['templateID'], 'schbasReminderMessageID'));
					TableMng::query(sprintf("UPDATE global_settings SET value = '%s' WHERE name = '%s'", $_POST['authorID'], 'schbasReminderAuthorID'));
					$SchbasSettingsInterface->enableFormConfirmFin();break;
				} else $this->setReminder();
				break;

				case 'fetchTextsAjax':
					$this->fetchTextsAjax();
					break;
			}
		}
	}

	function updateFee(){
		$stmt = TableMng::getDB()->prepare("UPDATE schbas_fee SET fee_normal = ?, fee_reduced = ? WHERE grade = ?;");
		for ($i = 5;$i<=12; $i++){
			$_POST[$i.'norm'] = str_replace (",", ".", $_POST[$i.'norm'] );
			$_POST[$i.'erm'] = str_replace (",", ".", $_POST[$i.'erm'] );
			$stmt->bind_param('sss', $_POST[$i.'norm'], $_POST[$i.'erm'], $i);
			if(!$stmt->execute()) {
				die('schinken');
			}
		}
		$stmt->close();
	}

	protected function fetchTextsAjax() {

		$templateId = TableMng::getDb()->real_escape_string($_POST['templateId']);
		$textId = TableMng::getDb()->real_escape_string($_POST['textId']);
		try {
			$template = TableMng::query(sprintf(
					'SELECT * FROM schbas_texts WHERE `description` = "%s%s"',
					$textId,$templateId));

		} catch (Exception $e) {
			die('errorFetchTemplate');
		}

		die(json_encode($template[0]));
	}

	protected function saveTexts () {
		$grade = $_POST['grade'];
		$textOneTitle = $_POST['messagetitle'];
		$textOneText = $_POST['messagetext'];
		$textTwoTitle = $_POST['messagetitle2'];
		$textTwoText = $_POST['messagetext2'];
		$textThreeTitle = $_POST['messagetitle3'];
		$textThreeText = $_POST['messagetext3'];

		if ($textOneTitle == '') $textOneTitle = '&nbsp;';
		if ($textOneText == '') $textOneText = '&nbsp;';
		if ($textTwoTitle == '') $textTwoTitle = '&nbsp;';
		if ($textTwoText == '') $textTwoText = '&nbsp;';
		if ($textThreeTitle == '') $textThreeTitle = '&nbsp;';
		if ($textThreeText == '') $textThreeText = '&nbsp;';

		try {
			TableMng::query('UPDATE schbas_texts SET title="'.$textOneTitle.'",text="'.$textOneText.'" WHERE description="textOne'.$grade.'"',false);
			TableMng::query('UPDATE schbas_texts SET title="'.$textTwoTitle.'",text="'.$textTwoText.'" WHERE description="textTwo'.$grade.'"',false);
			TableMng::query('UPDATE schbas_texts SET title="'.$textThreeTitle.'",text="'.$textThreeText.'" WHERE description="textThree'.$grade.'"',false);
			$SchbasSettingsInterface->SavingSuccess();
		} catch (Exception $e) {
			$SchbasSettingsInterface->SavingFailed();
		};
	}

	protected function editBankAccount () {

		require_once 'AdminSchbasSettingsInterface.php';
		$SchbasSettingsInterface = new AdminSchbasSettingsInterface($this->relPath);

		if (isset($_POST['owner']) && isset($_POST['number']) && isset($_POST['blz']) && isset($_POST['institute'])) {
			try {
				TableMng::query(sprintf("UPDATE global_settings SET value = '%s' WHERE name = '%s'", $_POST['owner']."|".$_POST['number']."|".$_POST['blz']."|".$_POST['institute'], 'bank_details'));
				$SchbasSettingsInterface->SavingSuccess();
			} catch (Exception $e) {
				$SchbasSettingsInterface->SavingFailed();
			}
		}
		else {
			$temp = TableMng::query('SELECT value FROM global_settings WHERE name="bank_details"');
			$bankAccount = explode("|", $temp[0]['value']);
			$SchbasSettingsInterface->EditBankAccount($bankAccount[0],$bankAccount[1],$bankAccount[2],$bankAccount[3]);
		}
	}
	
	/**
	 * sets the schbas message template for the payment reminder
	 */
	protected function setReminder() {
		require_once 'AdminSchbasSettingsInterface.php';
		$SchbasSettingsInterface = new AdminSchbasSettingsInterface($this->relPath);
		
		$activeReminderID = TableMng::query('SELECT value FROM global_settings WHERE name="schbasReminderMessageID"');
		$reminderAuthorID = TableMng::query('SELECT value FROM global_settings WHERE name="schbasReminderAuthorID"');
		$SchbasSettingsInterface->showReminderSelection($activeReminderID,$reminderAuthorID,$this->templatesFetchSchbas());	
	}
	
	/**
	 * Fetches all of the Templates from the database and returns them
	 *
	 * @return array() An Array of Elements represented as arrays themselfs or
	 * a void Array if no elements where found
	 */
	protected function templatesFetchSchbas() {
	
		$data = array();
	
		try {
			$data = TableMng::query('SELECT * FROM MessageTemplate WHERE GID=(SELECT ID FROM messagegroups WHERE name="Schbas");');
	
		} catch (MySQLVoidDataException $e) {
			return array();
	
		} catch (Exception $e) {
			$this->_interface->dieError('Konnte die Vorlagen nicht abrufen');
		}
	
		return $data;
	}
	
	
	protected function editCoverLetter () {

		require_once 'AdminSchbasSettingsInterface.php';
		$SchbasSettingsInterface = new AdminSchbasSettingsInterface($this->relPath);

		if (isset($_POST['messagetitle']) && isset($_POST['messagetext'])) {
			$coverLetterTitle = $_POST['messagetitle'];
			$coverLetterText = $_POST['messagetext'];


			if ($coverLetterTitle == '') $coverLetterTitle = '&nbsp;';
			if ($coverLetterText == '') $coverLetterText = '&nbsp;';

			try {
				TableMng::query('UPDATE schbas_texts SET title="'.$coverLetterTitle.'",text="'.$coverLetterText.'" WHERE description="coverLetter"',false);
				$SchbasSettingsInterface->SavingSuccess();
			} catch (Exception $e) {
				$SchbasSettingsInterface->SavingFailed();
			}
		}
		else {
			$title = TableMng::query('SELECT title FROM schbas_texts WHERE description="coverLetter"');
			$text = TableMng::query('SELECT text FROM schbas_texts WHERE description="coverLetter"');
			$SchbasSettingsInterface->EditCoverLetter($title[0]['title'],$text[0]['text']);
		}
	}

	protected function previewInfoDocs () {
		require_once 'AdminSchbasSettingsInterface.php';
		$SchbasSettingsInterface = new AdminSchbasSettingsInterface($this->relPath);
		if (isset($_POST['gradelabel'])) {
			$this->showPdf();
		}
		else {
			$SchbasSettingsInterface->ShowPreviewInfoTexts();
		}
	}

	private function showPdf() {
		require_once PATH_ACCESS. '/BookManager.php';

		//get cover letter date
		$letter_date =  TableMng::query("SELECT value FROM global_settings WHERE name='schbasDateCoverLetter'");

		$booklistManager = new BookManager();

		//get gradelevel ("Klassenstufe")
		$gradelevel = $_POST['gradelabel'];

		// get cover letter ("Anschreiben")
		$coverLetter = TableMng::query("SELECT title, text FROM schbas_texts WHERE description='coverLetter'");

		// get first infotext
		$textOne = TableMng::query("SELECT title, text FROM schbas_texts WHERE description='textOne".$gradelevel."'");

		// get second infotext
		$textTwo = TableMng::query("SELECT title, text FROM schbas_texts WHERE description='textTwo".$gradelevel."'");

		// get third infotext
		$textThree = TableMng::query("SELECT title, text FROM schbas_texts WHERE description='textThree".$gradelevel."'");

		// get booklist
		$booklist = $booklistManager->getBooksByClass($gradelevel);

		$books = '<table border="0" bordercolor="#FFFFFF" style="background-color:#FFFFFF" width="100%" cellpadding="0" cellspacing="1">
				<tr style="font-weight:bold; text-align:center;"><th>Fach</th><th>Titel</th><th>Verlag</th><th>ISBN-Nr.</th><th>Preis</th></tr>';

	//	$bookPrices = 0;
		foreach ($booklist as $book) {
			// $bookPrices += $book['price'];
			$books.= '<tr><td>'.$book['subject'].'</td><td>'.$book['title'].'</td><td>'.$book['publisher'].'</td><td>'.$book['isbn'].'</td><td align="right">'.$book['price'].' &euro;</td></tr>';
		}
		//$books .= '<tr><td></td><td></td><td></td><td style="font-weight:bold; text-align:center;">Summe:</td><td align="right">'.$bookPrices.' &euro;</td></tr>';
		$books .= '</table>';
		$books = str_replace('ä', '&auml;', $books);
		$books = str_replace('é', '&eacute;', $books);

		//get loan fees
		$feeNormal = TableMng::query("SELECT fee_normal FROM schbas_fee WHERE grade=".$gradelevel);
		$feeReduced = TableMng::query("SELECT fee_reduced FROM schbas_fee WHERE grade=".$gradelevel);

		//get bank account
		$bank_account =  TableMng::query("SELECT value FROM global_settings WHERE name='bank_details'");
		$bank_account = explode("|", $bank_account[0]['value']);

		//textOne[0]['title'] wird nicht ausgegeben, unter admin darauf hinweisen!
		$pageTwo = $books.'<br/>'.$textOne[0]['text'].'<br/><br/>'.
				'<table style="border:solid" width="75%" cellpadding="2" cellspacing="2">
				<tr><td>Leihgeb&uuml;hr: </td><td>'.$feeNormal[0]['fee_normal'].' Euro</td></tr>
						<tr><td>(3 und mehr schulpflichtige Kinder:</td><td>'.$feeReduced[0]['fee_reduced'].' Euro)</td></tr>
								<tr><td>Kontoinhaber:</td><td>'.$bank_account[0].'</td></tr>
								<tr><td>Kontonummer:</td><td>'.$bank_account[1].'</td></tr>
								<tr><td>Bankleitzahl:</td><td>'.$bank_account[2].'</td></tr>
								<tr><td>Kreditinstitut:</td><td>'.$bank_account[3].'</td></tr>
								</table>';




		$pageThree = "<h3>".$textTwo[0]['title']."</h3>".$textTwo[0]['text']."<br/><h3>".$textThree[0]['title']."</h3>".$textThree[0]['text'];

		$daterow = '<p style="text-align: right;">'.$letter_date[0]['value']."</p>";

		$this->createPdf($coverLetter[0]['title'],$daterow.$coverLetter[0]['text'],"Lehrb&uuml;cher Jahrgang ".$gradelevel,$pageTwo,
				'Weitere Informationen',$pageThree,$gradelevel,false,"","jahrgang_".$gradelevel);
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
