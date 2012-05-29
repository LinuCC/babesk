<?php

require_once 'Web.php';

$smarty;

$webManager = new Web();

if (isset($_GET['action']) AND $_GET['action'] == 'logout') {
	$webManager->logOut();
}

$smarty = $webManager->getSmarty();

if (isset($_GET['section'])) {
	$webManager->mainRoutine($_GET['section']);
}
else {
	$webManager->mainRoutine(false);
}

die();





//relative smarty path for css files

//verhindert, dass module nicht von der index.php aufgerufen werden
//$load_modules = 1;
//logged in before if the user ID is set
if (isset($_SESSION['uid'])) {
	$login = True;
}
else { //not logged in yet
	$login = False;
}
//logout
//login
if (!$login) {
	if (login()) {
		$login = true;
	}
}
if ($login) {
	require_once PATH_ACCESS . '/UserManager.php';
	$userManager = new UserManager();
	//seems like something that Smarty itself needs
	$smarty->assign('status', ''); //???

	// check for first password
	if ($userManager->firstPassword($_SESSION['uid'])) {
		$modManager->execute('Babesk|ChangePassword');
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
		$smarty->assign('login_tries', $_SESSION['login_tries']);
		$userManager->ResetLoginTries($userData['ID']);
		$_SESSION['login_tries'] = 0;
	}

	$smarty->assign('uid', $_SESSION['uid']);
	$smarty->assign('username', $_SESSION['username']);
	$smarty->assign('credit', $_SESSION['credit']);
	$smarty->assign('last_login', $_SESSION['last_login']);

	$head_modules = $modManager->getHeadModules();
	$head_mod_arr = array();

	foreach ($head_modules as $head_module) {
		$head_mod_arr[$head_module->getName()] = array('name' => $head_module->getName(), 'display_name' => $head_module
			->getDisplayName());
	}
	$smarty->assign('head_modules', $head_mod_arr);

	//include the module specified in GET['section']
	if (isset($_GET['section'])) {
		$modManager->execute($_GET['section']);
	}
	//or include the main menu
	else {
		$modManager->execute("Babesk|Menu");
	}

	//MAX_LOGIN_TIME
	/*if ((isset($_SESSION['uid'])) AND (time() < ($_SESSION['last_action'] + MAX_LOGIN_TIME)) AND ($_SESSION['IP'] == $_SERVER['REMOTE_ADDR']) AND ($_SESSION['HTTP_USER_AGENT'] == $_SERVER['HTTP_USER_AGENT'])) {
	 $_SESSION['lastaction'] = time();
	 require_once('modules.php');
	 } else {
	 $_SESSION['IP'] = '';
	 $smarty->display('web/index.tpl');
	 }*/
}
?>