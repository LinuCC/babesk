<?php

require_once PATH_INCLUDE . '/constants.php';

/**
 * Handles the Login for the Web-program
 * @author Pascal Ernst <pascal.cc.ernst@googlemail.com>
 */
class Login {
	//////////
	//////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct ($smarty) {
		$this->_smarty = $smarty;
		$this->setUpUserManager();
		$this->setUpGlobalSettingsManager ();
	}
	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////

	/**
	 * login-function
	 * handles the login. It shows the login-form, then checks the input and, if successful,
	 * it returns the ID of the User.
	 * @param string $username
	 * @param string $formpass
	 * @return true if successfuly logged in
	 */
	public function login () {

		if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['login'], $_POST['password'])) {
			$this->entryPoint();
			return $this->checkLogin ();
		}
		else {
			$this->dieShowLoginForm ();
		}
	}
	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////

	private function entryPoint () {

		defined('_WEXEC') or die("Access denied");
		$this->_username = $_POST['login'];
		$this->_password = $_POST['password'];
	}

	private function checkLogin () {

		$this->easterEggLeg();
		$this->checkLoginInput();
		$this->setUserIdByUsername();
		$this->checkPassword();
		$this->checkLockedAccount();
		$this->finishSuccessfulLogin();
		return true;
	}

	private function setUpUserManager () {

		require_once PATH_ACCESS . '/UserManager.php';
		$this->_userManager = new UserManager();
	}

	private function setUpGlobalSettingsManager () {
		require_once PATH_ACCESS . '/GlobalSettingsManager.php';
		$this->_globalSettingsManager = new GlobalSettingsManager ();
	}

	private function setUserIdByUsername () {

		try {
			$this->_userId = $this->_userManager->getUserID($this->_username);
		} catch (MySQLVoidDataException $e) {
			$this->assignErrorToSmarty(INVALID_LOGIN);
			$this->dieShowLoginForm();
		} catch (Exception $e) {
			$this->assignErrorToSmarty('ERROR:' . $e->getMessage());
			$this->dieShowLoginForm();
		}
	}

	private function checkLoginInput () {

		try {
			inputcheck($this->_username, 'name', 'Benutzername');
			inputcheck($this->_password, 'password', 'Passwort');
		} catch (WrongInputException $e) {
			$this->assignErrorToSmarty(sprintf('%s in %s', INVALID_CHARS, $e->getFieldName()));
			$this->dieShowLoginForm();
		}
	}

	private function dieShowLoginForm () {
		if ($this->isWebloginHelptext ()) {
			$this->_smarty->assign ('showLoginButton', true);
		}
		else {
			$this->_smarty->assign ('showLoginButton', false);
		}
		$this->_smarty->display('web/login.tpl');
		die();
	}

	/**
	 * Checks if the global Setting GlobalSettings::WEBLOGIN_HELPTEXT is existing
	 */
	private function isWebloginHelptext () {
		try {
			$txt = $this->_globalSettingsManager->valueGet (GlobalSettings::WEBLOGIN_HELPTEXT);
		} catch (Exception $e) {
			return false;
		}
		return ($txt != '');
	}

	private function easterEggLeg () {

		if ($this->_username == 'BaBeSK') {
			$this->assignErrorToSmarty('<marquee>' . file_get_contents("../credits.txt") . '</marquee>');
			$this->dieShowLoginForm();
		}
	}

	private function assignErrorToSmarty ($str) {

		$this->_smarty->assign('error', $str);
	}

	private function checkPassword () {

		if(!$this->_userManager->checkPassword($this->_userId, $this->_password)) {

			$this->assignErrorToSmarty(INVALID_LOGIN);
			$this->addLoginTryToUser();
			$this->dieShowLoginForm();
		}
	}

	private function checkLockedAccount () {

		if($this->_userManager->checkAccount($this->_userId)) {
			$this->assignErrorToSmarty(ACCOUNT_LOCKED);
			$this->dieShowLoginForm();
		}
	}

	private function addLoginTryToUser () {

		try {
			$this->_userManager->AddLoginTry($this->_userId);
		} catch (Exception $e) {
			$this->assignErrorToSmarty('error adding logintry to user');
			$this->dieShowLoginForm();
		}
	}

	private function finishSuccessfulLogin () {

		$_SESSION['uid'] = $this->_userId;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////

	private $_smarty;

	/**
	 * @var UserManager
	 */
	private $_userManager;

	/**
	 * @var GlobalSettingsManager
	 */
	private $_globalSettingsManager;

	private $_username;
	private $_password;
	private $_userId;
}

?>