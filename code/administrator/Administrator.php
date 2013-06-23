<?php

require_once "../include/path.php";
require_once PATH_INCLUDE . '/TableMng.php';
require_once PATH_INCLUDE . '/Acl.php';
require_once PATH_ADMIN . '/admin_functions.php';
require_once PATH_ACCESS . "/LogManager.php";
require_once PATH_INCLUDE . "/functions.php";
require_once PATH_INCLUDE . '/exception_def.php';
require_once PATH_INCLUDE . '/moduleManager.php';
require_once PATH_INCLUDE . '/DataContainer.php';
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

	public function __construct () {

		if(!isset($_SESSION)) {
			$this->initEnvironment();
		}

		validSession() or die(INVALID_SESSION);
		$this->initSmarty();
		TableMng::init ();
		$this->_adminInterface = new AdminInterface(NULL, $this->_smarty);
		$this->_logger = new LogManager();
		$this->_acl = new Acl();
		$this->_moduleManager = new ModuleManager('administrator',
			$this->_adminInterface);
		$this->_moduleManager->setDataContainer(new DataContainer(
			$this->_smarty, $this->_adminInterface, $this->_acl));
	}

	////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////

	public function getUserLoggedIn () {
		return $this->_userLoggedIn;
	}

	public function setUserLoggedIn ($userLoggedIn) {
		$this->_userLoggedIn = $userLoggedIn;
	}

	public function getSmarty () {
		return $this->_smarty;
	}

	public function getLogger () {
		return $this->_logger;
	}

	public function getModuleManager () {
		return $this->_moduleManager;
	}

	////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////

	public function initUserInterface () {

		$this->_smarty->assign('_ADMIN_USERNAME', $_SESSION['username']);
		$this->_smarty->assign('sid', htmlspecialchars(SID));

			$this->_smarty->assign('base_path', PATH_SMARTY . '/templates/administrator/base_layout.tpl');

	}

	public function userLogOut () {

		$login = False;
		session_destroy();
		$this->showLogin();
	}

	public function executeModule ($moduleName) {

		$smarty = $this->_smarty;
		$this->_moduleManager->execute($moduleName);
	}

	public function MainMenu () {

		$headmodules = $this->_acl->getModuleroot()->moduleByPathGet(
			'root/administrator')->getChilds();

		$this->_smarty->assign('is_mainmenu', true);
		// $this->_smarty->assign('modules', $allowedModules);
		$this->_smarty->assign('headmodules', $headmodules);
		// $this->_smarty->assign('module_names', $this->_moduleManager->getModuleDisplayNames());
		$this->_smarty->display('administrator/menu.tpl');
	}

	public function testLogin () {

		if (!$this->getUserLoggedIn()) {
			if ($this->showLogin())
				return true;
			else
				return false;
		}
		else {
			return true;
		}

	}

	public function showLogin () {

		$smarty = $this->_smarty;
		require_once "login.php";

		if ($login) //coming from login.php, another problem...
			return true;
		else
			return false;
	}

	////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////

	private function initEnvironment () {

		$this->setPhpIni();

		//if this value is not set, the modules will not execute
		define('_AEXEC', 1);

		session_name('sid');
		session_start();
		error_reporting(E_ALL);
	}

	private function initSmarty () {

		require_once PATH_SMARTY . "/smarty_init.php";

		$this->_smarty = $smarty;
		$this->_smarty->assign('smarty_path', REL_PATH_SMARTY);
		$this->_smarty->assign('status', '');

		$version=@file_get_contents("../version.txt");
		if ($version===FALSE) $version = "";
		$smarty->assign('babesk_version', $version);
	}

	private function setPhpIni () {

		ini_set('display_errors', 1);
		ini_set('session.use_cookies', 1);
		ini_set('session.use_only_cookies', 0);
		ini_set("default_charset", "utf-8");
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
}

?>
