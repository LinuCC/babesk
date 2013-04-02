<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_WEB . '/headmod_Messages/MessageFunctions.php';

class MessageAdmin extends Module{
	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
		$this->_smartyPath = PATH_SMARTY . '/templates/web' . $path;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->init();

		if(isset($_GET['action'])) {
			switch($_GET['action']) {
				case 'newMessage':
					$this->newMessage();
					break;
				case 'newMessageForm':
					$this->newMessageForm();
					break;
				case 'showMessage':
					$this->showMessage();
					break;
				case 'addReceiverAjax':
					$this->addReceiverAjax();
					break;
				case 'addManagerAjax':
					$this->addManagerAjax();
					break;
				case 'deleteMessageAjax':
					$this->deleteMessageAjax();
					break;
				case 'removeReceiverAjax':
					$this->removeReceiverAjax();
					break;
				case 'removeManagerAjax':
					$this->removeManagerAjax();
					break;
				case 'fetchTemplateAjax':
					$this->fetchTemplateAjax();
					break;
				default:
					die('Wrong action-value given');
					break;
			}
		}
		else {
			die('No Access');
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function init() {
		global $smarty;
		$this->_smarty = $smarty;
		$this->_interface = new WebInterface($this->_smarty);
	}

	/**
	 * Saves a Message created by the user
	 */
	private function newMessage() {
		//INIT
		$db = TableMng::getDb();
		$msgTitle = $db->real_escape_string($_POST['messagetitle']);
		$msgText = $db->real_escape_string($_POST['messagetext']);
		$startDate = sprintf('%s-%s-%s',
			$db->real_escape_string($_POST['StartDateYear']),
			$db->real_escape_string($_POST['StartDateMonth']),
			$db->real_escape_string($_POST['StartDateDay']));
		$endDate = sprintf('%s-%s-%s',
			$db->real_escape_string($_POST['EndDateYear']),
			$db->real_escape_string($_POST['EndDateMonth']),
			$db->real_escape_string($_POST['EndDateDay']));
		$shouldReturn = isset($_POST['shouldReturn']) ?
			'shouldReturn' : 'noReturn';
		$shouldEmail = isset($_POST['shouldEmail']);
		$msgReceiverIds = array();
		try {
			//UPLOAD
			$db->autocommit(false);
			//Add Message itself
			TableMng::query(sprintf('INSERT INTO Message
					(originUserId,title,text,validFrom,validTo)
				VALUES (%s,"%s","%s","%s","%s")',
				$_SESSION['uid'], $msgTitle, $msgText, $startDate, $endDate));
			$messageId = $db->insert_id;
			$this->newMessageAddCreator($messageId);
			//Add receivers to the receiver-list
			$msgReceiverIds = array();
			if(isset($_POST['addMessageAddedUser']) && count($_POST['addMessageAddedUser'])) {
				$msgReceiverIds = array_merge($msgReceiverIds,
					$_POST['addMessageAddedUser']);
			}
			$userIdsOfGrades = $this->saveMessageGrades();
			if(count($userIdsOfGrades)) {
				$msgReceiverIds = array_merge($msgReceiverIds,
					$userIdsOfGrades);
			}
			$queryReceivers = 'INSERT INTO MessageReceivers
				(`messageId`, `userId`, `return`)
				VALUES (?, ?, ?)';
			$stmt = $db->prepare($queryReceivers);
			foreach ($msgReceiverIds as $rec) {
				$stmt->bind_param("iis", $messageId, $rec, $shouldReturn);
				$stmt->execute();
			}
			$db->autocommit(true);
		} catch (Exception $e) {
			$this->_interface->DieError('Konnte die Nachricht nicht
				hinzufügen!');
		}
		if($shouldEmail) {
			$recNotSendTo = $this->sendEmails($msgReceiverIds);
			$notSendStr = '';
			foreach($recNotSendTo as $receiver) {
				$notSendStr .= sprintf('Dem Benutzer %s konnte keine Email gesendet werden. (Email-Adresse: "%s")<br />',
					$receiver->forename . ' ' .$receiver->name,
					$receiver->email);
			}
		}
		else {
			$notSendStr = 'Es wurden keine Emails verschickt.';
		}
		$this->addSavedCopiesCount(count($msgReceiverIds), $_SESSION['uid']);
		$this->_smarty->assign('emailsNotSend', $notSendStr);
		$this->_smarty->display($this->_smartyPath . 'messageCreateFinished.tpl');
	}

	/**
	 * Adds the creator of the Message to the Managers
	 * @param  int $messageId
	 */
	private function newMessageAddCreator($messageId) {
		TableMng::query(sprintf(
			'INSERT INTO MessageManagers (messageId, userId)
			VALUES (%s, %s)', $messageId, $_SESSION['uid']));
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

	/**
	 * Sends notice-emails to the Receivers
	 * @param  array(MessageAdminReceivers) $receivers
	 * @return array(MessageAdminReceivers) the receivers were the email
	 * couldnt be send to
	 */
	private function sendEmails($receivers) {
		require_once PATH_INCLUDE . '/email/SMTPMailer.php';
		$usersNotSend = array();//the Email could not be send to these users
		$userdata = $this->sendEmailsFetchUserdata($receivers);
		$mailer = new SMTPMailer($this->_interface);
		$mailer->smtpDataInDatabaseLoad();
		$mailer->emailFromXmlLoad(PATH_INCLUDE .
			'/email/Babesk_Nachricht_Info.xml');
		foreach($userdata as $user) {
			$mailer->AddAddress($user->email);
			if($user->email != '' && $mailer->Send()) {
				//everything fine
			}
			else {
				$usersNotSend[] = $user;
			}
			$mailer->ClearAddresses();
		}
		return $usersNotSend;
	}

	private function sendEmailsFetchUserdata($receivers) {
		$userData = array();
		$forename = $name = $username = $birthday = $email = $telephone = '';
		$stmt = TableMng::getDb()->prepare(
			'SELECT `forename`, `name`, `username`, `birthday`, `email`,
				`telephone` FROM users WHERE `ID` = ?');
		$stmt->bind_result($forename, $name, $username, $birthday, $email,
			$telephone);
		foreach($receivers as $recId) {
			$stmt->bind_param('i', $recId);
			$stmt->execute();
			while($stmt->fetch()) {
				$userData[] = new MessageAdminUser($recId, $forename, $name,
					$username, $birthday, $email, $telephone);
			}
		}
		return $userData;
	}

	/**
	 * Shows a form to the User in which he can create a new Message
	 *
	 * @fixme grades do not get sorted out by schoolyear
	 */
	private function newMessageForm() {

		$grades = $templates = array();

		try {
			$grades = TableMng::query(
				'SELECT CONCAT(gradeValue, label) AS name, ID
				FROM grade', true);
			$templates = TableMng::query(
				'SELECT * FROM MessageTemplate;', true);

		} catch (MySQLVoidDataException $e) {
			//gets sorted out with Smarty in the tpl-File

		} catch (Exception $e) {
			$this->_interface->DieError('Konnte die nötigen Daten nicht abrufen.');
		}

		$this->_smarty->assign('grades', $grades);
		$this->_smarty->assign('templates', $templates);
		$this->_smarty->display($this->_smartyPath . 'newMessage.tpl');
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
	 * Shows the Message-Data to the User allowing administrative things
	 * @return [type] [description]
	 */
	protected function showMessage() {
		$messageId = $_GET['ID'];
		$userId = $_SESSION['uid'];
		if(MessageFunctions::checkIsManagerOf($messageId, $userId)){
			$receivers = $this->getReceiverOfMessage($messageId);
			$managers = $this->getManagerOfMessage($messageId);
			$messageData = $this->getMessage($messageId);
			//format dateformat ISO 8601 into european-friendly Date
			$messageData['validTo'] = formatDate($messageData['validTo']);
			$messageData['validFrom'] = formatDate($messageData['validFrom']);
			$this->smartyAssignIsCreator($userId,
				$messageData ['originUserId']);
			$this->_smarty->assign('receivers', $receivers);
			$this->_smarty->assign('managers', $managers);
			$this->_smarty->assign('messageData', $messageData);
			$this->_smarty->display($this->_smartyPath . '/showMessage.tpl');
		}
		else {
			$this->_interface->DieError('Keine Berechtigung, um diese Nachricht als Manager einzusehen oder die Nachricht existiert nicht.');
		}
	}

	/**
	 * Assigns a Var to Smarty based on the check if the user is the creator of
	 * the message or not
	 */
	protected function smartyAssignIsCreator($userId, $origUserId) {
		if($userId == $origUserId) {
			$this->_smarty->assign('isCreator', true);
		}
		else {
			$this->_smarty->assign('isCreator', false);
		}
	}

	/**
	 * Fetches the Data of a Message from the Database and returns them
	 * @param  int $id the ID of the message to fetch
	 * @return array() the data of the message as an Array SQL-Style
	 */
	protected function getMessage($id) {
		$id = TableMng::getDb()->real_escape_string($id);
		try {
			$data = TableMng::query(sprintf(
				'SELECT * FROM Message WHERE `ID` = %s;
				', $id), true);
		} catch (Exception $e) {
			$this->_interface->DieError('Konnte die Nachricht nicht abrufen!');
		}
		return $data[0];
	}

	/**
	 * Fetches the Managers of the Message and returns them
	 * @param  int $id The Id of the Mrssage
	 * @return MessageAdminManager The Manager of the Message
	 */
	protected function getManagerOfMessage($id) {
		$managers = array();//contains the MessageAdminManager-Objects
		$managerArray = array();//saves the Array returned by MySQL
		$userForename = $userName = '';
		$id = TableMng::getDb()->real_escape_string($id);
		//get Ids of Managers
		try {
			$managerArray = TableMng::query(sprintf(
				'SELECT *
				FROM MessageManagers
				WHERE messageId = %s', $id), true);
		} catch (MySQLVoidDataException $e) {
			return array();
		} catch (Exception $e) {
			echo 'Konnte die Manager nicht abrufen';
		}
		//get data of manager
		$stmt = TableMng::getDb()->prepare(
			'SELECT forename, name
			FROM users u
			WHERE u.ID = ?');
		foreach ($managerArray as $mng) {
			$stmt->bind_param('i', $mng ['userId']);
			$stmt->bind_result($userForename, $userName);
			if($stmt->execute()) {
				$stmt->fetch();
				$managers [] = new MessageAdminManager($mng ['userId'],
					$userForename, $userName);
			}
			else {
				echo sprintf('Konnte den Manager mit der ID %s nicht laden.',
					$mng ['userId']);
			}
		}
		return $managers;
	}

	/**
	 * Fetches the Receivers of the Message and returns them
	 * @param  int $id The Id of the Message
	 * @return MessageAdminReceiver The Receiver of the Message
	 */
	protected function getReceiverOfMessage($id) {
		$receivers = array();//Contains the Objects MessageAdminReceiver
		$receiverArray = array();//Contains the Arrays coming from MySQL
		$userForename = $userName = '';
		$id = TableMng::getDb()->real_escape_string($id);
		//get IDs of the Receivers
		try {
			$receiverArray = TableMng::query(sprintf(
				'SELECT userId, `read`, `return` FROM MessageReceivers
				WHERE messageId = %s;', $id), true);
		} catch (MySQLVoidDataException $e) {
			return array();
		} catch (Exception $e) {
			echo 'konnte die Empfänger nicht abrufen' . $e->getMessage();
		}
		//get the data of the receivers
		$stmt = TableMng::getDb()->prepare(
			'SELECT u.forename, u.name
			FROM users u
			WHERE u.ID = ?;
			');
		foreach($receiverArray as $receiver) {
			$stmt->bind_param('i', $receiver ['userId']);
			$stmt->bind_result($userForename, $userName);
			if($stmt->execute()) {
				$stmt->fetch();
				$receivers [] = new MessageAdminReceiver($receiver['userId'],
					$userForename, $userName, $receiver['read'],
					$receiver['return']);
			}
			else {
				echo sprintf('Konnte den Empfänger mit der ID "%s" nicht laden', $receiver ['userId']);
			}
		}
		return $receivers;
	}

	protected function addReceiverAjax() {
		$messageId = TableMng::getDb()->real_escape_string($_POST['messageId']);
		$userId = TableMng::getDb()->real_escape_string($_POST['userId']);
		if(MessageFunctions::checkIsManagerOf($messageId, $_SESSION['uid'])) {
			try {
				TableMng::query(sprintf(
					'INSERT INTO MessageReceivers (messageId, userId)
					VALUES (%s, %s)
					', $messageId, $userId));
			} catch (Exception $e) {
				echo('Could not add the Receiver');
			}
		}
		else {
			echo 'No Manager!';
		}

	}

	protected function addManagerAjax() {
		$messageId = TableMng::getDb()->real_escape_string($_POST['messageId']);
		$userId = TableMng::getDb()->real_escape_string($_POST['userId']);
		if(MessageFunctions::checkIsManagerOf($messageId, $_SESSION['uid'])) {
			try {
				var_dump(sprintf(
					'INSERT INTO MessageManagers (messageId, userId)
					VALUES (%s, %s)
					', $messageId, $userId));
				TableMng::query(sprintf(
					'INSERT INTO MessageManagers (messageId, userId)
					VALUES (%s, %s)
					', $messageId, $userId));
			} catch (Exception $e) {
				echo('Could not add teh Receiver');
			}
		}
		else {
			echo 'No Manager!';
		}
	}

	protected function deleteMessageAjax() {
		$messageId = TableMng::getDb()->real_escape_string($_POST['messageId']);
		if(MessageFunctions::checkIsCreatorOf($messageId, $_SESSION['uid'])) {
			try {
				MessageFunctions::deleteMessage($messageId);
			} catch (Exception $e) {
				die('error');
			}
		}
		else {
			die('No Owner!');
		}
	}

	protected function removeReceiverAjax() {
		$messageId =
			TableMng::getDb()->real_escape_string($_POST['messageId']);
		$receiverId =
			TableMng::getDb()->real_escape_string($_POST['receiverId']);
		if(MessageFunctions::checkIsManagerOf($messageId, $_SESSION['uid'])) {
			try {
				MessageFunctions::removeReceiver($messageId, $receiverId);
			} catch (Exception $e) {
				die('error' . $e->getMessage());
			}
		}
		else {
			die('No Manager!');
		}
	}

	protected function removeManagerAjax() {
		$messageId =
			TableMng::getDb()->real_escape_string($_POST['messageId']);
		$managerId =
			TableMng::getDb()->real_escape_string($_POST['managerId']);
		if(MessageFunctions::checkIsManagerOf($messageId, $_SESSION['uid'])) {
			if($_SESSION['uid'] != $managerId) {
				try {
					MessageFunctions::removeManager($messageId, $managerId);
				} catch (Exception $e) {
					die('error');
				}
			}
			else {
				//The Manager wants to remove himself, say nope in Javascript
				die('errorSelf');
			}
		}
		else {
			die('No Manager!');
		}
	}

	protected function fetchTemplateAjax() {

		$templateId =
			TableMng::getDb()->real_escape_string($_POST['templateId']);

		try {
			$template = TableMng::query(sprintf(
				'SELECT * FROM MessageTemplate WHERE `ID` = "%s"',
				$templateId), true);

		} catch (Exception $e) {
			die('errorFetchTemplate');
		}

		die(json_encode($template[0]));
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_smarty;

	protected $_interface;

	protected $_smartyPath;

}

/**
 * Data-Object used in MessageAdmin
 */
class MessageAdminReceiver {

	public function __construct($id, $forename, $name, $readMessage,
		$returnedMessage) {
		$this->id = $id;
		$this->readMessage = $readMessage;
		$this->returnedMessage = $returnedMessage;
		$this->forename = $forename;
		$this->name = $name;
	}

	public $id;
	public $readMessage;
	public $returnedMessage;
	public $forename;
	public $name;
}

/**
 * Data-Object used in MessageAdmin
 */
class MessageAdminManager {

	public function __construct($id, $forename, $name) {
		$this->id = $id;
		$this->forename = $forename;
		$this->name = $name;
	}

	public $id;
	public $forename;
	public $name;
}

/**
 * Data-Object used in MessageAdmin
 */
class MessageAdminUser {

	public function __construct($id, $forename, $name, $username, $birthday, $email, $telephone) {
		$this->id = $id;
		$this->forename = $forename;
		$this->name = $name;
		$this->username = $username;
		$this->birthday = $birthday;
		$this->telephone = $telephone;
		$this->email = $email;
	}

	public $id;
	public $forename;
	public $name;
	public $username;
	public $birthday;
	public $telephone;
	public $email;
}

?>