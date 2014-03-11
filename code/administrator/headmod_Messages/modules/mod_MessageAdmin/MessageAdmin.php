<?php

require_once PATH_INCLUDE . '/Module.php';
require_once 'MessageAdminInterface.php';
require_once PATH_ADMIN . '/headmod_Messages/Messages.php';

class MessageAdmin extends Messages {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct ($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);

		if(isset($_GET['action'])) {
			switch($_GET['action']) {
				case 'deleteMessage':
					$this->messageDelete();
					break;
				default:
					die('Wrong action-value');
					break;
			}
		}
		else {
			$this->mainMenu();
		}
	}


	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		defined('_AEXEC') or die('Access denied');

		$this->_dataContainer = $dataContainer;
		$this->_interface = new MessageAdminInterface($this->relPath,
			$this->_dataContainer->getSmarty());
	}

	protected function mainMenu() {

		$messages = $this->messageFetchAll();
		$this->messagesAddReceiverCount($messages);
		$creatorsWithMessages = $this->messagesSortToCreators($messages);
		$this->creatorsAddName($creatorsWithMessages);

		$this->_interface->mainMenu($creatorsWithMessages);
	}

	/**
	 * Fetches all Messages in the Database
	 *
	 * @return array() an array of Messages, each represented by an array
	 * or a void array if nothing could be fetched / an error occured
	 */
	protected function messageFetchAll() {

		try {
			$data = TableMng::query('SELECT * FROM MessageMessages');

		} catch (MySQLVoidDataException $e) {
			return array();

		} catch (Exception $e) {
			$this->_interface->showError('Konnte die Nachrichten nicht
				abrufen');
			return array();
		}

		return $data;
	}

	/**
	 * Creates an Array of MessageAdminCreator based on the messages given
	 * @param  array(array()) $messages
	 * @return array(MessageAdminCreator)
	 */
	protected function messagesSortToCreators($messages) {

		$creators = array();

		foreach($messages as $message) {
			if($this->creatorsInitByMessages($creators, $message)) {
				//creatorsInitByMessages already added the message to
				//the creator using reference
			}
			else {
				//Create a new MessageCreator
				$creators[] = new MessageAdminCreator(
					$message['originUserId'], '', array($message));
			}
		}

		return $creators;
	}

	/**
	 * Adds the given message to the the creator with the same id as the messages originUserId
	 * @param  array(MessageAdminCreator) $creators
	 * @param  array() $message
	 * @return bool true if the creator with the same id was found, false if
	 * not
	 */
	protected function creatorsInitByMessages(&$creators, $message) {

		foreach($creators as &$creator) {

			if($message['originUserId'] == $creator->id) {
				$creator->addMessage($message);
				return true;
			}
		}

		return false;
	}

	/**
	 * Fetches the names of the creators and adds them to the creator-objects
	 *
	 * @param  MessageAdminCreator $creators Is given as reference
	 */
	protected function creatorsAddName(&$creators) {

		$name = '';

		$stmt = TableMng::getDb()->prepare(
			'SELECT CONCAT(forename, " ", name) FROM users WHERE `ID` = ?;');
		$stmt->bind_result($name);

		foreach($creators as $creator) {
			$id = TableMng::getDb()->real_escape_string($creator->id);

			$stmt->bind_param('i', $id);
			$stmt->execute();
			$stmt->fetch();

			$creator->name = $name;
		}
	}

	/**
	 * Adds the Count of receivers of each message of the given Message-Array
	 *
	 * Each message gets the new Key "receiverCount". It states to how many
	 * users the message got send.
	 *
	 * @param  array(array()) $messages Passed as reference
	 */
	protected function messagesAddReceiverCount(&$messages) {

		$count = '';

		$stmt = TableMng::getDb()->prepare(
			'SELECT COUNT(*) FROM MessageReceivers WHERE `messageId` = ?;');
		$stmt->bind_result($count);

		foreach($messages as &$message) {
			$id = TableMng::getDb()->real_escape_string($message['ID']);

			$stmt->bind_param('i', $id);
			$stmt->execute();
			$stmt->fetch();

			$message['receiverCount'] = $count;
		}
	}

	/**
	 * Deletes the message the user picked
	 */
	protected function messageDelete() {

		if(isset($_GET['id'])) {
			$id = TableMng::getDb()->real_escape_string($_GET['id']);
			$this->messageDeleteFromDb($id);
			$this->_interface->dieMsg('Die Nachricht wurde erfolgreich gelöscht');
		}
		else {
			$this->_interface->dieError('Die ID wurde nicht angegeben!');
		}
	}

	/**
	 * Removes a Message from the Database by the ID given
	 *
	 * It also removes the links to this Message in the Tables
	 * MessageReceivers and MessageManagers.
	 *
	 * @param  int $id the ID of the message to remove
	 */
	protected function messageDeleteFromDb($id) {

		try {
			TableMng::getDb()->autocommit(false);
			$query = sprintf(
				'DELETE FROM MessageMessages WHERE `ID` = %s;
				DELETE FROM MessageReceivers WHERE `messageId` = %s;
				DELETE FROM MessageManagers WHERE `messageId` = %s;',
				$id, $id, $id);
			TableMng::queryMultiple($query);
			TableMng::getDb()->autocommit(true);

		} catch (Exception $e) {
			$this->_interface->dieError('Konnte die Nachricht nicht löschen!');
		}
	}


	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_dataContainer;

	protected $_interface;

}

/**
 * Data-Object used by MessageAdmin
 */
class MessageAdminCreator {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($id, $name, $messages) {

		$this->id = $id;
		$this->name = $name;
		$this->messages = $messages;
	}

	public function addMessage($message) {

		$this->messages[] = $message;
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	public $id;
	public $name;
	public $messages; //Messages created by this user
}

?>
