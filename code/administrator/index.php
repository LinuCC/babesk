<?php

require_once 'Administrator.php';
$adminManager = new Administrator();
$smarty = $adminManager->getSmarty();
$adminManager->run();

// $adminManager->setUserLoggedIn(isset($_SESSION['UID']));

// if (isset($_GET['action']) AND $_GET['action'] == 'logout') {
// 	$adminManager->userLogOut();
// 	die();
// }
// if ($adminManager->testLogin()) {
// 	//workaround of modules using smarty, logger and modManager globally
// 	$logger = $adminManager->getLogger();
// 	$modManager = $adminManager->getModuleManager();
// 	$adminManager->initUserInterface();

// 	if (isset($_GET['section'])) {
// 		$adminManager->executeModule($_GET['section'], false);
// 	}
// 	else {
// 		$adminManager->MainMenu();
// 	}
// }

?>
