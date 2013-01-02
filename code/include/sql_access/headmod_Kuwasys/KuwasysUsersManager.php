<?php

require_once PATH_ACCESS . '/TableManager.php';

class KuwasysUsersManager extends TableManager {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct ($interface = NULL) {
		parent::__construct('users');
		$this->_userIdArray = array ();
	}

	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	public function addUser ($forename, $name, $username, $password, $email, $telephone, $birthday) {

		parent::addEntry('forename', $forename, 'name', $name, 'username', $username, 'password', $password, 'email',
			$email, 'telephone', $telephone, 'birthday', $birthday);
	}

	/**
	 * Adds multiple Users
	 * @param $rows DbAMRow-Array
	 */
	public function addMultipleUser ($rows) {
		$mQMng = $this->getMultiQueryManager ();
		foreach ($rows as $row) {
			$mQMng->rowAdd ($row);
		}
		$mQMng->dbExecute ($mQMng::$Insert);
	}

	public function deleteUser ($ID) {

		parent::delEntry($ID);
	}

	public function getUserByID ($ID) {

		$userData = parent::searchEntry(sprintf('ID = "%s"', $ID));
		return $userData;
	}

	public function getAllUsers () {

		$users = $this->getTableData();
		return $users;
	}

	public function changeUserWithoutPassword ($ID, $forename, $name, $username, $email, $telephone, $isFirstPassword) {

		parent::alterEntry($ID, 'forename', $forename, 'name', $name, 'username', $username, 'email', $email,
			'telephone', $telephone, 'first_passwd', $isFirstPassword);
	}

	public function changeUserWithPassword ($ID, $forename, $name, $username, $email, $telephone, $password, $isFirstPassword) {
		parent::alterEntry($ID, 'forename', $forename, 'name', $name, 'username', $username, 'email', $email,
			'telephone', $telephone, 'password', $password, 'first_passwd', $isFirstPassword);
	}

	public function addUserIdToUserIdArray ($userId) {

		$this->_userIdArray [] = $userId;
	}

	public function getUsersByUserIdArray () {

		$users = $this->getMultipleEntriesByArray('ID', $this->_userIdArray);
		$this->cleanUserIdArray ();
		return $users;
	}

	public function changePasswordOfUserIdArray ($password) {
		$idStr = '';
		foreach ($this->_userIdArray as $userId) {
			$idStr .= sprintf('%s,', $userId);
		}
		$idStr = rtrim ($idStr, ',');
		$query = sql_prev_inj(sprintf('UPDATE %s SET password = "%s" WHERE ID IN(%s)', $this->tablename, $password, $idStr));
		$this->executeQuery ($query);
		$this->cleanUserIdArray ();
	}

	public function changeEmailAdress ($userId, $email) {
		$this->alterEntry ($userId, 'email', $email);
	}

	/**
	 * Returns the ID that the next added tablerow would get
	 */
	public function getNextAIUserId () {
		return $this->getNextAutoIncrementID ();
	}

	private function cleanUserIdArray () {
		$this->_userIdArray = array();
	}

	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////
	private $_userIdArray;
}

?>