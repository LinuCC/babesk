<?php
    //No direct access
    defined('_WEXEC') or die("Access denied");
    
    require_once PATH_INCLUDE.'/functions.php';
    
    
    if ('POST' == $_SERVER['REQUEST_METHOD']) {
        if(empty($_POST['login']) OR empty($_POST['password'])) {
			$smarty->assign('error', EMPTY_FORM);
			$smarty->display('web/index.tpl');
            exit();
        }

        $username = $_POST['login'];
        $formpass = $_POST['password'];

		$result = $userManager->getUserID($username);

        if (!$result) {
            $smarty->assign('error', INVALID_USER);
			$smarty->display('web/index.tpl');
            exit();
        } else {
			$uid = $result;
		}

		$result = $userManager->checkPassword($uid, $formpass);

		if (!$result) {
			$smarty->assign('error', INVALID_LOGIN);
			$smarty->display('web/login.tpl');
            exit();
		}
        
		$userData = $userManager->getAllUserData($uid);

        $_SESSION['last_login'] = formatDateTime($userData['last_login']);
        $_SESSION['credit'] = $userData['credit'];
        $_SESSION['username'] = $userData['forename'].' '.$userData['name'];
        $_SESSION['login_tries'] = $userData['login_tries'];
		$_SESSION['uid'] = $uid;
        $_SESSION['last_action'] = time();
        $_SESSION['IP'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];

        //Successfully logged in
        $login = True;
    }
    else {
        $smarty->display('web/login.tpl'); 
    }

?>