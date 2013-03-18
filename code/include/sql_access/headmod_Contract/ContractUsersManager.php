<?php

require_once PATH_ACCESS . '/TableManager.php';

class NachrichtenUsersManager extends TableManager {

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

	public function changeUserWithoutPassword ($ID, $forename, $name, $username, $email, $telephone) {

		parent::alterEntry($ID, 'forename', $forename, 'name', $name, 'username', $username, 'email', $email,
			'telephone', $telephone);
	}

	public function changeUserWithPassword ($ID, $forename, $name, $username, $email, $telephone, $password) {

		parent::alterEntry($ID, 'forename', $forename, 'name', $name, 'username', $username, 'email', $email,
			'telephone', $telephone, 'password', $password);
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

	private function cleanUserIdArray () {
		$this->_userIdArray = array();
	}

	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////
	private $_userIdArray;
}

?>