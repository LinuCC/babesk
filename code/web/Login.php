<?php

/**
 * Handles the Login for the Web-program
 * @author Pascal Ernst <pascal.cc.ernst@googlemail.com>
 */
class Login {

	////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////
	public function __construct ($smarty) {
        $this->interface = new WebInterface($smarty);
		$this->_smarty = $smarty;
		$this->setUpUserManager();
		$this->setUpGlobalSettingsManager ();
	}
	////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////

	/**
	 * login-function
	 * handles the login. It shows the login-form, then checks the input and, if successful,
	 * it returns the ID of the User.
	 * @param string $username
	 * @param string $formpass
	 * @return true if successfuly logged in
	 */
	public function login() {

		if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['login'], $_POST['password'])) {
			$this->entryPoint();
			return $this->checkLogin();
		}
		else {
			$this->dieShowLoginForm();
		}
	}
	////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////

	protected function entryPoint() {

		defined('_WEXEC') or die("Access denied");
		$this->_username = $_POST['login'];
		$this->_password = $_POST['password'];
		$this->_isAjaxRequest = (
			isset($_GET['login']) && $_GET['login'] == 'ajax'
		);
	}

	private function checkLogin() {

		$this->easterEggLeg();
		$this->checkLoginInput();
		$this->setUserIdByUsername();
		$this->checkPassword();
		$this->checkLockedAccount();
        $this->checkCardLost();
		$this->finishSuccessfulLogin();

		if($this->_isAjaxRequest) {
			die(json_encode(array('val' => 'success')));
		}
		else {
			return true;
		}
	}

	private function setUpUserManager() {

		require_once PATH_ACCESS . '/UserManager.php';
		$this->_userManager = new UserManager();
	}

	private function setUpGlobalSettingsManager () {
		require_once PATH_ACCESS . '/GlobalSettingsManager.php';
		$this->_globalSettingsManager = new GlobalSettingsManager ();
	}

	private function setUserIdByUsername() {

		try {
			$this->_userId = $this->_userManager->getUserID($this->_username);

		} catch (MySQLVoidDataException $e) {
			$this->dieShowLoginForm(
				_g('User not found or incorrect password'), true
			);
		} catch (Exception $e) {
			$this->_logger->log(
				'Error while fetching the userId at userlogin!',
				'Moderate', Null,
				json_encode(array('msg' => $e->getMessage())));
			$this->dieShowLoginForm(_g('Error while logging you in!'), true);
		}
	}

	private function checkLoginInput() {

		if(empty($this->_username) || empty($this->_password)) {
			$this->dieShowLoginForm(
				_g('Please input both username and password!'), true
			);
		}

		try {
			inputcheck($this->_username, 'name', _g('Username'));
			inputcheck($this->_password, 'password', _g('Password'));

		} catch (WrongInputException $e) {
			$this->dieShowLoginForm(
				_g('The input of field %1$s contains invalid characters or ' .
					'is too short!', $e->getFieldName()
				), true
			);
		}
	}

	private function dieShowLoginForm($msg = '', $isError = false) {

		if($this->_isAjaxRequest) {
			$val = ($isError) ? 'error' : 'notice';
			die(json_encode(array('msg' => $msg, 'val' => $val)));
		}
		else {
			if ($this->isWebloginHelptext()) {
				$this->_smarty->assign ('webLoginHelptext', true);
			}
			else {
				$this->_smarty->assign ('webLoginHelptext', false);
			}
			$this->_smarty->display(PATH_SMARTY_TPL . '/web/login.tpl');
			die();
		}
	}

	/**
	 * Checks if the global Setting GlobalSettings::WEBLOGIN_HELPTEXT is existing
	 */
	private function isWebloginHelptext() {
		try {
			$txt = $this->_globalSettingsManager->valueGet (GlobalSettings::WEBLOGIN_HELPTEXT);
		} catch (Exception $e) {
			return false;
		}
		return ($txt != '');
	}

	private function easterEggLeg() {

		if ($this->_username == 'BaBeSK') {
			$this->dieShowLoginForm(
				'<marquee>' . file_get_contents("../credits.txt") .
				'</marquee>'
			);
		}
	}

	private function assignErrorToSmarty($str) {

		$this->_smarty->assign('error', $str);
	}

	private function checkPassword() {

		if(!$this->_userManager->checkPassword($this->_userId, $this->_password)) {

			$this->assignErrorToSmarty(
				_g('User not found or incorrect password')
			);
			$this->addLoginTryToUser();
			$this->dieShowLoginForm(
				_g('User not found or incorrect password'), true
			);
		}
	}

	private function checkLockedAccount() {

		if($this->_userManager->checkAccount($this->_userId)) {
			$this->dieShowLoginForm(_g('Account is locked!'), true);
		}
	}

    private function checkCardLost() {
        $lost = TableMng::query(sprintf("select lost from cards where UID = %s", $this->_userId))[0]['lost'];
        if ($lost) {
            TableMng::query(sprintf("UPDATE cards SET lost=%s WHERE UID = %s", $lost-1, $this->_userId));
            $this->interface->showError("Deine LeG-Card wurde gefunden und kann im GNISSEL-Raum abgeholt werden.");
        }
    }

	private function addLoginTryToUser() {

		try {
			$this->_userManager->AddLoginTry($this->_userId);
		} catch (Exception $e) {
			$this->assignErrorToSmarty('error adding logintry to user');
			$this->dieShowLoginForm();
		}
	}

	private function finishSuccessfulLogin() {

		$_SESSION['uid'] = $this->_userId;
	}

	////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////

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

	private $_isAjaxRequest;
}

?>
