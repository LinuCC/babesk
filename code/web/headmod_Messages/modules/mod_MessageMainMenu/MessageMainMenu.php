<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_WEB . '/headmod_Messages/MessageFunctions.php';

/**
 * Entry-Point for the User to display, print and create new messages
 * The User can only create a new Message if he is in the correct group
 *
 * @author Mirek Hancl
 * @author Pascal Ernst <pascal.cc.ernst@gmail.com>
 */
class MessageMainMenu extends Module {


	///////////////////////////////////////////////////////////////////////
	//Constructor
	///////////////////////////////////////////////////////////////////////
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
		$this->_smartyPath = PATH_SMARTY . '/templates/web' . $path;
	}

	///////////////////////////////////////////////////////////////////////
	//Methods
	///////////////////////////////////////////////////////////////////////
	public function execute($dataContainer) {
		//No direct access
		$this->init();
		if (isset($_GET['action'])) {
			$action=$_GET['action'];
			switch ($action) {
				case 'newMessage':
					$this->newMessageForm();
					break;
				case 'saveMessage':
					$this->saveMessage();
					break;
				case 'deleteMessage':
					$this->deleteMessage();
					break;
				case 'showMessage':
					$this->showMessage();
					break;
				case 'showMessageAdmin':
					$this->showMessageAdmin();
					break;
				case 'searchUserAjax':
					$this->searchUserAjax();
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

	private function init() {
		defined('_WEXEC') or die("Access denied");
		global $smarty;
		$this->_smarty = $smarty;
		$this->_interface = new WebInterface($smarty);
		require_once PATH_INCLUDE . '/TableMng.php';
		TableMng::init();
		$this->setEditor();
	}

	/**
	 * Sets the variable $_isEditor based on the User
	 */
	private function setEditor() {
		$contractGID = TableMng::query('SELECT value FROM global_settings WHERE name = "messagesEdit"',true);
		$userGID = TableMng::query('SELECT GID FROM users WHERE ID =
			"'.$_SESSION['uid'].'"',true);
		$this->_isEditor = ($contractGID[0]['value'] == $userGID[0]['GID']);
	}

	/**
	 * Fetches the messages that the User is allowed to manage
	 *
	 * @return array() an array of array-elements describing the messages
	 */
	private function fetchManagedMessages() {
		$messages = array();
		$query = sprintf(
			'SELECT m.ID AS ID,m.title AS title,m.validFrom AS validFrom,
				m.validTo AS validTo
			FROM Message m
			JOIN MessageManagers mm ON m.ID = mm.messageId AND mm.userId = %s
			', $_SESSION['uid']);
		try {
			$messages = TableMng::query ($query, true);
		} catch (MySQLVoidDataException $e) {
			$this->_smarty->append('error','Konnte keine selbst-erstellten Nachrichten finden');
		} catch (Exception $e) {
			$this->_smarty->append(
					sprintf ('Konnte die selbst-erstellten Nachrichten nicht abrufen!', $e->getMessage()));
		}
		return $messages;
	}

	/**
	 * Fetches the messages directed towards this user
	 *
	 * @return array() an array of array-elements describing the messages
	 */
	private function fetchReceivedMessages() {
		$messages = array();
		$query = sprintf(
			'SELECT m.id AS ID,m.title AS title,m.validFrom AS validFrom,
			m.validTo AS validTo, mr.return AS "return"
			FROM Message m
			JOIN MessageReceivers mr ON mr.userId = %s
				AND m.ID = mr.messageId
			WHERE SYSDATE() BETWEEN m.validFrom AND m.validTo
			', $_SESSION['uid']);
		try {
			$messages = TableMng::query ($query, true);
		} catch (MySQLVoidDataException $e) {
			$this->_smarty->assign('error','Keine Post vorhanden!');
		} catch (Exception $e) {
			$this->_interface->DieError (
					sprintf ('Konnte die Post nicht abrufen! %s', $e->getMessage()));
		}
		return $messages;
	}

	/**
	 * Displays the MainMenu of Messages
	 */
	private function showMainMenu() {
		$createdMsg = $receivedMsg = array();
		if ($this->_isEditor) {
			$this->_smarty->assign('editor',true);
			$createdMsg = $this->fetchManagedMessages();
		}
		$receivedMsg = $this->fetchReceivedMessages();
		$this->_smarty->assign('createdMsg', $createdMsg);
		$this->_smarty->assign('receivedMsg', $receivedMsg);
		$this->_smarty->assign('BaBeSkTerminal', $this->checkIsKioskMode());
		$this->_smarty->display($this->_smartyPath . 'menu.tpl');
	}

	/**
	 * Shows a form to the User in which he can create a new Message
	 *
	 * @fixme grades do not get sorted out by schoolyear
	 */
	private function newMessageForm() {
		$grades = TableMng::query(
			'SELECT CONCAT(gradeValue, label) AS name, ID
			FROM grade', true);
		$this->_smarty->assign('grades',$grades);
		$this->_smarty->display($this->_smartyPath . 'newMessage.tpl');
	}

	/**
	 * Saves a contract
	 */
	private function saveMessage() {
		$db = TableMng::getDb();
		$msgTitle = $db->real_escape_string($_POST['contracttitle']);
		$msgText = $db->real_escape_string($_POST['contracttext']);
		$startDate = sprintf('%s-%s-%s',
			$db->real_escape_string($_POST['StartDateYear']),
			$db->real_escape_string($_POST['StartDateMonth']),
			$db->real_escape_string($_POST['StartDateDay']));
		$endDate = sprintf('%s-%s-%s',
			$db->real_escape_string($_POST['EndDateYear']),
			$db->real_escape_string($_POST['EndDateMonth']),
			$db->real_escape_string($_POST['EndDateDay']));
		$db->autocommit(false);
		TableMng::query(sprintf(
			'INSERT INTO Message (originUserId,title,text,validFrom,validTo)
			VALUES (%s,"%s","%s","%s","%s")',
			$_SESSION['uid'], $msgTitle, $msgText, $startDate, $endDate));

		$messageId = $db->insert_id;
		//Add creator to the managers-list
		TableMng::query(sprintf(
			'INSERT INTO MessageManagers (messageId, userId)
			VALUES (%s, %s)', $messageId, $_SESSION['uid']));
		$shouldReturn = isset($_POST['shouldReturn']) ?
			'shouldReturn' : 'noReturn';
		//Add receivers to the receiver-list
		$queryReceivers = 'INSERT INTO MessageReceivers
			(`messageId`, `userId`, `return`)
			VALUES (?, ?, ?)';
		$stmt = $db->prepare($queryReceivers);
		$msgReceiverIds = array();
		if(isset($_POST['msgReceiver']) && count($_POST['msgReceiver'])) {
			$msgReceiverIds = array_merge($msgReceiverIds,
				$_POST['msgReceiver']);
		}
		$userIdsOfGrades = $this->saveMessageGrades();
		if(count($userIdsOfGrades)) {
			$msgReceiverIds = array_merge($msgReceiverIds, $userIdsOfGrades);
		}
		foreach ($msgReceiverIds as $rec) {
			$stmt->bind_param("iis", $messageId, $rec, $shouldReturn);
			$stmt->execute();
		}
		$db->commit();
		$db->autocommit(true);
		$this->addSavedCopiesCount(count($msgReceiverIds), $_SESSION['uid']);
		$this->_smarty->display($this->_smartyPath . 'new_contract_fin.tpl');
	}

	/**
	 * Handles the user-selected grades when saving a new message
	 *
	 * @return an Array of userIds of the users that are in the selected grades
	 */
	private function saveMessageGrades() {
		$userIds = array();
		$userId = '';
		if(isset($_POST['grades']) && count($_POST['grades'])) {
			$db = TableMng::getDb();
			$grades = $_POST['grades'];
			$stmt =$db->prepare("SELECT UserID AS userId
				FROM jointUsersInGrade WHERE GradeID = ?");
			foreach($grades as $gradeId) {
				$stmt->bind_param("i", $gradeId);
				$stmt->execute();
				$stmt->bind_result($userId);
				while($stmt->fetch()) {
					$userIds [] = $userId;
				}
			}
		}
		return $userIds;
	}

	/**
	 * Deletes a Contract
	 */
	private function deleteMessage() {
		$db = TableMng::getDb();
		$messageId = TableMng::getDb()->real_escape_string($_GET['ID']);
		if ($this->_isEditor &&
			MessageFunctions::checkIsManagerOf($messageId, $_SESSION['uid'])) {
			try {
				$db->autocommit(false);
				TableMng::query(sprintf(
					'DELETE FROM Message WHERE `id` = "%s";', $messageId));
				TableMng::query(sprintf(
					'DELETE FROM MessageReceivers WHERE `messageId` = "%s";',
					$messageId));
				TableMng::query(sprintf(
					'DELETE FROM MessageManagers WHERE `messageId` = "%s";',
					$messageId));
				$db->autocommit(true);//automatically commits
			} catch (Exception $e) {
				$this->_interface->DieError('Die Nachricht konnte nicht gelöscht werden.' . $e->getMessage());
			}
		}
		else {
			$this->_interface->DieError(sprintf('Keine Zugriffsberechtigungen auf diese Nachricht (Message-ID: %s)', $messageId));
		}
		$this->_smarty->display($this->_smartyPath
			. 'delete_contract_fin.tpl');
	}

	/**
	 * Shows a specific message
	 */
	private function showMessage() {
		$db = TableMng::getDb();
		$messageId = $db->real_escape_string($_GET['ID']);
		if(($isManager = MessageFunctions::checkIsManagerOf($messageId,
			$_SESSION['uid']))
			|| (MessageFunctions::checkHasReceived($messageId,
				$_SESSION['uid']))) {

			$msgText = $msgTitle = $forename = $name = $grade = $msgRecId = '';
			$query = "SELECT m.title, m.text, mr.read, mr.ID,
					u.forename, u.name, CONCAT(g.gradeValue, g.label)
				FROM users u
				JOIN MessageReceivers mr ON mr.userId = u.ID
				JOIN Message m ON mr.messageId = m.ID AND m.ID = ?
				LEFT JOIN jointUsersInGrade uig ON u.ID = uig.UserID
				LEFT JOIN grade g ON g.ID = uig.GradeID
				WHERE u.ID = ?";
			$stmt = $db->prepare($query);
			if($stmt) {
				$stmt->bind_param('ii', $messageId, $_SESSION['uid']);
				$stmt->bind_result($msgTitle, $msgText, $isRead, $msgRecId,
					$forename, $name, $grade);
				$stmt->execute();
				while($stmt->fetch()) {
					// User got multiple messages of the same kind, select only
					// the last one
				}
				if($isRead == '0') {
					$this->markMsgAsRead($msgRecId);
				}
				$msgText = str_replace("{vorname}", $forename, $msgText);
				$msgText = str_replace("{name}", $name, $msgText);
				$this->createPdf($msgTitle, $msgText, $grade);
			}
			else {
				$this->_interface->DieError('Konnte die Nachrichtendaten nicht
					abrufen');
			}
		}
		else {
			$this->_interface->DieError ( 'Kein Zugriff erlaubt!');
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

	/**
	 * Searches for users with Ajax
	 */
	private function searchUserAjax() {
		$db = TableMng::getDb();
		$username = $db->real_escape_string($_GET['username']);
		$users = MessageFunctions::usersGetSimilarTo($username, 10);
		//output the findings
		foreach($users as $user) {
			echo sprintf(
				'<input type="button" onclick="addUser(\'%s\', \'%s\');"
					value="%s"><br />',
				$user['userId'], $user['userFullname'],
				$user['userFullname']);
		}
	}

	private function markMsgAsRead($msgReceiverId) {
		$db = TableMng::getDb();
		$query = sprintf(
			'UPDATE MessageReceivers SET `read` = "1" WHERE ID = "%s";',
			$db->real_escape_string($msgReceiverId));
		if($db->query($query)){
			return;
		}
		else {
			$this->_interface->DieError('Konnte die Nachricht nicht als gelesen markieren' . $db->error);
		}
	}

	/**
	 * Adds saved Copies to the Carbon-Footprint-Table
	 *
	 * @param int $count the Count of saved Copies to add
	 * @param int $authorId the author of the message that saved $count copies
	 */
	private function addSavedCopiesCount($count, $authorId) {
		$db  = TableMng::getDb();
		$count = $db->real_escape_string($count);
		$authorId = $db->real_escape_string($authorId);
		try {
			$authorEntryExists = TableMng::query(sprintf(
				"SELECT COUNT(*) AS count FROM MessageCarbonFootprint
				WHERE `authorId` = %s;
				", $authorId), true);
			if( ( (int) $authorEntryExists [0]['count']) > 0) {
				TableMng::query(sprintf(
					"UPDATE MessageCarbonFootprint
					SET `savedCopies` = `savedCopies` + %s
					WHERE `authorId` = %s
					", $count, $authorId));
			}
			else {
				$query = sprintf(
					"INSERT INTO MessageCarbonFootprint
						(`authorId`, `savedCopies`, `returnedCopies`)
					VALUES (%s, %s, 0);
					", $authorId, $count);
				TableMng::query($query);
			}
		} catch (Exception $e) {
			//not important, just echoing is enough
			echo "Konnte die CarbonFootprint-Daten nicht verarbeiten";
		}
	}

	private function showMessageAdmin() {
		require_once 'MessageShowAdmin.php';
		MessageShowAdmin::init($this->_smarty, $this->_smartyPath, $this->_interface);
		MessageShowAdmin::execute();
	}

	/**
	 * Creates a PDF for the Participation Confirmation and returns its Path
	 */
	private function createPdf ($title,$text,$class) {
		require_once  PATH_INCLUDE .('/pdf/tcpdf/config/lang/ger.php');
		require_once PATH_INCLUDE . '/pdf/tcpdf/tcpdf.php';

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('LeG Uelzen');
		$pdf->SetTitle($title);
		$pdf->SetSubject($title);
		$pdf->SetKeywords('');

		// set default header data
		$pdf->SetHeaderData('../../../../web/headmod_Messages/modules/mod_MessageMainMenu/logo.jpg', 15, 'LeG Uelzen', "Formulargenerator 0.1\nKlasse: ".$class, array(0,0,0), array(0,0,0));
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
		$pdf->SetFont('helvetica', '', 14, '', true);

		// Add a page
		// This method has several options, check the source code documentation for more information.
		$pdf->AddPage();

		// set text shadow effect
		$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

		// Set some content to print
		$html = '<p align="center"><h2>'. $title.'</h2></p><br>'.$text;

		// Print text using writeHTMLCell()
		$pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

		// ---------------------------------------------------------

		// Close and output PDF document
		// This method has several options, check the source code documentation
		// for more information.
		$pdf->Output('example_001.pdf', 'I');
	}

	///////////////////////////////////////////////////////////////////////
	//Attributes
	///////////////////////////////////////////////////////////////////////

	/**
	 * The path to the Smarty-templates of this module
	 */
	private $_smartyPath;

	/**
	 * An Smarty-Object, used to Output data
	 */
	private $_smarty;

	/**
	 * Stores the Interface of this Module
	 */
	private $_interface;

	/**
	 * Saves if the User is allowed to send Messages
	 */
	private $_isEditor;

}
?>