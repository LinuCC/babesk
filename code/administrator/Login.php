<?php

class Login {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($smarty) {

		$this->_smarty = $smarty;
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

		$user = $this->userFetch();
		$this->userCheckDieOnError($user);

		$this->_user = $user[0];
		return true;
	}


	protected function emptyLoginCheckDieOnError() {

		if(!isset($_POST['Username'], $_POST['Password'])) {
			$this->loginShow(INVALID_FORM);
		}

		if(trim($_POST['Username']) == '' ||
			trim($_POST['Password']) == '') {
			$this->loginShow(EMPTY_FORM);
		}
	}

	protected function userFetch() {

		TableMng::sqlEscape($_POST['Username']);
		$password = hash_password($_POST['Password']);

		try {
			$users = TableMng::query("SELECT ID, username FROM users
				WHERE `username` = '$_POST[Username]'
					AND `password` = '$password'", true);

		} catch (Exception $e) {
			$this->loginShow('Error executing Query');
		}

		return $users;
	}

	protected function userCheckDieOnError($users) {

		if(empty($users)) {
			$this->loginShow(INVALID_LOGIN);
		}
		else if(count($users) > 1) {
			$this->loginShow('Error: multiple fitting users found');
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected  $_isLoggedIn;

	protected $_smarty;

	protected $_user;
}

?>
