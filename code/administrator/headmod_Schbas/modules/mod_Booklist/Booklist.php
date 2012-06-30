<?php

require_once PATH_INCLUDE . '/Module.php';

class Booklist extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	
	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
	}
	
	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute() {
		
		defined('_AEXEC') or die('Access denied');
		
		require_once 'AdminActivateInterface.php';
		require_once 'AdminActivateProcessing.php';
		
		$userInterface = new AdminActivateInterface($this->relPath);
		$userProcessing = new AdminActivateProcessing($userInterface);
		
		if ('POST' == $_SERVER['REQUEST_METHOD']) {
			$action = $_GET['action'];
			switch ($action) {
				case 1: //register user
					if (isset($_POST['forename'], $_POST['name'], $_POST['username'])) {
						//form is filled out, register the user
						try {
							$userProcessing->RegisterUser($_POST['forename'], $_POST['name'], $_POST['username'], $_POST[
									'passwd'], $_POST['passwd_repeat'], $_SESSION['CARD_ID'], $_POST["Date_Year"] . '-' . $_POST[
									"Date_Month"] . '-' . $_POST["Date_Day"], $_POST["gid"], $_POST["credits"]);
						} catch (Exception $e) {
							$userInterface->dieError($e->getMessage());
						}
					} else if (isset($_POST['id'])) {
						//id is already filled out, show register-form
						$ar_groups = $userProcessing->getGroups();
						$_SESSION['CARD_ID'] = $_POST['id'];
						$userInterface->ShowRegisterForm($ar_groups['arr_gid'], $ar_groups['arr_group_name']);
					} else {
						//show card-id-form
						$userInterface->ShowCardidInput();
					}
					break;
				case 2: //show the users
					$userProcessing->ShowUsers(false);
					break;
				case 3: //delete the user
					if (isset($_POST['delete'])) {
						$userProcessing->DeleteUser($_GET['ID']);
					} else if (isset($_POST['not_delete'])) {
						$userInterface->ShowSelectionFunctionality();
					} else {
						$userProcessing->DeleteConfirmation($_GET['ID']);
					}
					break;
				case 4:
					if (!isset($_POST['id'], $_POST['forename'], $_POST['name'], $_POST['username'], $_POST['credits'], $_POST[
					'gid'])) {
					$userProcessing->ChangeUserForm($_GET['ID']);
					} else {
						$soli = 0;
						if (isset($_POST['soliAccount'])) {
							$soli = 1;
						}
						if (isset($_POST['lockAccount'])) {
							$userProcessing->ChangeUser($_GET['ID'], $_POST['id'], $_POST['forename'], $_POST['name'], $_POST[
									'username'], $_POST['passwd'], $_POST['passwd_repeat'], $_POST['Date_Year'] . '-' . $_POST[
									'Date_Month'] . '-' . $_POST['Date_Day'], $_POST['gid'], $_POST['credits'], 1, @$_POST[
									'cardnumber'], $soli);
						} else {
							$userProcessing->ChangeUser($_GET['ID'], $_POST['id'], $_POST['forename'], $_POST['name'], $_POST[
									'username'], $_POST['passwd'], $_POST['passwd_repeat'], $_POST['Date_Year'] . '-' . $_POST[
									'Date_Month'] . '-' . $_POST['Date_Day'], $_POST['gid'], $_POST['credits'], 0, @$_POST[
									'cardnumber'], $soli);
						}
					}
					break;
			}
		} else {
			$userInterface->ShowSelectionFunctionality();
		}
	}
}

?>