<?php
/**
 * login-function
 * handles the login. It shows the login-form, then checks the input and, if successful,
 * it returns the ID of the User.
 * @param string $username
 * @param string $formpass
 * @return true if successfuly logged in
 */
function login() {
	defined('_WEXEC') or die("Access denied");
	global $smarty;
	
	if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['login'], $_POST['password'])) {
		require_once PATH_INCLUDE.'/constants.php';
		require_once PATH_INCLUDE.'/user_access.php';
		$userManager = new UserManager();
		$username = $_POST['login'];
		$formpass = $_POST['password'];
		try {
			inputcheck($username, 'name');
			inputcheck($formpass, 'password');
		} catch (Exception $e) {
 			$smarty->assign('error', INVALID_CHARS.':"'.$e->getMessage().'"');
			$smarty->display('web/login.tpl');
			die();
		}

		//get the userID by the username
		try {
			$uid = $userManager->getUserID($username);
		} catch (MySQLVoidDataException $e) {
			$smarty->assign('error', INVALID_LOGIN);
			$smarty->display('web/login.tpl');
			die();
		} catch (Exception $e) {
			die('ERROR:'.$e);
		}
		$is_pw_correct = $userManager->checkPassword($uid, $formpass);
		$account_locked = $userManager->checkAccount($uid);		//check if account is locked
		if (!$is_pw_correct) {
			$smarty->assign('error', INVALID_LOGIN);
			$userManager->AddLoginTry($uid);
			$smarty->display('web/login.tpl');
			exit();
		}
		elseif ($account_locked) {
			$smarty->assign('error', ACCOUNT_LOCKED);
			$smarty->display('web/login.tpl');
			exit();
		}
		else {
			$_SESSION['uid'] = $uid;
			return true;
		}
	}
	else {
		$smarty->display('web/login.tpl');
	}
}


?>