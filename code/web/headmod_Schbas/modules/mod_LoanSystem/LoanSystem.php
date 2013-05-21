<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_WEB . '/WebInterface.php';

class LoanSystem extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	private $_smartyPath;
	private $_smarty;
	private $_interface;

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
		$this->_smartyPath = PATH_SMARTY . '/templates/web' . $path;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute($dataContainer) {
		//No direct access
		defined('_WEXEC') or die("Access denied");
		$this->init();
		//var_dump($_POST);
		$schbasEnabled = TableMng::query("SELECT value FROM global_settings WHERE name='isSchbasClaimEnabled'",true);
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
						$this->showFormPdf();
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

	private function init() {
		defined('_WEXEC') or die("Access denied");
		global $smarty;
		$this->_smarty = $smarty;
		$this->_interface = new WebInterface($smarty);
		require_once PATH_INCLUDE . '/TableMng.php';
		TableMng::init();

	}

	private function showMainMenu() {
		$schbasYear = TableMng::query("SELECT value FROM global_settings WHERE name='schbas_year'",true);
		$this->_smarty->assign('schbasYear', $schbasYear[0]['value']);
		$this->_smarty->assign('BaBeSkTerminal', $this->checkIsKioskMode());
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


	private function showFormPdf() {

		//get gradeValue ("Klassenstufe")
		$gradeValue = TableMng::query("SELECT gradeValue FROM grade WHERE id=(SELECT GradeID from jointusersingrade WHERE UserID='".$_SESSION['uid']."')",true);
		$gradeValue[0]['gradeValue'] = strval(intval($gradeValue[0]['gradeValue'])+1);
		
		$schbasYear = TableMng::query("SELECT value FROM global_settings WHERE name='schbas_year'",true);
		
		
		//get cover letter date
		$letter_date =  TableMng::query("SELECT value FROM global_settings WHERE name='schbasDateCoverLetter'",true);
		
		$schbasDeadlineClaim = TableMng::query("SELECT value FROM global_settings WHERE name='schbasDeadlineClaim'",true);
		$text = "</h4>Bitte ausgef&uuml;llt zur&uuml;ckgeben an das Sekretariat des Lessing-Gymnasiums bis zum ".$schbasDeadlineClaim[0]['value']."!</h4>";

		$text .= '<table border="1"><tr>';
		if (isset($_POST['eb_name']) && $_POST['eb_name']=="" || isset($_POST['eb_vorname']) && $_POST['eb_vorname']=="") $text .= "<td>Name, Vorname des/der Erziehungsberechtigten:<br><br><br><br><br><br></td>";
		else $text .= "<td>Name, Vorname des/der Erziehungsberechtigten:<br/>".$_POST['eb_name'].", ".$_POST['eb_vorname']."</td>";
		if (isset($_POST['eb_adress']) && $_POST['eb_adress']=="") $text .= "<td>Anschrift: </td>";
		else $text .= "<td>Anschrift:<br/>".$_POST['eb_adress']."</td>";
		if (isset($_POST['eb_tel']) && $_POST['eb_tel']=="") $text .= "<td>Telefon:</td>";
		else $text .= "<td>Telefon:<br/>".$_POST['eb_tel']."</td>";



		$name =  TableMng::query("SELECT forename, name FROM users WHERE ID = '".$_SESSION['uid']."'",true);

		$text .= '</tr><tr><td colspan="2">Name, Vorname des Sch&uuml;lers / der Sch&uuml;lerin:<br>'.$name[0]['name'].", ".$name[0]['forename'].'</td>';
		$text .= "<td><b>Jahrgangsstufe: ".$gradeValue[0]['gradeValue']."</b></td>";

		$text .= "</tr></table>&nbsp;<br/><br/>";

		$text .= "An der entgeltlichen Ausleihe von Lernmitteln im Schuljahr ".$schbasYear[0]['value']." ";
		
		//get loan fees
		$feeNormal = TableMng::query("SELECT fee_normal FROM schbas_fee WHERE grade=".$gradeValue[0]['gradeValue'],true);
		$feeReduced = TableMng::query("SELECT fee_reduced FROM schbas_fee WHERE grade=".$gradeValue[0]['gradeValue'],true);
		
		$feedback = "";
		if ($_POST['loanChoice']=="noLoan") {
			$feedback = "nl";
			$text .= "nehmen wir nicht teil. ";
			if (isset($_POST['noLoanFee']) && $_POST['noLoanFee']=="true") {
				$feedback = "nls";
				$text .= "Wir geh&ouml;ren zu dem von der Zahlung des Entgelts befreiten Personenkreis. Leistungsbescheid bzw. &auml;hnlicher Nachweis ist beigef&uuml;gt.";
			}
		}
		else {
			$text .= "nehmen wir teil und melden uns hiermit verbindlich zu den in Ihrem Schreiben vom ".$letter_date[0]['value']." genannten Bedingungen an.<br/>";
			if (isset ($_POST['loanFee']) && $_POST['loanFee']=="loanNormal") {
				$feedback = "ln";
				$text .= "Der Betrag von ".$feeNormal[0]['fee_normal']." &euro; ";
			}
			else if (isset($_POST['loanFee']) && $_POST['loanFee']=="loanReduced") { 
				$feedback = "lr";
				$text .= "Den Betrag von ".$fee_reduced[0]['feeReduced']." &euro; (mehr als 2 schulpflichtigen Kinder) ";
			}
			
			//get bank account details
			$bank_account =  TableMng::query("SELECT value FROM global_settings WHERE name='bank_details'",true);
			$bank_account = explode("|", $bank_account[0]['value']);
			$schbasDeadlineTransfer = TableMng::query("SELECT value FROM global_settings WHERE name='schbasDeadlineTransfer'",true);
			$text .= "wird bis sp&auml;testens ".$schbasDeadlineTransfer[0]['value']." auf das Konto ".$bank_account[1]." bei der ".$bank_account[3]." (".$bank_account[2].") &uuml;berwiesen.<br/>";
			$text .= "Empf&auml;nger: ".$bank_account[0].". Sollte der Betrag nicht fristgerecht eingehen, besteht kein Anspruch auf Teilnahme an der Ausleihe.<br/>";
			$username = TableMng::query("SELECT username FROM users WHERE ID=".$_SESSION['uid'],true);
			$text .= "Verwendungszeck: ".$username[0]['username']." ".$gradeValue[0]['gradeValue'];
			if (isset($_POST['loanFee']) && $_POST['loanFee']=="loanReduced") {
				$text .= "<u>Weitere schulpflichtige Kinder im Haushalt (Schuljahr ".$schbasYear[0]['value']."):</u><br/>Name, Vorname, Schule jedes Kindes:<br/>";
				if (isset($_POST['siblings']) && $_POST['siblings']=="") $text .= '<table style="border:solid" width="75%" cellpadding="2" cellspacing="2">
						<tr><td><br><br><br><br><br><br><br></td></tr></table>';
				else $text .=	$_POST['siblings'];
			}
		}	
		$this->createPdf('Anmeldeformular',$text,'','','','',$gradeValue[0]['gradeValue'],true,$feedback,$_SESSION['uid']);
	}

	private function showPdf() {
		require_once PATH_ACCESS. '/BookManager.php';

		//get cover letter date
		$letter_date =  TableMng::query("SELECT value FROM global_settings WHERE name='schbasDateCoverLetter'",true);
		
		$booklistManager = new BookManager();

		//get gradeValue ("Klassenstufe")
		$gradeValue = TableMng::query("SELECT gradeValue FROM grade WHERE id=(SELECT GradeID from jointusersingrade WHERE UserID='".$_SESSION['uid']."')",true);
		$gradeValue[0]['gradeValue'] = strval(intval($gradeValue[0]['gradeValue'])+1);

		// get cover letter ("Anschreiben")
		$coverLetter = TableMng::query("SELECT title, text FROM schbas_texts WHERE description='coverLetter'",true);

		// get first infotext
		$textOne = TableMng::query("SELECT title, text FROM schbas_texts WHERE description='textOne".$gradeValue[0]['gradeValue']."'",true);

		// get second infotext
		$textTwo = TableMng::query("SELECT title, text FROM schbas_texts WHERE description='textTwo".$gradeValue[0]['gradeValue']."'",true);

		// get third infotext
		$textThree = TableMng::query("SELECT title, text FROM schbas_texts WHERE description='textThree".$gradeValue[0]['gradeValue']."'",true);

		// get booklist
		$booklist = $booklistManager->getBooksByClass($gradeValue[0]['gradeValue']);

		$books = '<table border="0" bordercolor="#FFFFFF" style="background-color:#FFFFFF" width="100%" cellpadding="0" cellspacing="1">
				<tr style="font-weight:bold; text-align:center;"><th>Fach</th><th>Titel</th><th>Verlag</th><th>ISBN-Nr.</th><th>Preis</th></tr>';

		$bookPrices = 0;
		foreach ($booklist as $book) {
			$bookPrices += $book['price'];
			$books.= '<tr><td>'.$book['subject'].'</td><td>'.$book['title'].'</td><td>'.$book['publisher'].'</td><td>'.$book['isbn'].'</td><td align="right">'.$book['price'].' &euro;</td></tr>';
		}
		$books .= '<tr><td></td><td></td><td></td><td style="font-weight:bold; text-align:center;">Summe:</td><td align="right">'.$bookPrices.' &euro;</td></tr>';
		$books .= '</table>';
		$books = str_replace('ä', '&auml;', $books);
		$books = str_replace('é', '&eacute;', $books);

		//get loan fees
		$feeNormal = TableMng::query("SELECT fee_normal FROM schbas_fee WHERE grade=".$gradeValue[0]['gradeValue'],true);
		$feeReduced = TableMng::query("SELECT fee_reduced FROM schbas_fee WHERE grade=".$gradeValue[0]['gradeValue'],true);

		//get bank account
		$bank_account =  TableMng::query("SELECT value FROM global_settings WHERE name='bank_details'",true);
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
		
		$this->createPdf($coverLetter[0]['title'],$daterow.$coverLetter[0]['text'],"Lehrb&uuml;cher Jahrgang ".$gradeValue[0]['gradeValue'],$pageTwo,
				'Weitere Informationen',$pageThree,$gradeValue[0]['gradeValue'],false,"",$_SESSION['uid']);
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