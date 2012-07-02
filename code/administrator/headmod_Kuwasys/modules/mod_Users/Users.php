<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_INCLUDE . '/functions.php';
require_once 'UsersInterface.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysUsersManager.php';

/**
 * Main-Class for the Module Users
 * allows adding, changing, showing and deleting Users
 * @author Pascal Ernst <pascal.cc.ernst@googlemail.com>
 *
 */
class Users extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	private $_interface;
	private $_usersManager;
	/**
	 * @var KuwasysLanguageManager
	 */
	private $_languageManager;

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct ($name, $display_name, $path) {

		parent::__construct($name, $display_name, $path);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute ($dataContainer) {

		$this->entryPoint($dataContainer);

		if (isset($_GET['action'])) {
			switch ($_GET['action']) {
				case 'addUser':
					$this->addUser();
					break;
				case 'showUsers':
					$this->showUsers();
					break;
				case 'deleteUser':
					$this->deleteUser();
					break;
				default:
					$this->_interface->dieError($this->_languageManager->getText('actionValueWrong'));
			}

		}
		else {
			$this->showMainMenu();
		}

	}

	////////////////////////////////////////////////////////////////////////////////
	//Implements
	private function entryPoint ($dataContainer) {

		defined('_AEXEC') or die('Access denied');
		$this->_usersManager = new KuwasysUsersManager();
		$this->_interface = new UsersInterface($this->relPath, $dataContainer->getSmarty());
		$this->_languageManager = $dataContainer->getLanguageManager();
		$this->_languageManager->setModule('Users');
	}

	private function showMainMenu () {

		$this->_interface->showMainMenu();
	}

	private function showAddUser () {

		$this->_interface->showAddUser();
	}

	/**
	 * adds a User to the MySQL-table
	 */
	private function addUser () {

		if (isset($_POST['username'], $_POST['name'], $_POST['forename'], $_POST['telephone'])) {
			$this->checkAddUserInput();
			$this->checkPasswordRepetition();
			$this->addUserToDatabase();
			$this->_interface->dieMsg(sprintf($this->_languageManager->getText('finishedAddUser'), $_POST['forename'],
				$_POST['name']));
		}
		else {
			$this->showAddUser();
		}
	}

	/**
	 * @used-by Users::addUser
	 */
	private function checkAddUserInput () {

		try {
			inputcheck($_POST['forename'], 'name', $this->_languageManager->getText('formForename'));
			inputcheck($_POST['name'], 'name', $this->_languageManager->getText('formName'));
			inputcheck($_POST['username'], 'name', $this->_languageManager->getText('formUsername'));
			inputcheck($_POST['password'], 'password', $this->_languageManager->getText('formPassword'));
			inputcheck($_POST['passwordRepeat'], 'password', $this->_languageManager->getText('formPasswordRepeat'));
			inputcheck($_POST['email'], 'email', $this->_languageManager->getText('formEmail'));
			inputcheck($_POST['telephone'], 'number', $this->_languageManager->getText('formTelephone'));
		} catch (WrongInputException $e) {
			$this->_interface->dieError(sprintf($this->_languageManager->getText('formWrongInput'), $e->getFieldName()));
		}
	}

	/**
	 * @used-by Users::addUser
	 */
	private function checkPasswordRepetition () {

		if ($_POST['password'] == $_POST['passwordRepeat']) {
			return true;
		}
		else {
			$this->_interface->dieError($this->_languageManager->getText('formWrongPasswordRepetition'));
		}
	}

	/**
	 * @used-by Users::addUser
	 */
	private function addUserToDatabase () {

		$date = $this->convertNumbersToDate($_POST['Date_Day'], $_POST['Date_Month'], $_POST['Date_Year']);
		try {
			$this->_usersManager->addUser($_POST['forename'], $_POST['name'], $_POST['username'], hash_password($_POST[
				'password']), $_POST['email'], $_POST['telephone'], $date);
		} catch (MySQLConnectionException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorAddUserConnectDatabase'));
		}
		catch (Exception $e) {
			$this->_interface->dieError(sprintf($this->_languageManager->getText('errorAddUser'), $e->getMessage()));
		}
	}

	private function showUsers () {

		$users = $this->getAllUsers();
		$this->_interface->showAllUsers($users);
	}

	private function getAllUsers () {

		try {
			$users = $this->_usersManager->getTableData();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoUsers'));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchAllUsers'));
		}
		return $users;
	}
	
	/**
	 * @used-by Users::addUserToDatabase
	 * @param int(2) $day
	 * @param int(2) $month
	 * @param int(4) $year
	 */
	private function convertNumbersToDate ($day, $month, $year) {

		$date = sprintf('%s.%s.%s', $day, $month, $year);
		return $date;
	}

	private function deleteUser () {

		if (isset($_POST['dialogConfirmed'])) {
			$this->deleteUserFromDatabase();
		}
		else if (isset($_POST['dialogNotConfirmed'])) {
			$this->_interface->dieMsg($this->_languageManager->getText('deleteUserNotComfirmed'));
		}
		else {
			$this->showDeleteUserConfirmation();
		}
	}

	private function showDeleteUserConfirmation () {

		$userData = $this->getUserData($_GET['ID']);
		$userForename = $userData['forename'];
		$userName = $userData['name'];
		$this->_interface->showDeleteUserConfirmation($_GET['ID'], $userForename, $userName, $this->_languageManager);
	}

	/**
	 * @used-by Users::showDeleteUserConfirmation
	 * @param unknown_type $ID
	 */
	private function getUserData ($ID) {

		try {
			$userData = $this->_usersManager->getUserByID($ID);
		} catch (MySQLConnectionException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorConnectDatabase'));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorGetUser'));
		}
		return $userData;
	}

	/**
	 * @used-by Users::deleteUser
	 */
	private function deleteUserFromDatabase () {

		try {
			$this->_usersManager->deleteUser($_GET['ID']);
		} catch (Exception $e) {
			$this->_interface->dieError(sprintf($this->_languageManager->getText('errorDeleteUser'), $e->getMessage()));
		}
		$this->_interface->dieMsg($this->_languageManager->getText('finDeleteUser'));
	}

}
?>