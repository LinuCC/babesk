<?php

require_once 'NewWebInterface.php';

class Web {
	///////////////////////////////////////////////////////////////////////
	//Attributes
	///////////////////////////////////////////////////////////////////////
	private $_smarty;
	private $_loggedIn;
	private $_interface;
	private $_userManager;
	private $_acl;
	private $_dataContainer;

	///////////////////////////////////////////////////////////////////////
	//Constructor
	///////////////////////////////////////////////////////////////////////
	public function __construct () {

		if (!isset($_SESSION)) {
			require_once "../include/path.php";
			$this->initEnvironment();
			$this->initSmarty();
		}

		require_once PATH_ACCESS . '/UserManager.php';
		require_once PATH_INCLUDE . '/moduleManager.php';
		require_once PATH_INCLUDE . '/functions.php';
		require_once PATH_INCLUDE . '/TableMng.php';
		require_once PATH_ACCESS . '/LogManager.php';
		require_once PATH_ACCESS . '/GlobalSettingsManager.php';
		require_once PATH_INCLUDE . '/Acl.php';
		require_once 'WebInterface.php';

		TableMng::init ();
		$this->_userManager = new UserManager();
		$this->_gsManager = new GlobalSettingsManager ();
		$this->_loggedIn = isset($_SESSION['uid']);
		$this->_interface = new WebInterface($this->_smarty);
		$this->_acl = new Acl();
		$this->_acl->setSubprogramPath('root/web');
		$this->_dataContainer = new DataContainer($this->_smarty,
			$this->_interface, $this->_acl);
		$this->initLanguage();
	}

	///////////////////////////////////////////////////////////////////////
	//Getters and Setters
	///////////////////////////////////////////////////////////////////////
	public function getSmarty() {
		return $this->_smarty;
	}

	///////////////////////////////////////////////////////////////////////
	//Methods
	///////////////////////////////////////////////////////////////////////
	public function logOut() {

		$this->_loggedIn = false;
		session_destroy();
	}

	public function mainRoutine($moduleStr) {

		$this->handleLogin();
		$this->handleRedirect();
		$this->initUserdata();
		$this->loadModules();
		$this->checkFirstPassword();
		$this->display($moduleStr);
	}

	///////////////////////////////////////////////////////////////////////
	//Implementations
	///////////////////////////////////////////////////////////////////////
	private function initEnvironment() {

		ini_set('session.use_cookies', 1);
		ini_set('session.use_only_cookies', 0);
		error_reporting(E_ALL);
		ini_set('display_errors', 1);

		session_start();

		//if this value is not set, the modules will not execute
		define('_WEXEC', 1);
	}

	private function initSmarty() {

		require PATH_SMARTY . "/smarty_init.php";
		$this->_smarty = $smarty;
		$this->_smarty->assign('smarty_path', REL_PATH_SMARTY);
		$version=@file_get_contents("../version.txt");
		if ($version===FALSE) {
			$version = "";
		}
		$smarty->assign('babesk_version', $version);
		$this->_smarty->assign('error', '');
	}

	/**
	 * Checks if the User has a preset Password and has not changed it yet
	 */
	private function checkFirstPassword() {

		$changePasswordOnFirstLoginEnabled = TableMng::query('SELECT value
			FROM global_settings WHERE `name` = "firstLoginChangePassword"',
			true);

		if ($changePasswordOnFirstLoginEnabled[0]['value'] == '0') {
			$userData = $this->_userManager->getUserdata ($_SESSION ['uid']);
			$firstPassword = $userData ['first_passwd'];

			if ($firstPassword != '0') {
				$this->_moduleManager->execute
					('Settings|ChangePresetPassword');
				die ();
			}
		}
	}

	/**
	 * handles if the user gets redirected after some seconds
	 */
	private function redirect() {

		try {
			$data = TableMng::query('SELECT gsDelay.value AS delay,
					gsTarget.value AS target
				FROM global_settings gsDelay, global_settings gsTarget
				WHERE gsDelay.name = "webHomepageRedirectDelay" AND
					gsTarget.name = "webHomepageRedirectTarget"', true);

		} catch (Exception $e) {
			return;
		}
		if ($data[0]['target'] != '') {
			$red = array (
				'time' => $data[0]['delay'],
				'target' => $data[0]['target']);
			$this->_smarty->assign('redirection', $red);
		}
	}

	private function initUserdata() {

		$userData = $this->_userManager->getUserdata($_SESSION['uid']);

		$this->addSessionUserdata($userData);
		$this->handleModuleSpecificData($userData);
		$this->loginTriesHandle($userData);
		$this->addUserdataToSmarty();
	}

	/**
	 * Adds Session-vars containing data about the connected client
	 */
	private function addSessionUserdata($userData) {
		$_SESSION['username'] = $userData['forename'] . ' ' . $userData['name'];
		$_SESSION['last_login'] = formatDateTime($userData['last_login']);
		$_SESSION['login_tries'] = $userData['login_tries'];
		$_SESSION['IP'] = $_SERVER['REMOTE_ADDR'];
		$_SESSION['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
	}

	private function addUserdataToSmarty() {

		$this->_smarty->assign('uid', $_SESSION['uid']);
		$this->_smarty->assign('username', $_SESSION['username']);
		$this->_smarty->assign('last_login', $_SESSION['last_login']);
	}

	/**
	 * check for new mail
	 */
	private function checkForMail() {

		try {
			$mailcount = TableMng::query(sprintf("SELECT COUNT(*) AS count
				FROM MessageReceivers mr
				LEFT JOIN Message m ON mr.messageId = m.ID
				WHERE %s = userId
					AND SYSDATE() BETWEEN m.validFrom AND m.validTo
					AND mr.read = 0",
				$_SESSION['uid']), true);

		} catch (MySQLVoidDataException $e) {
			return; //no new mails found

		} catch (Exception $e) {
			return; //No Emails found, maybe the tables do not exist
		}

		if ($mailcount[0]['count'] > 0) {
			$this->_smarty->assign('newmail', true);
		}
	}

	private function executeModule($name) {

		try {
			$this->_acl->moduleExecute($name, $this->_dataContainer);

		} catch (AclException $e) {
			if($e->getCode() == 105) { //Module-Access forbidden
				$this->_interface->dieError(
					'Keine Zugriffsberechtigung auf dieses Modul!');
			}
		}
	}

	private function initLanguage() {

		$language = 'de_DE.utf-8';
		$domain = 'messages';

		putenv("LANG=$language");
		setlocale(LC_ALL, $language);

		// Set the text domain as 'messages'
		bindtextdomain($domain, PATH_CODE . '/locale');
		bind_textdomain_codeset($domain, "UTF-8");
		textdomain($domain);
	}

	private function handleLogin() {

		if (!$this->_loggedIn) {
			$this->logIn();
			$this->redirect();
		}
	}

	private function logIn() {

		require_once 'Login.php';
		$loginManager = new Login($this->_smarty);
		if($loginManager->login()) {
			$this->_userManager->updateLastLoginToNow($_SESSION['uid']);
		}
	}

	private function displayCreditsWhenActive($userData) {

		//module-specific
		if (isset($userData['credit'])) {
			$_SESSION['credit'] = $userData['credit'];
			$this->_smarty->assign('credit', $_SESSION['credit']);
		}
	}

	private function loginTriesHandle($userData) {

		if ($_SESSION['login_tries'] > 3) {
			$this->_smarty->assign('login_tries', $_SESSION['login_tries']);
			$this->_userManager->ResetLoginTries($userData['ID']);
			$_SESSION['login_tries'] = 0;
		}
	}

	private function handleModuleSpecificData($userData) {

		$this->displayCreditsWhenActive($userData);
		$this->checkForMail();
	}

	private function loadModules() {

		try {
			$this->_acl->accessControlInit($_SESSION['uid']);
			$webModule = $this->_acl->moduleGet('root/web');
			$this->_smarty->assign('modules', $webModule->getChilds());

		} catch (AclException $e) {
			$this->_interface->dieError('Sie sind in keiner Gruppe und ' .
				'haben daher keine Rechte! Wenden sie sich bitte an den ' .
				'Administrator');
		}
	}

	private function handleRedirect() {
		if (isset($_GET ['webRedirect'])) { //redirect to a module
			$this->redirect();
		}
	}

	private function display($moduleStr) {

		$this->_smarty->assign('moduleroot', $this->_acl->getModuleroot());
		if ($moduleStr) {
			$this->executeModule($moduleStr);
		}
		else {
			$birthday = date("m-d",strtotime($this->_userManager->getBirthday($_SESSION['uid'])));

			$this->_smarty->assign('birthday',$birthday);
			$this->_smarty->display(PATH_SMARTY . '/templates/web/main_menu.tpl');
		}
	}
}

?>
