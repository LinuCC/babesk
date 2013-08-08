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
require_once PATH_INCLUDE . '/ModuleExecutionInputParser.php';
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
		TableMng::init();
		$this->_adminInterface = new AdminInterface(NULL, $this->_smarty);
		$this->_logger = new LogManager();
		$this->_acl = new Acl();
		$this->_moduleExecutionParser = new ModuleExecutionInputParser();
		$this->_moduleExecutionParser->setSubprogramPath(
			'root/administrator');
		$this->loadVersion();
		$this->_dataContainer = new DataContainer(
			$this->_smarty,
			$this->_adminInterface,
			$this->_acl);

		// $this->vikingsSpamAndPorc();
	}

	protected function vikingsSpamAndPorc() {

		TableMng::getDb()->autocommit(false);

		$activeSy = TableMng::query('SELECT ID  FROM schoolYear sy
   WHERE active = 1');

		$users = TableMng::query('SELECT u.ID AS userId,
    uig.GradeID AS gradeId

   FROM users u
   JOIN jointUsersInGrade uig ON uig.UserID = u.ID');

		$stmt = TableMng::getDb()->prepare(
				'INSERT INTO usersInGradesAndSchoolyears
    (UserID, GradeID, schoolyearId) VALUES
    (?, ?, ?);
   ');

		foreach($users as $user) {
			$stmt->bind_param('sss', $user['userId'], $user['gradeId'], $activeSy[0]['ID']);
			if($stmt->execute()) {
				//yay
			}
			else {
				throw new Exception('Could not change things'. $stmt->error);
			}
		}

		TableMng::getDb()->autocommit(true);
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

			$this->_smarty->assign('base_path', PATH_SMARTY . '/templates/administrator/base_layout.tpl');

	}

	public function executeModule() {

		try {
			$this->_acl->moduleExecute(
				$this->_moduleExecutionParser, $this->_dataContainer);

		} catch (Exception $e) {
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

	/**
	 * Adds an "Back to Module"-Link, useful if header-link couldnt be shown
	 */
	private function moduleBacklink() {

		$link = str_replace('/', '|',
			$this->_moduleExecutionParser->moduleExecutionGet());
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
