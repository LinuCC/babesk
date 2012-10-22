<?php

require_once PATH_INCLUDE . '/Module.php';

class ChangePassword extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	private $smartyPath;
	
	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
		$this->smartyPath = PATH_SMARTY . '/templates/web' . $path;
	}
	
	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute() {
		//No direct access
		defined('_WEXEC') or die("Access denied");
		require_once 'change_password_constants.php';
		require_once PATH_INCLUDE.'/functions.php';
		global $smarty;
		global $logger;
		$userManager = new UserManager();
		try {
			$userData = $userManager->getEntryData($_SESSION['uid'], '*');
		} catch (Exception $e) {
			$logger->log('WEB|change_password', 'MODERATE', sprintf('Unable to get Entry Data; UID:%s; %s',
					$_SESSION['uid'], $e->getMessage()));
			die(ERR);
		}
		$smarty->assign('username', $userData['forename'].' '.$userData['name']);
		if(isset($userData['credit']))
			$smarty->assign('credit', $userData['credit']);
		$smarty->assign('last_login', $userData['last_login']);
		
		if(isset($_POST['passwd'])) {
			if (!isset($_POST['passwd']) and !isset($_POST['passwd_repeat'])) {
				die(INVALID_FORM);
			}
			$passwd = '';
			if(($passwd = $_POST['passwd']) != $_POST['passwd_repeat']) {
				$smarty->assign('status', UNMATCHED_PASSWORDS);
				$smarty->display($this->smartyPath . 'change_password.tpl');
				exit();
			}
			if(!preg_match('/\A^[a-zA-Z0-9 _-]{4,20}\z/',$passwd)){
				$smarty->assign('status', CH_P_WRONG_PW);
				$smarty->display($this->smartyPath . 'change_password.tpl');
				exit();
			}
			else if($userData['password'] == hash_password($passwd)) {
				$smarty->assign('status', CH_P_OLD_PW);
				$smarty->display($this->smartyPath . 'change_password.tpl');
				exit();
			}
		
			$userManager->updatePassword($_SESSION['uid'], $passwd);
				$smarty->assign('status', '<p>Passwort wurde erfolgreich ge&auml;ndert!</p>');
				$smarty->display($this->smartyPath . 'change_password_success.tpl');
		}
		else {
			$smarty->display($this->smartyPath . 'change_password.tpl');
			exit();
		}
	}
}
?>