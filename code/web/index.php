<?php

ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 0);
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

//if this value is not set, the modules will not execute
define('_WEXEC', 1);

require_once "../include/path.php";
require_once PATH_INCLUDE.'/managers.php';
require_once PATH_INCLUDE.'/moduleManager.php';
require_once PATH_INCLUDE.'/functions.php';
require_once PATH_SMARTY."/smarty_init.php";
require_once 'login.php';
require 'modules.php';
//relative smarty path for css files
$smarty->assign('smarty_path', REL_PATH_SMARTY);

$smarty->assign('error', '');

$modManager = new ModuleManager($modules);

//verhindert, dass module nicht von der index.php aufgerufen werden
//$load_modules = 1;
//logged in before if the user ID is set
if(isset($_SESSION['uid'])) {
	$login = True;
}
else {     //not logged in yet
	$login = False;
}
//logout
if (isset($_GET['action']) AND  $_GET['action'] == 'logout') {
	$login = False;
	session_destroy();
}
//login
if(!$login) {
	if(login()) {
		$login = true;
	}
}
if($login){ 
	//seems like something that Smarty itself needs
	$smarty->assign('status', ''); //???
	 
	// check for first password
	if($userManager->firstPassword($_SESSION['uid'])) {
		$modManager->executeWeb('change_password');
	}
	$userData = $userManager->getEntryData($_SESSION['uid'], '*');

	$_SESSION['last_login'] = formatDateTime($userData['last_login']);
	$_SESSION['credit'] = $userData['credit'];
	$_SESSION['username'] = $userData['forename'].' '.$userData['name'];
	$_SESSION['login_tries'] = $userData['login_tries'];
	$_SESSION['IP'] = $_SERVER['REMOTE_ADDR'];
	$_SESSION['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];

	//general user data for header etc
	if ($_SESSION['login_tries'] > 3) {
		$smarty->assign('login_tries', $_SESSION['login_tries']);
		$userManager->ResetLoginTries($userData['ID']);
		$_SESSION['login_tries'] = 0;          //????? musste schon in der DB zur�cksetzen
	}

	$smarty->assign('uid', $_SESSION['uid']);
	$smarty->assign('username', $_SESSION['username']);
	$smarty->assign('credit', $_SESSION['credit']);
	$smarty->assign('last_login', $_SESSION['last_login']);
	 
	 
	//include the module specified in GET['section']
	if (isset($_GET['section'])) {
		$modManager->executeWeb($_GET['section']);
	}
	//or include the main menu
	else {
		$modManager->executeWeb("menu");
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