<?php


require_once "../include/path.php";
require_once PATH_INCLUDE . '/TableMng.php';
require_once PATH_INCLUDE . '/Acl.php';
require_once PATH_ADMIN . '/admin_functions.php';
require_once PATH_ACCESS . "/LogManager.php";
require_once PATH_INCLUDE . "/functions.php";
require_once PATH_INCLUDE . '/exception_def.php';
require_once PATH_INCLUDE . '/DataContainer.php';
require_once PATH_INCLUDE . '/Module.php';
require_once PATH_INCLUDE . '/HeadModule.php';
require_once 'Login.php';
require_once 'AdminInterface.php';
require_once 'locales.php';

/**
 *
 * @author Pascal Ernst <pascal.cc.ernst@googlemail.com>
 *
 */
class Administrator {

	////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////

	public function __construct() {

		if(!isset($_SESSION)) {
			$this->initEnvironment();
		}

		validSession() or die(INVALID_SESSION);
		$this->initSmarty();
		TableMng::init ();
		$this->_adminInterface = new AdminInterface(NULL, $this->_smarty);
		$this->_logger = new LogManager();
		$this->_acl = new Acl();
		$this->loadVersion();
		$this->_dataContainer = new DataContainer(
			$this->_smarty,
			$this->_adminInterface,
			$this->_acl);
	}

	////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////

	public function getUserLoggedIn() {
		return $this->_userLoggedIn;
	}

	public function setUserLoggedIn($userLoggedIn) {
		$this->_userLoggedIn = $userLoggedIn;
	}

	public function getSmarty() {
		return $this->_smarty;
	}

	public function getLogger() {
		return $this->_logger;
	}

	public function getModuleManager() {
		return $this->_moduleManager;
	}

	////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////

	public function run() {

		$smarty = $this->_smarty;
		$logger = $this->_logger;

		$login = new Login($this->_smarty);
		if($login->loginCheck()) {
			$this->accessControlInit();
			$this->initUserInterface();
			if(isset($_GET['section'])) {
				$this->executeModule($_GET['section'], false);
			}
			else {
				$this->MainMenu();
			}
		}
		else {
			die('Not logged in');
		}
	}

	public function initUserInterface() {

		$this->_smarty->assign('_ADMIN_USERNAME', $_SESSION['username']);
		$this->_smarty->assign('sid', htmlspecialchars(SID));

			$this->_smarty->assign('base_path', PATH_SMARTY . '/templates/administrator/base_layout.tpl');

	}

	public function executeModule($moduleName) {

		$modSubPath = explode('|', $moduleName);
		$headmod = $modSubPath[0];
		$mod = $modSubPath[1];
		$path = "root/administrator/$headmod/$mod";
		$smarty = $this->_smarty;
		try {
			$this->_acl->moduleExecute($path, $this->_dataContainer);

		} catch (Exception $e) {
			$this->_adminInterface->dieError(
				'Konnte das Modul nicht ausführen:' . $e->getMessage());
		}
	}

	public function MainMenu() {

		$adminModule = $this->_acl->moduleGet('root/administrator');

		$this->_smarty->assign('is_mainmenu', true);
		$this->_smarty->assign('headmodules', $adminModule->getChilds());
		$this->_smarty->assign('moduleroot', $this->_acl->getModuleroot());
		$this->_smarty->display('administrator/menu.tpl');
	}


	////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////

	private function initEnvironment() {

		$this->setPhpIni();
		$this->initLanguage();

		//if this value is not set, the modules will not execute
		define('_AEXEC', 1);

		session_name('sid');
		session_start();
		error_reporting(E_ALL);
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

	private function initSmarty() {

		require_once PATH_SMARTY . "/smarty_init.php";

		$this->_smarty = $smarty;
		$this->_smarty->assign('smarty_path', REL_PATH_SMARTY);
		$this->_smarty->assign('status', '');

		$version=@file_get_contents("../version.txt");
		if ($version===FALSE) $version = "";
		$smarty->assign('babesk_version', $version);
	}

	private function setPhpIni() {

		ini_set('display_errors', 1);
		ini_set('session.use_cookies', 1);
		ini_set('session.use_only_cookies', 0);
		ini_set("default_charset", "utf-8");
	}

	private function loadVersion() {

		$version = '';

		if(file_exists('../version.txt')) {
			$version = file_get_contents('../version.txt');
		}
		$this->_smarty->assign('babesk_version', $version);
	}

	private function accessControlInit() {

		try {
			$this->_acl->accessControlInit($_SESSION['UID']);

		} catch(AclException $e) {
			if($e->getCode() == 104) {
				$this->_smarty->assign('status',
					'Account hat keine Admin-Berechtigung');
				$this->_smarty->display('administrator/login.tpl');
				die();
			}
			else {
				$this->_adminInterface->dieError(
					'Konnte den Zugriff nicht einrichten!');
			}
		}
	}

	////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////

	/**
	 * The Modulemanager
	 * @var ModuleManager
	 */
	private $_moduleManager;

	/**
	 * The Access-Control-Layer
	 */
	private $_acl;

	/**
	 * The Interface handling displaying stuff
	 * @var AdminInterface
	 */
	private $_adminInterface;

	/**
	 * If the User is logged in or not
	 * @var boolean
	 */
	private $_userLoggedIn;

	/**
	 * The Smarty-Object
	 * @var Smarty
	 */
	private $_smarty;

	/**
	 * To log things
	 * @var Logs
	 */
	private $_logger;

	private $_dataContainer;

	private $_login;
}

?>
