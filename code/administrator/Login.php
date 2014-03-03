<?php

class Login {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($smarty, $pdo, $logger) {

		$this->_smarty = $smarty;
		$this->_pdo = $pdo;
		$this->_logger = $logger;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function loggedInUserGet() {

		return $this->_user;
	}

	public function loginCheck() {

		$this->logoutCheck();

		if($this->isLoggedIn()) {
			return true;
		}
		else {
			if($ret = $this->loginDataProcess()) {
				$this->login();
			}
			return $ret;
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function loginShow($msg = '') {

		$this->_smarty->assign('status', $msg);
		$this->_smarty->display('administrator/login.tpl');
		die();
	}

	protected function logout() {

		$this->_isLoggedIn = false;
		session_destroy();
		$this->loginShow('Sie wurden erfolgreich ausgeloggt');
	}

	protected function login() {

		$_SESSION['UID'] = $this->_user['ID'];
		$_SESSION['username'] = $this->_user['username'];
	}

	protected function isLoggedIn() {

		return isset($_SESSION['UID']);
	}

	protected function isLogoutDesired() {

		return (isset($_GET['action']) && $_GET['action'] == 'logout');
	}

	protected function logoutCheck() {

		if($this->isLogoutDesired()) {
			$this->logout();
			die();
		}
	}

	protected function loginDataProcess() {

		if($this->loginDataCheckExistence() &&
			$this->loginDataVerify()) {
			return true;
		}
		else {
			$this->loginShow('Login-Daten sind nicht richtig');
		}
	}

	protected function loginDataCheckExistence() {

		if('POST' == $_SERVER['REQUEST_METHOD']) {
			$this->emptyLoginCheckDieOnError();
			return true;
		}
		else {
			$this->loginShow();
		}
	}

	protected function loginDataVerify() {

		$this->usernameNotDuplicatedCheck($_POST['Username']);
		$userData = $this->userDataFetch($_POST['Username']);

		if(empty($userData)) {
			$this->loginShow('Der Benutzer wurde nicht gefunden.');
		}
		else {
			if(!$this->passwordCheck(
					$userData['ID'], $userData['password'], $_POST['Password']
			)) {
				$this->loginShow(_g('User not found or incorrect password'));
			}
		}

		$this->_user = $userData;
		return true;
	}


	protected function emptyLoginCheckDieOnError() {

		if(!isset($_POST['Username'], $_POST['Password'])) {
			$this->loginShow('Please Log in');
		}

		if(trim($_POST['Username']) == '' ||
			trim($_POST['Password']) == '') {
			$this->loginShow(_g('Please fill out the form completely.'));
		}
	}

	/**
	 * Fetches and returns the data of the user with username $username
	 * @param  string $username The username of the user
	 * @return array            An array of data
	 * ['ID' => '<Id of user>', 'password' => '<hashed password of user>',
	 *  'username' => 'username']
	 */
	protected function userDataFetch($username) {

		try {
			$stmt = $this->_pdo->prepare(
				'SELECT ID, password, username FROM users
					WHERE username LIKE ?'
			);
			$stmt->execute(array($username));
			return $stmt->fetch();

		} catch (PDOException $e) {
			$this->loginShow('Error executing Query');
		}
	}

	/**
	 * Checks if a username exists multiple times in the users-table
	 * Dies displaying a message if username exists multiple times
	 * @param  string $username The username to check for
	 */
	protected function usernameNotDuplicatedCheck($username) {

		if($this->usernameCountGet($username) > 1) {
			$this->_logger->log('multiple users with same username found!',
				'Problematic', Null, json_encode(array(
					'username' => $username
			)));
			$this->loginShow('Error: multiple fitting users found');
		}
	}

	/**
	 * Checks how often the given username exists
	 * @return int    the count of the users having this username
	 */
	protected function usernameCountGet($username) {

		try {
			$stmt = $this->_pdo->prepare(
				'SELECT COUNT(*) FROM users WHERE username = ?'
			);
			$stmt->execute(array($username));
			return $stmt->fetchColumn();

		} catch (PDOException $e) {
			$this->_logger->log('Could not fetch the username-Count',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
		}
	}

	/**
	 * Checks if the password is the correct one for the user
	 * Also checks if the hashed password is hashed in the old md5-way and
	 * converts it if necessary
	 * @param  int    $userId          The Id of the user
	 * @param  string $password        The hashed password
	 * @param  string $passwordToCheck The password given by the user to check
	 * @return bool                    true if it is the correct password,
	 *                                 else false
	 */
	protected function passwordCheck($userId, $password, $passwordToCheck) {

		if (strlen(trim($password)) == 32 &&
			md5($passwordToCheck) == $password
		) {
			//Convert old-style md5-hashed password to new hash-method
			$this->passwordOfUserUpdate($userId, $passwordToCheck);
			return true;
		}
		else {
			return validate_password($passwordToCheck, $password);
		}
	}

	/**
	 * Updates the users password
	 * Dies displaying a message on error
	 * @param  int    $userId   The Id of the user to change the password
	 * @param  string $password The password (not hashed)
	 */
	protected function passwordOfUserUpdate($userId, $password) {

		$newHash = hash_password($password);

		try {
			$stmt = $this->_pdo->prepare(
				'UPDATE users SET password = ? WHERE ID = ?'
			);
			$stmt->execute(array($newHash, $userId));

		} catch (PDOException $e) {
			$this->loginShow('Error executing Query');
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected  $_isLoggedIn;

	protected $_smarty;

	protected $_user;

	protected $_pdo;

	protected $_logger;
}

?>
