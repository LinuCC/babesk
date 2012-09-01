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
		$this->_userToFetchArray = array ();
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
		
		$this->_userToFetchArray [] = $userId;
	}
	
	public function getUsersByUserIdArray () {
	
		$users = $this->getMultipleEntriesByArray('ID', $this->_userToFetchArray);
		return $users;
	}
	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////
	private $_userToFetchArray;
}

?>