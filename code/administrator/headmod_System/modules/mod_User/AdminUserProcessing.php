<?php
class AdminUserProcessing {
	function __construct($userInterface) {

		$this->userInterface = $userInterface;
		global $logger;
		$this->logs = $logger;
		$this->messages = array(
				'error' => array('max_credits' => 'Maximales Guthaben der Gruppe überschritten.',
						'mysql_register' => 'Problem bei dem Versuch, den neuen Benutzer in MySQL einzutragen.',
						'input1' => 'Ein Feld wurde falsch mit ', 'input2' => ' ausgefüllt',
						'uid_get_param' => 'Die Benutzer-ID (UID) vom GET-Parameter ist falsch: Der Benutzer ist nicht vorhanden!',
						'groups_get_param' => 'Ein Fehler ist beim holen der Gruppen aufgetreten.',
						'delete' => 'Ein Fehler ist beim löschen des Benutzers aufgetreten:',
						'add_cardid' => 'Konnte die Karten-ID nicht hinzufügen. Vorgang abgebrochen.',
						'register' => 'Konnte den Benutzer nicht hinzufügen!',
						'change' => 'Konnte den Benutzer nicht ändern!',
						'passwd_repeat' => 'das Passwort und das wiederholte Passwort stimmen nicht überein',
						'card_id_change' => 'Warnung: Konnte den Zähler der Karten-ID nicht erhöhen.',
						'no_groups' => 'Es sind keine Gruppen vorhanden!',
						'user_existing' => ' der Benutzer ist schon vorhanden oder die Kartennummer wird schon benutzt.',
						'booklisterror' => 'Fehler beim &Uuml;berpr&uuml;fen der Schulbuchausleihe.',
				'booklist' => 'Benutzer kann nicht gel&ouml;scht werden. Es sind noch B&uuml;cher ausgeliehen!'),
				'get_data_failed' => 'Ein Fehler ist beim fetchen der Daten aufgetreten',
				'notice' => array('please_repeat' => 'Bitte wiederholen sie den Vorgang.'));
	}
	//////////////////////////////////////////////////
	//--------------------Register--------------------
	//////////////////////////////////////////////////
	/**
	 * Registers an User
	 * RegisterUser Registers an user by adding a MySQL-usertable-entry
	 * @param string $forename The forename of the user
	 * @param string $name The name of the user
	 * @param string $username The username (to login) of the user
	 * @param string $passwd The password of the user
	 * @param string $passwd_repeat The reapeated password, to make sure its spelled right
	 * @param number $cardID The Card-id the user has
	 * @param string $birthday The Birthday of the user. Format: YYYY-MM-DD
	 * @param number $GID The Id of the Group the user is in
	 * @param string $credits How much credits the user has
	 * @param string $class the class
	 * @throws Exception if something gone wrong
	 */
	function RegisterUser($forename, $name, $username, $passwd, $passwd_repeat, $cardID, $birthday, $GID, $credits,$class) {

		require_once PATH_ACCESS . '/UserManager.php';
		require_once PATH_ACCESS . '/CardManager.php';
		require_once PATH_ACCESS . '/GroupManager.php';
		require_once PATH_ACCESS . "/LogManager.php";

		$userManager = new UserManager();
		$cardManager = new CardManager();
		$groupManager = new GroupManager();
		$logger = new LogManager();

		//checks the input for wrong Characters etc
		try {
			inputcheck($forename, 'name');
			inputcheck($name, 'name');
			inputcheck($username, 'name');
			inputcheck($passwd, 'password');
			inputcheck($passwd_repeat, 'password');
			inputcheck($cardID, 'card_id');
			inputcheck($birthday, 'birthday');
			inputcheck($GID, 'id');

		} catch (Exception $e) {
			$this->userInterface->ShowRepeatRegister();
			throw new Exception(
					$this->messages['error']['input1'] . '"' . $e->getMessage() . '"'
							. $this->messages['error']['input2'] . '<br><br>' . $this->messages['error']['register']);
		}
		if ($cardManager->is_card_existing($cardID) || $userManager->isUserExisting($forename, $name, $username)) {
			throw new Exception($this->messages['error']['register'] . $this->messages['error']['user_existing']);
		}
		if ($credits == '')
			$credits = '0';
		else {
			try {
				inputcheck($name, 'name');
			} catch (Exception $e) {
				$this->userInterface->ShowRepeatRegister();
				throw new Exception(
						$this->messages['error']['input1'] . '"' . $e->getMessage() . '"'
								. $this->messages['error']['input2'] . '<br><br>'
								. $this->messages['error']['register']);
			}
		}
		//check max amount of credits of the group
		if ($credits > $groupManager->getMaxCredit($GID)) {
			$this->userInterface
					->dieError(
							$this->messages['error']['max_credits'] . 'maximales Guthaben der Gruppe:'
									. $groupManager->getMaxCredit($GID) . '€');
			throw new Exception($this->messages['error']['register']);
		}

		try {
			$userManager->addUser($name, $forename, $username, $passwd, $birthday, $credits, $GID, $class);
		} catch (Exception $e) {
			$this->userInterface
					->dieError("<br>" . $this->messages['error']['mysql_register'] . $e->getMessage() . "<br>");
			$cardManager->delEntry($cardID);
			throw new Exception($this->messages['error']['register'] . $e->getMessage());
		}

		try {
			$cardManager->addCard($cardID, $userManager->getUserID($username));
		} catch (Exception $e) {
			$userManager->delEntry($userManager->getUserID($username));//user has no cardID, delete him
			throw new Exception($this->messages['error']['add_cardid'] . $e->getMessage());
		}

		$this->userInterface->ShowRegisterFin($name, $forename);

		$_SESSION['CARD_ID'] = NULL;
		$logger
				->log(USERS, NOTICE,
						"REG_ADDED_USER-ID:" . $cardID . "-NAME:" . $name . "-FORENAME:" . $forename . "-BIRTHDAY:"
								. $birthday . "-CREDITS:" . $credits . "-GID:" . $GID . "-");
	}

	function getGroups() {

		require_once PATH_ACCESS . '/GroupManager.php';

		$group_manager = new GroupManager('groups');

		$arr_group_id = array();
		$arr_group_name = array();

		try {
			$sql_groups = $group_manager->getTableData();
		} catch (MySQLVoidDataException $e) {
			$this->userInterface->dieError($this->messages['error']['no_groups']);
		}
		if (!empty($sql_groups)) {
			foreach ($sql_groups as $group) {
				$arr_group_id[] = $group["ID"];
				$arr_group_name[] = $group["name"];
			}
		}
		return array('arr_gid' => $arr_group_id, 'arr_group_name' => $arr_group_name);
		// 		$smarty->assign('gid', $arr_group_id);
		// 		$smarty->assign('g_names', $arr_group_name);
	}
	//////////////////////////////////////////////////
	//--------------------Show Users--------------------
	//////////////////////////////////////////////////
	function ShowUsers($filter) {
		require_once PATH_ACCESS . '/UserManager.php';
		require_once PATH_ACCESS . '/GroupManager.php';

		$userManager = new UserManager();
		$groupManager = new GroupManager();

		try {
			$groups = $groupManager->getTableData();
			isset($_GET['sitePointer'])?$showPage = $_GET['sitePointer'] + 0:$showPage = 1;
			$nextPointer = $showPage*10-10;
			$users = $userManager->getUsersSorted($nextPointer,$filter);
		} catch (Exception $e) {
			$this->logs
					->log('ADMIN', 'MODERATE',
							sprintf('Error while getting Data from MySQL:%s in %s', $e->getMessage(), __METHOD__));
			$this->userInterface->dieError($this->messages['error']['get_data_failed']);
		}

		foreach ($users as &$user) {
			$is_named = false;
			foreach ($groups as $gn) {
				if ($gn['ID'] == $user['GID']) {
					$user['groupname'] = $gn['name'];
					$is_named = true;
					break;
				}
			}
			$is_named or $user['groupname'] = 'Error: This group is non-existent!';
		}
		$navbar = navBar($showPage, 'users', 'System', 'User', '2',$filter);
		$this->userInterface->ShowUsers($users,$navbar);
	}
	//////////////////////////////////////////////////
	//--------------------Delete User--------------------
	//////////////////////////////////////////////////
	/**
	 * Shows the confirm-deletion-dialog
	 * Enter description here ...
	 * @param number $uid the UserID
	 */
	function DeleteConfirmation($uid) {

		require_once PATH_ACCESS . '/UserManager.php';

		$userManager = new UserManager();


		try {
			$user = $userManager->getEntryData($uid, 'forename', 'name');
		} catch (Exception $e) {
			$this->userInterface
					->dieError($this->messages['error']['uid_get_param'] . ';<br>ExceptionMessage:' . $e->getMessage());
		}

		$this->userInterface->ShowDeleteConfirmation($uid, $user['forename'], $user['name']);
	}

	function DeleteUser($uid) {

		require_once PATH_ACCESS . '/UserManager.php';
		require_once PATH_ACCESS . '/CardManager.php';
		require_once PATH_ACCESS . '/LoanManager.php';

		$loanManager = new LoanManager();
		$userManager = new UserManager();
		$cardManager = new CardManager();

		try {
			$hasBooks = $loanManager->getLoanlistByUID($uid);
			if (sizeof($hasBooks) != 0)
				$this->userInterface->dieError($this->messages['error']['booklist'] );
		} catch (Exception $e) {
			$this->userInterface->dieError($this->messages['error']['booklisterror'] . $e->getMessage());
		}

		$user = array();

		try {

			$user = $userManager->getEntryData($uid, 'forename', 'name','credit','birthday','class');

			$userManager->delEntry($uid);
			$cardManager->delEntry($cardManager->getCardIDByUserID($uid));
		} catch (Exception $e) {
			$this->userInterface->dieError($this->messages['error']['delete'] . $e->getMessage());
		}
		if ($this->createPdf($user,$uid)) $this->userInterface->ShowDeleteFin($uid);
		else $this->userInterface->dieError('Fehler beim Generieren des PDFs!');
	}


	/** Creates a PDF for the Message
	 *
	 */
	private function createPdf ($user,$uid) {
		require_once  PATH_INCLUDE .('/pdf/tcpdf/config/lang/ger.php');
		require_once PATH_INCLUDE . '/pdf/tcpdf/tcpdf.php';

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('LeG Uelzen');

		$pdf->SetKeywords('');

		// set default header data
		$pdf->SetHeaderData('../../../../web/headmod_Messages/modules/mod_MessageAdminMenu/logo.jpg', 15, 'LeG Uelzen', "Abmeldung von: ".$user['forename']." ".$user['name']."\nKlasse: ".$user['class'], array(0,0,0), array(0,0,0));
		$pdf->setFooterData($tc=array(0,0,0), $lc=array(0,0,0));

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		//set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		//set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		//set some language-dependent strings
		$pdf->setLanguageArray($l);

		// ---------------------------------------------------------

		// set default font subsetting mode
		$pdf->setFontSubsetting(true);

		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		$pdf->SetFont('helvetica', '', 11, '', true);

		// Add a page
		// This method has several options, check the source code documentation for more information.
		$pdf->AddPage();

		// set text shadow effect
		$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

		// Set some content to print
		$html = '<p align="center"><h2>R&uuml;ckgabe der LeG-Card / L&ouml;schung der Benutzerdaten</h2></p><br>'
				.'Hiermit wird best&auml;tigt, dass die Schulb&uuml;cher von '.$user['forename'].' '.$user['name'].' vollst&auml;ndig zur&uuml;ckgegeben wurden. <br/>
Hiermit wird best&auml;tigt, dass s&auml;mtliche personenbezogenen Daten am '.date("d.m.Y").' aus dem System gel&ouml;scht wurden.<br/>';

		if ($user['credit']=="0.00") $html .= 'Es liegt kein Restguthaben vor.<br/>';
		else $html .= 'Es liegt ein Restguthaben in H&ouml;he von '.$user['credit'].' &euro; vor. Dieses muss beim Caterer abgeholt werden.<br/>';
 $html .= 'Mit der R&uuml;ckgabe der LeG-Card kann das Pfandgeld in H&ouml;he von 3,50 &euro; zzgl. 0,50 &euro;, je nach Zustand der H&uuml;lle, ausbezahlt werden.<br/>
<hr>
<p align="center"><h3>Auszahlung des Restguthabens</h3></p><br>
Restguthaben in H&ouml;he von '.$user['credit'].' &euro; am ___.___.2013 erhalten.<br><br>
<br>						Unterschrift Caterer
		<br><hr>
<p align="center"><h3>Pfanderstattung</h3></p><br>
Bitte geben Sie diesen Abschnitt im Lessing-Gymnasium ab.<br>
Bitte kreuzen Sie an, ob Sie den Pfandbetrag an die Sch&uuml;lergenossenschaft Gnissel des LeG Uelzen spenden m&ouml;chten
		oder eine &Uuml;berweisung auf ein Bankkonto w&uuml;nschen.<br>

[&nbsp;&nbsp;] Das Pfandgeld m&ouml;chte ich an Gnissel spenden<br>
[&nbsp;&nbsp;] Ich m&ouml;chte das Pfandgeld auf folgendes Konto &uuml;berwiesen haben:<br>
Kontoinhaber:   <br>
Kontonummer:<br>
BLZ:		<br>
Kreditinstitut: <br><br>

Uelzen, den ___.___.2013
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Unterschrift Elternteil bzw. vollj&auml;hriger Sch&uuml;ler<br>


<hr>
<p align="center"><h3>Abschnitt f&uuml;r den Caterer</h3></p><br>
 Restguthaben in H&ouml;he von '.$user['credit'].' &euro; am ___.___.2013 erhalten.<br><br>
		<br><br>Unterschrift Elternteil bzw. vollj&auml;hriger Sch&uuml;ler
		';

		// Print text using writeHTMLCell()
		$pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

		// ---------------------------------------------------------

		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.
		$pdf->Output('../include/pdf/tempPdf/deleted_'.$uid.'.pdf', 'F');
		return true;
	}


	/**
	 * This function prepares and shows the ChangeUser-Form
	 * Enter description here ...
	 * @param string (numeric) $uid The ID of the User
	 */
	function ChangeUserForm($uid) {

		require_once PATH_ACCESS . '/UserManager.php';
		require_once PATH_ACCESS . '/GroupManager.php';
		require_once PATH_ACCESS . '/CardManager.php';
		$cardManager = new CardManager();
		$userManager = new UserManager();
		$groupManager = new GroupManager();
		$cardnumber = $cardManager->getCardnumberByUserID($uid);
		try {
			$user = TableMng::query(sprintf(
				'SELECT u.*,
				(SELECT CONCAT(g.gradeValue, g.label) AS class
					FROM jointUsersInGrade uig
					LEFT JOIN grade g ON uig.gradeId = g.ID
					LEFT JOIN jointGradeInSchoolYear gisy
						ON gisy.gradeId = g.ID
					LEFT JOIN schoolYear sy ON gisy.schoolyearId = sy.ID
					WHERE uig.userId = u.ID) AS class
				FROM users u WHERE `ID` = %s', $uid), true);

		} catch (Exception $e) {
			$this->userInterface->dieError($this->messages['error']['uid_get_param'] . $e->getMessage());
		}
		try {
			$groups = $this->getGroups();
		} catch (Exception $e) {
			$this->userInterface->dieError($this->messages['error']['groups_get_param'] . $e->getMessage());
		}

		$this->userInterface->ShowChangeUser($user[0], $groups['arr_gid'], $groups['arr_group_name'], $cardnumber);
	}

	/**
	 * This function changes the user by the given parameters.
	 * This function just alters the entries of the table, so if
	 * unchanged arguments are given these will be not changed.
	 * @param numeric string $old_id The ID of the object to change
	 * @param numeric string $id The new ID of the object
	 * @param string $forename The new forename of the object
	 * @param string $name The new name of the object
	 * @param string $username The new username of the object
	 * @param string $passwd The new password of the object
	 * @param string $passwd_repeat The repition of the password
	 * @param string $birthday The new birthday of the object. Format YYYY-MM-DD
	 * @param numeric string $GID The new Group-ID for the object
	 * @param unknown_type $credits
	 * @param boolean $locked is 1 if account is locked
	 * @throws Exception
	 */
	function ChangeUser($old_id, $id, $forename, $name, $username, $passwd, $passwd_repeat, $birthday, $GID, $credits,
			$locked, $cardnumber, $soli,$class) {

		require_once PATH_ACCESS . '/UserManager.php';
		require_once PATH_ACCESS . '/CardManager.php';
		$userManager = new UserManager();
		$cardManager = new CardManager();
		try {
			inputcheck($forename, 'name');
			inputcheck($name, 'name');
			inputcheck($username, 'name');
			inputcheck($birthday, 'birthday');
			inputcheck($GID, 'id');
			inputcheck($credits, 'credits');
			if ($cardnumber)
				inputcheck($cardnumber, 'card_id');
		} catch (Exception $e) {
			$this->userInterface
					->dieError(
							$this->messages['error']['input1'] . '"' . $e->getMessage() . '"'
									. $this->messages['error']['input2']);
			//throw new Exception($this->messages['error']['change']);
		}
		if (isset($passwd, $passwd_repeat)) {
			if ($passwd != $passwd_repeat) {
				try {
					inputcheck($passwd, 'password');
					inputcheck($passwd_repeat, 'password');
				} catch (Exception $e) {
					$this->userInterface->dieError($this->messages['error']['passwd_repeat']);
				}
			}
		}
		try {
			$userManager
					->alterUser($old_id, $id, $name, $forename, $username, hash_password($passwd), $birthday, $credits,
								$GID, $locked, $soli,$class);
			if ($cardnumber) {
				$cardManager->changeCardnumber($cardManager->getIDByUserID($id), $cardnumber);
				try {
					$cardManager->addCardIdChange($cardManager->getIDByUserID($id));
				} catch (Exception $e) {
					$this->userInterface->dieError($this->messages['error']['card_id_change']);
					$this->logs
							->log(ADMIN, MODERATE,
									'Error: Could not finish addCardIdChange() in ' . __METHOD__ . '; CardID: '
											. $cardManager->getIDByUserID($id));
				}
			}
		} catch (Exception $e) {
			$this->userInterface->dieError($this->messages['error']['change'] . $e->getMessage());
		}
		$cardChanges = $cardManager->getCardIDChanges($cardManager->getIDByUserID($id));
		$this->userInterface
				->ShowChangeUserFin($id, $name, $forename, $username, $birthday, $credits, $GID, $locked, $soli,$class,$cardChanges);
	}

	var $messages = array();
	private $userInterface;

	/**
	 *@var Logger
	 */
	protected $logs;
}

?>