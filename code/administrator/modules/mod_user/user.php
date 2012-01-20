<?php
require_once 'AdminUserInterface.php';
require_once 'AdminUserProcessing.php';

defined('_AEXEC') or die('Access denied');
$user_processing = new AdminUserProcessing();
$user_interface = new AdminUserInterface();

if ('POST' == $_SERVER['REQUEST_METHOD']) {
	$action = $_GET['action'];
	switch ($action) {
		case 1: //register user
			if(isset($_POST['forename'], $_POST['name'], $_POST['username'])) {
				//form is filled out, register the user
				try {
					$user_processing->RegisterUser($_POST['forename'] , $_POST['name'],
					$_POST['username'], $_POST['passwd'],
					$_POST['passwd_repeat'], $_SESSION['CARD_ID'],
					$_POST["Date_Year"].'-'.$_POST["Date_Month"].'-'.$_POST["Date_Day"],
					$_POST["gid"], $_POST["credits"]);
				} catch (Exception $e) {
					$user_interface->ShowError($e->getMessage());
				}
			} else if(isset($_POST['id'])) {
				//id is already filled out, show register-form
				$ar_groups = $user_processing->getGroups();
				$_SESSION['CARD_ID'] = $_POST['id'];
				$user_interface->ShowRegisterForm($ar_groups['arr_gid'], $ar_groups['arr_group_name']);
			} else {
				//show card-id-form
				$user_interface->ShowCardidInput();
			}
			break;
		case 2://show the users
			$user_processing->ShowUsers(false);
			break;
		case 3://delete the user
			if(isset($_POST['delete'])) {
				$user_processing->DeleteUser($_GET['ID']);
			}
			else if(isset($_POST['not_delete'])) {
				$user_interface->ShowSelectionFunctionality();
			}
			else {
				$user_processing->DeleteConfirmation($_GET['ID']);
			}
			break;
		case 4:
			if(!isset($_POST['id'], $_POST['forename'], $_POST['name'], $_POST['username'], $_POST['credits'], $_POST['gid'])) {
				$user_processing->ChangeUserForm($_GET['ID']);
			}
			else {
				if(isset ($_POST['lockAccount'])) {
					$user_processing->ChangeUser($_GET['ID'], $_POST['id'], $_POST['forename'], $_POST['name'],
					$_POST['username'], $_POST['passwd'], $_POST['passwd_repeat'], $_POST['Date_Year'].'-'.$_POST['Date_Month'].'-'.$_POST['Date_Day'], $_POST['gid'], $_POST['credits'],1, @$_POST['cardnumber']);
				} else {
					$user_processing->ChangeUser($_GET['ID'], $_POST['id'], $_POST['forename'], $_POST['name'],
					$_POST['username'], $_POST['passwd'], $_POST['passwd_repeat'], $_POST['Date_Year'].'-'.$_POST['Date_Month'].'-'.$_POST['Date_Day'], $_POST['gid'], $_POST['credits'],0, @$_POST['cardnumber']);
				}
			}
			break;
	}
}
else {
	$user_interface->ShowSelectionFunctionality();
}

?>