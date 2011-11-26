<?php
//No direct access
defined('_WEXEC') or die("Access denied");
require_once 'change_password_constants.php';
require_once PATH_INCLUDE.'/functions.php';
global $smarty;

$userManager = new UserManager();
$userData = $userManager->getEntryData($_SESSION['uid'], '*');
$smarty->assign('username', $userData['username']);
$smarty->assign('credit', $userData['credit']);
$smarty->assign('last_login', $userData['last_login']);

if(isset($_POST['passwd'])) {
	if (!isset($_POST['passwd']) and !isset($_POST['passwd_repeat'])) {
		die(INVALID_FORM);
	}
	$passwd = '';
	if(($passwd = $_POST['passwd']) != $_POST['passwd_repeat']) {
		$smarty->assign('status', UNMATCHED_PASSWORDS);
		$smarty->display('web/modules/mod_change_password/change_password.tpl');
		exit();
	}
	if(!preg_match('/\A^[a-zA-Z0-9 _-]{4,20}\z/',$passwd)){
		$smarty->assign('status', CH_P_WRONG_PW);
		$smarty->display('web/modules/mod_change_password/change_password.tpl');
		exit();
	}
	else if($userData['password'] == hash_password($passwd)) {
		$smarty->assign('status', CH_P_OLD_PW);
		$smarty->display('web/modules/mod_change_password/change_password.tpl');
		exit();
	}

	$userManager->updatePassword($_SESSION['uid'], $passwd);
	 
	$smarty->assign('status', '<p>Erstpasswort wurde erfolgreich ge&auml;ndert</p>');
}
else {
	$smarty->display('web/modules/mod_change_password/change_password.tpl');
	exit();
}
?>