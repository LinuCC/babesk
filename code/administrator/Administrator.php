<?php

require_once "../include/path.php";

require_once PATH_INCLUDE . '/TableMng.php';
require_once PATH_INCLUDE . '/Acl.php';
require_once PATH_INCLUDE . "/functions.php";
require_once PATH_INCLUDE . '/exception_def.php';
require_once PATH_INCLUDE . '/DataContainer.php';
require_once PATH_INCLUDE . '/ModuleExecutionInputParser.php';
require_once PATH_INCLUDE . '/ArrayFunctions.php';
require_once PATH_INCLUDE . '/sql_access/DBConnect.php';
require_once PATH_INCLUDE . '/Logger.php';
require_once 'Login.php';
require_once 'AdminInterface.php';

/**
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
		TableMng::init();
		$this->_adminInterface = new AdminInterface(NULL, $this->_smarty);
		// AdminInterface has used global $smarty, workaround
		AdminInterface::$smartyHelper = $this->_smarty;
		$this->_acl = new Acl();
		$this->_moduleExecutionParser = new ModuleExecutionInputParser();
		$this->_moduleExecutionParser->setSubprogramPath(
			'root/administrator');
		$this->loadVersion();
		$this->initPdo();
		$this->_logger = new Logger($this->_pdo);
		$this->_logger->categorySet('Administrator');
	}

	////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////

	public function run() {

		$login = new Login($this->_smarty);
		if($login->loginCheck()) {
			$this->accessControlInit();
			$this->initUserInterface();
			if($this->_moduleExecutionParser->load()) {
				$this->backlink();
				$this->moduleBacklink();
				$this->executeModule();
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

		$this->_smarty->assign('base_path',
			PATH_SMARTY . '/templates/administrator/base_layout.tpl');

	}

	public function executeModule() {

		try {

			$this->_acl->moduleExecute(
				$this->_moduleExecutionParser->executionCommandGet(),
				$this->dataContainerCreate());

		} catch (Exception $e) {
			$this->_logger->log(
				'Error executing a Module', 'Notice', Null,
				json_encode(array(
					'userId' => $_SESSION['UID'],
					'msg' => $e->getMessage()
			)));

			if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
				//It was an Ajax-Call, dont show the whole Website
				die(json_encode(array('value' => 'error',
					'message' => 'Konnte das Modul nicht ausführen:<br />' .
						$e->getMessage())));
			}
			else {
				$this->_adminInterface->dieError(
					'Konnte das Modul nicht ausführen:<br />' .
					$e->getMessage());
			}
		}
	}

	public function MainMenu() {

		$adminModule = $this->_acl->moduleGet('root/administrator');

		if($adminModule) {
			$this->_smarty->assign('is_mainmenu', true);
			$this->_smarty->assign('headmodules', $adminModule->getChilds());
			$this->_smarty->assign('moduleroot', $this->_acl->getModuleroot());
			$this->_smarty->display('administrator/menu.tpl');
		}
		else {
			$this->_logger->log('Administrator-Layer access denied.',
				'Notice', null, json_encode(array(
					'userId' => $_SESSION['UID']))
			);
			$this->_adminInterface->dieError(_g('Error Accessing the Admin-Layer; Either the Module does not exist, or you dont have the rights to access it!'));
		}

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
		date_default_timezone_set('Europe/Berlin');
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
		// $this->_smarty->assign('smarty_path', REL_PATH_SMARTY);
		$this->_smarty->assign('status', '');

		$version=@file_get_contents("../version.txt");
		if ($version===FALSE) $version = "";
		$smarty->assign('babesk_version', $version);
	}

	/**
	 * Initializes the PDO-Object, used for Database-Queries
	 *
	 * triggers an error when
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

	/**
	 * Adds an "Back to Module"-Link, useful if header-link couldnt be shown
	 */
	private function moduleBacklink() {

		$modCommand = clone(
			$this->_moduleExecutionParser->executionCommandGet());
		$modCommand->delim = '|';
		$link = $modCommand->pathGet();
		$this->_smarty->assign('moduleBacklink', $link);
	}

	/**
	 * Modules can set a manual backlink, handle it if set
	 */
	private function backlink() {

		if(!empty($_SESSION['backlink'])) {
			$this->_smarty->assign('backlink', $_SESSION['backlink']);
			unset($_SESSION['backlink']);
		}
	}

	/**
	 * Creates a DataContainer and returns it
	 * @return Object DataContainer A Container containing general data needed
	 *                by the Modules
	 */
	private function dataContainerCreate() {

		$dataContainer = new DataContainer(
			$this->_smarty,
			$this->_adminInterface,
			$this->_acl,
			$this->_pdo,
			$this->_logger);

		return $dataContainer;
	}

	////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////

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

	private $_login;

	private $_moduleExecutionParser;

	private $_pdo;
}

?>
