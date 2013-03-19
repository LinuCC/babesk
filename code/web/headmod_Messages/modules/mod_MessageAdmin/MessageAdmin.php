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
				case 'showMessage':
					$this->showMessage();
					break;
				default:
					die('Wrong action-value given');
					break;
			}
		}
		else {

		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function init() {
		global $smarty;
		$this->_smarty = $smarty;
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
			$this->smartyAssignIsCreator($userId,
				$messageData ['originUserId']);
			$this->_smarty->assign('receivers', $receivers);
			$this->_smarty->assign('managers', $managers);
			$this->_smarty->assign('messageData', $messageData);
			$this->_smarty->display($this->_smartyPath . '/showMessage.tpl');
		}
		else {
			$this->_interface->DieError('Keine Berechtigung, um diese Nachricht als Manager einzusehen.');
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
				WHERE userId = %s AND messageId = %s',
				$_SESSION['uid'], $id), true);
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

?>