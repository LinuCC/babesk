<?php

class Web {

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
		require_once PATH_INCLUDE . '/Acl.php';
		require_once PATH_INCLUDE . '/ModuleExecutionInputParser.php';
		require_once 'WebInterface.php';

		TableMng::init ();
		$this->_userManager = new UserManager();
		$this->_loggedIn = isset($_SESSION['uid']);
		$this->_interface = new WebInterface($this->_smarty);
		$this->_acl = new Acl();
		$this->initPdo();
		$this->_moduleExecutionParser = new ModuleExecutionInputParser();
		$this->_moduleExecutionParser->setSubprogramPath('root/web');
		$this->_dataContainer = new DataContainer(
			$this->_smarty,
			$this->_interface,
			$this->_acl,
			$this->_pdo);
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

	public function mainRoutine() {

		$this->handleLogin();
		$this->handleRedirect();
		$this->initUserdata();
		$this->loadModules();
		$this->_smarty->assign('babeskActivated',
			(boolean) $this->_acl->moduleGet('root/web/Babesk'));
		$userData = $this->_userManager->getUserdata($_SESSION['uid']);
		$this->checkFirstPassword();
		$this->display();
	}

	///////////////////////////////////////////////////////////////////////
	//Implementations
	///////////////////////////////////////////////////////////////////////
	private function initEnvironment() {

		ini_set('session.use_cookies', 1);
		ini_set('session.use_only_cookies', 0);
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
		date_default_timezone_set('Europe/Berlin');

		session_start();

		//if this value is not set, the modules will not execute
		define('_WEXEC', 1);
	}

	private function initSmarty() {

		require PATH_SMARTY . "/smarty_init.php";
		$this->_smarty = $smarty;
		// $this->_smarty->assign('smarty_path', REL_PATH_SMARTY);
		$version=@file_get_contents("../version.txt");
		$this->_smarty->assign('inh_path', 'web/baseLayout.tpl');
		if ($version===FALSE) {
			$version = "";
		}
		$smarty->assign('babesk_version', $version);
		$this->_smarty->assign('error', '');
	}

	/**
	 * Initializes the PDO-Object, used for Database-Queries
	 *
	 * triggers an error when the PDO-Object could not be created
	 */
	private function initPdo() {

		try {
			$connector = new DBConnect();
			$connector->initDatabaseFromXML();
			$this->_pdo = $connector->getPdo();
			$this->_pdo->query('SET @activeSchoolyear :=
				(SELECT ID FROM schoolYear WHERE active = "1");');

		} catch (Exception $e) {
			trigger_error('Could not create the PDO-Object!');
		}
	}

	/**
	 * Checks if the User has a preset Password and has not changed it yet
	 */
	private function checkFirstPassword() {

		$changePasswordOnFirstLoginEnabled = TableMng::query('SELECT value
			FROM global_settings WHERE `name` = "firstLoginChangePassword"');

		if ($changePasswordOnFirstLoginEnabled[0]['value'] == '1') {
			$userData = $this->_userManager->getUserdata ($_SESSION ['uid']);
			$firstPassword = $userData ['first_passwd'];

			if ($firstPassword != '0') {
				$this->_smarty->assign('moduleroot',
					$this->_acl->getModuleroot());
				$pwChange = new ModuleExecutionInputParser(
					'root/web/Settings/ChangePresetPassword');
				$this->_acl->moduleExecute($pwChange, $this->_dataContainer);
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
					gsTarget.name = "webHomepageRedirectTarget"');

		} catch (Exception $e) {
			return;
		}
		if(isset($data[0]['target']) && $data[0]['target'] != '') {
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
				$_SESSION['uid']));

		} catch (MySQLVoidDataException $e) {
			return; //no new mails found

		} catch (Exception $e) {
			return; //No Emails found, maybe the tables do not exist
		}

		if ($mailcount[0]['count'] > 0) {
			$this->_smarty->assign('newmail', true);
		}
	}

	private function executeModule() {

		try {
			try {
				$this->_acl->moduleExecute($this->_moduleExecutionParser,
					$this->_dataContainer);

			} catch (Exception $e) {
				$this->_interface->dieError(_g('Error executing the Module!'));
			}


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

	private function display() {

		$this->_smarty->assign('moduleroot', $this->_acl->getModuleroot());
		$this->footerImagePath();
		if ($this->_moduleExecutionParser->load()) {
			$this->executeModule();
		}
		else {
			$birthday = date("m-d",strtotime($this->_userManager->getBirthday($_SESSION['uid'])));

			$this->_smarty->assign('birthday',$birthday);
			$this->_smarty->display(PATH_SMARTY . '/templates/web/main_menu.tpl');
		}
	}

	/**
	 * Loads the Path to the Footer-Image, so that it is not that buggy anymore
	 *
	 * Sets a Smarty-Variable when a background-Image is found
	 */
	private function footerImagePath() {

		$this->_moduleExecutionParser->load();
		$modpath = $this->_moduleExecutionParser->executionGet();
		$modpath = preg_replace('/.*?\/web\//', '', $modpath);
		$path = '';

		if($modpath) {
			$path = $this->footerImagePathLoadByModulepath($modpath);
		}

		if(empty($path)) {
			$path = "{$this->_imagepathPrefix}defaultImage.png";
			if(file_exists($path)) {
				$this->_smarty->assign('footerBackground', $path);
			}
		}
		else {
			$this->_smarty->assign('footerBackground', $path);
		}
	}

	/**
	 * Loads the Path of the Footer-Image by the Module-Path given
	 *
	 * @param  string $modpath The Path of the Module
	 * @return string          The Path to the Image to display or false if no
	 * Image by Modulepath found
	 */
	private function footerImagePathLoadByModulepath($modpath) {

		$filename = str_replace('/', '_', $modpath) . '_footer.png';
		//Relative Path form this is different than the path from Smarty
		$path = $this->_imagepathPrefix . $filename;

		if(file_exists($path)) {
			return $path;
		}
		else {
			$mods = explode('_', $modpath);
			array_pop($mods);

			if(count($mods) > 1) {
				$nModPath = implode('_', $mods);
				return $this->footerImagePathLoadByModulepath($nModPath);
			}
			else {
				return false;
			}
		}
	}

	///////////////////////////////////////////////////////////////////////
	//Attributes
	///////////////////////////////////////////////////////////////////////

	private $_smarty;
	private $_loggedIn;
	private $_interface;
	private $_userManager;
	private $_acl;
	private $_dataContainer;
	private $_moduleExecutionParser;

	/**
	 * The Prefix to the location where the Images are
	 * @var string
	 */
	private $_imagepathPrefix = '../images/moduleBackgrounds/';
}

?>
