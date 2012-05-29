<?php

// ini_set('session.use_cookies', 1);
// ini_set('session.use_only_cookies', 0);

// session_name('sid');
// session_start();
// ini_set("default_charset", "utf-8");

// //if this value is not set, the modules will not execute
// define('_AEXEC', 1);

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// require_once "../include/path.php";
// require_once PATH_SMARTY . "/smarty_init.php";
// require_once PATH_INCLUDE . "/logs.php";
// require_once PATH_INCLUDE . "/functions.php";
// require_once PATH_INCLUDE . '/exception_def.php';
// require_once PATH_INCLUDE . '/moduleManager.php';
// //require_once 'modules.php';
// require_once 'locales.php';
// require_once 'AdminInterface.php';
// $smarty->assign('smarty_path', REL_PATH_SMARTY);
// $smarty->assign('status', '');
// $smarty->assign('babesk_version', file_get_contents("../version.txt"));

// //@todo: following line should not be here
// define('BASE_PATH', PATH_SMARTY . '/templates/administrator/base_layout.tpl');

// require_once PATH_ADMIN . '/admin_functions.php';

// //the module manager
// $moduleManager = new ModuleManager('administrator');

// //check for valid session and save the ip address
// validSession() or die(INVALID_SESSION);

// //logged in before if the user ID is set
// if (isset($_SESSION['UID'])) {
// 	$login = True;
// }
// else { //not logged in yet
// 	$login = False;
// }
// //logout
// if (isset($_GET['action']) AND $_GET['action'] == 'logout') {
// 	$login = False;
// 	session_destroy();
// }
// //login
// if (!$login) {
// 	require_once "login.php";
// }

// //login.php sets $login to true so this is executed after a successful log-in
// if ($login) {
// 	$smarty->assign('_ADMIN_USERNAME', $_SESSION['username']);
// 	$smarty->assign('sid', htmlspecialchars(SID));
// 	$smarty->assign('base_path', BASE_PATH);
// 	//include a module if selected
// 	if (isset($_GET['section'])) {
// 		$moduleManager->execute($_GET['section']);
// 	}
// 	//or include the menu
// 	else {

// 		$allowedModules = $moduleManager->getAllowedModules();
// 		$headModules = $moduleManager->getHeadModulesOfModules($allowedModules);
// 		$head_mod_arr = array();

// 		foreach ($headModules as $headModule) {
// 			$head_mod_arr[] = array('name' => $headModule->getName(), 'display_name' => $headModule->getDisplayName());
// 		}

// 		$smarty->assign('is_mainmenu', true);
// 		$smarty->assign('modules', $allowedModules);
// 		$smarty->assign('head_modules', $head_mod_arr);
// 		$smarty->assign('module_names', $moduleManager->getModuleDisplayNames());
// 		$smarty->display('administrator/menu.tpl');
// 	}
// }

//?>


<?php

require_once 'Administrator.php';

$adminManager = new Administrator();

$adminManager->setUserLoggedIn(isset($_SESSION['UID']));

if (isset($_GET['action']) AND $_GET['action'] == 'logout') {
	$adminManager->userLogOut();
	die();
}
if ($adminManager->testLogin()) {
	//workaround of modules using smarty, logger and modManager globally
	$smarty = $adminManager->getSmarty();
	$logger = $adminManager->getLogger();
	$modManager = $adminManager->getModuleManager();
	$adminManager->initUserInterface();
	
	if (isset($_GET['section'])) {
		$adminManager->executeModule($_GET['section'], false);
	}
	else {
		$adminManager->MainMenu();
	}
}

?>