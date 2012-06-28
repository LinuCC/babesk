<?php

class Web {
	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////
	private $_smarty;
	private $_moduleManager;
	private $_loggedIn;

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct () {

		if (!isset($_SESSION)) {
			
			require_once "../include/path.php";
			
			$this->initEnvironment();
			$this->initSmarty();

			require_once PATH_INCLUDE . '/moduleManager.php';
			require_once PATH_INCLUDE . '/functions.php';

		}
		$this->_moduleManager = new ModuleManager('web');
		$this->_loggedIn = isset($_SESSION['uid']);
		$this->_moduleManager->allowAllModules();
	}

	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////
	public function getSmarty () {
		return $this->_smarty;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	public function logOut () {

		$this->_loggedIn = false;
		session_destroy();
	}

	public function logIn () {

		require_once 'login.php';
		return login();
	}

	public function mainRoutine ($mod_str) {

		if (!$this->_loggedIn) {
			if (!$this->logIn()) {
				return;
			}
		}

		require_once PATH_ACCESS . '/UserManager.php';
		$userManager = new UserManager();
		//seems like something that Smarty itself needs
		$this->_smarty->assign('status', ''); //???

		// check for first password
		if ($userManager->firstPassword($_SESSION['uid'])) {
			$this->_moduleManager->execute('Babesk|ChangePassword', false);
		}
		$userData = $userManager->getEntryData($_SESSION['uid'], '*');

		$_SESSION['last_login'] = formatDateTime($userData['last_login']);
		$_SESSION['credit'] = $userData['credit'];
		$_SESSION['username'] = $userData['forename'] . ' ' . $userData['name'];
		$_SESSION['login_tries'] = $userData['login_tries'];
		$_SESSION['IP'] = $_SERVER['REMOTE_ADDR'];
		$_SESSION['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];

		//general user data for header etc
		if ($_SESSION['login_tries'] > 3) {
			$this->_smarty->assign('login_tries', $_SESSION['login_tries']);
			$userManager->ResetLoginTries($userData['ID']);
			$_SESSION['login_tries'] = 0;
		}

		$this->_smarty->assign('uid', $_SESSION['uid']);
		$this->_smarty->assign('username', $_SESSION['username']);
		$this->_smarty->assign('credit', $_SESSION['credit']);
		$this->_smarty->assign('last_login', $_SESSION['last_login']);

		$head_modules = $this->_moduleManager->getHeadModules();
		$head_mod_arr = array();

		foreach ($head_modules as $head_module) {
			$head_mod_arr[$head_module->getName()] = array('name' => $head_module->getName(), 'display_name' =>
				$head_module->getDisplayName());
		}
		$this->_smarty->assign('head_modules', $head_mod_arr);

		//include the module specified in GET['section']
		if ($mod_str) {
			$this->_moduleManager->execute($mod_str, false);
		}
		//or include the main menu
		else {
			$this->_smarty->display(PATH_SMARTY . '/templates/web/main_menu.tpl');
		}
	}

	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////
	private function initEnvironment () {

		ini_set('session.use_cookies', 1);
		ini_set('session.use_only_cookies', 0);
		error_reporting(E_ALL);
		ini_set('display_errors', 1);

		session_start();

		//if this value is not set, the modules will not execute
		define('_WEXEC', 1);
	}

	private function initSmarty () {

		require PATH_SMARTY . "/smarty_init.php";
		$this->_smarty = $smarty;
		$this->_smarty->assign('smarty_path', REL_PATH_SMARTY);
		$this->_smarty->assign('babesk_version', file_get_contents("../version.txt"));
		$this->_smarty->assign('error', '');
	}
}

?>
