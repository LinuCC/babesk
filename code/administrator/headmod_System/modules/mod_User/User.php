<?php

require_once 'AdminUserInterface.php';
require_once 'AdminUserProcessing.php';
require_once 'UsernameAutoCreator.php';
require_once PATH_ACCESS . '/CardManager.php';
require_once PATH_ACCESS . '/UserManager.php';
require_once PATH_INCLUDE . '/Module.php';

class User extends Module {
	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
	}
	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	public function execute($dataContainer) {
		$this->entryPoint ();
		if ('POST' == $_SERVER['REQUEST_METHOD']) {
			$action = $_GET['action'];
			switch ($action) {
				case 1: //register user
					$this->userRegister ();
					break;
				case 2: //show the users
					$this->usersShow ();
					break;
				case 3: //delete the user
					$this->userDelete ();
					break;
				case 4:
					if (isset ($_POST['user_search'])) {
						try {
							$userID = $this->cardManager->getUserID($_POST['user_search']);
						} catch (Exception $e) {
							 $userID =  $e->getMessage();
						}
						if ($userID == 'MySQL returned no data!') {
						try {
							$userID = $this->userManager->getUserID($_POST['user_search']);
						} catch (Exception $e) {
							$this->userInterface->dieError("Benutzer nicht gefunden!");
						}

					}

						$this->userProcessing->ChangeUserForm($userID);

						break;
					}

					if (!isset($_POST['id'], $_POST['forename'], $_POST['name'], $_POST['username'], $_POST['credits'], $_POST[
					'gid'])) {
					$this->userProcessing->ChangeUserForm($_GET['ID']);
					} else {
						$soli = 0;
						if (isset($_POST['soliAccount'])) {
							$soli = 1;
						}
						if (isset($_POST['lockAccount'])) {
							$this->userProcessing->ChangeUser($_GET['ID'], $_POST['id'], $_POST['forename'], $_POST['name'], $_POST[
									'username'], $_POST['passwd'], $_POST['passwd_repeat'], $_POST['Date_Year'] . '-' . $_POST[
									'Date_Month'] . '-' . $_POST['Date_Day'], $_POST['gid'], $_POST['credits'], 1, @$_POST[
									'cardnumber'], $soli,$_POST['class']);
						} else {
							$this->userProcessing->ChangeUser($_GET['ID'], $_POST['id'], $_POST['forename'], $_POST['name'], $_POST[
									'username'], $_POST['passwd'], $_POST['passwd_repeat'], $_POST['Date_Year'] . '-' . $_POST[
									'Date_Month'] . '-' . $_POST['Date_Day'], $_POST['gid'], $_POST['credits'], 0, @$_POST[
									'cardnumber'], $soli,$_POST['class']);
						}
					}
					break;
				case 5:
					$this->userCreateUsernames ();
					break;
			}
		} elseif  (('GET' == $_SERVER['REQUEST_METHOD'])&&isset($_GET['action'])) {
			$action = $_GET['action'];
			switch ($action) {
				case 2: //show the users
					if (isset($_GET['filter'])) {
						$this->userProcessing->ShowUsers($_GET['filter']);
					} else {
						$this->userProcessing->ShowUsers("name");
					}
			}
		}
		else {
			$this->userInterface->ShowSelectionFunctionality();
		}
	}
	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////
	protected function entryPoint () {
		defined('_AEXEC') or die('Access denied');
		$this->cardManager = new CardManager();
		$this->userManager = new UserManager();
		$this->userInterface = new AdminUserInterface($this->relPath);
		$this->userProcessing = new AdminUserProcessing($this->userInterface);
		$this->messages = array(
				'error' => array('no_id' => 'ID nicht gefunden.'));
	}

	protected function userRegister () {
		if (isset($_POST['forename'], $_POST['name'], $_POST['username'])) {
			//form is filled out, register the user
			try {
				$this->userProcessing->RegisterUser($_POST['forename'],
					$_POST['name'], $_POST['username'], $_POST['passwd'],
					$_POST['passwd_repeat'], $_SESSION['CARD_ID'],
					$_POST["Date_Year"] . '-' . $_POST["Date_Month"] . '-' .
					$_POST["Date_Day"], $_POST["gid"], $_POST["credits"],
					$_POST["class"]);
			} catch (Exception $e) {
				$this->userInterface->dieError($e->getMessage());
			}
		} else if (isset($_POST['id'])) {
			//id is already filled out, show register-form
			$ar_groups = $this->userProcessing->getGroups();
			$_SESSION['CARD_ID'] = $_POST['id'];
			$this->userInterface->ShowRegisterForm($ar_groups['arr_gid'], $ar_groups['arr_group_name']);
		} else {
			//show card-id-form
			$this->userInterface->ShowCardidInput();
		}
	}

	protected function usersShow () {
		if (isset($_POST['filter'])) {
			$this->userProcessing->ShowUsers($_POST['filter']);
		} else {
			$this->userProcessing->ShowUsers("name");
		}
	}

	protected function userDelete () {
		if (isset($_POST['delete'])) {
			$this->userProcessing->DeleteUser($_GET['ID']);
		} else if (isset($_POST['not_delete'])) {
			$this->userInterface->ShowSelectionFunctionality();
		} else {
			$this->userProcessing->DeleteConfirmation($_GET['ID']);
		}
	}

	protected function userCreateUsernames () {
		if (isset ($_POST ['confirmed'])) {
			$creator = new UsernameAutoCreator ();
			$scheme = new UsernameScheme ();
			$scheme->templateAdd (UsernameScheme::Forename);
			$scheme->stringAdd ('.');
			$scheme->templateAdd (UsernameScheme::Name);
			$creator->usersSet ($this->userManager->getAllUsers());
			$creator->schemeSet ($scheme);
			$users = $creator->usernameCreateAll ();
			foreach ($users as $user) {
				///@FIXME: PURE EVIL DOOM LOOP OF LOOPING SQL-USE. Kill it with fire.
				$this->userManager->alterUsername ($user ['ID'], $user ['username']);
			}
			$this->userInterface->dieMsg ('Die Benutzernamen wurden erfolgreich geändert');
		}
		else {
			$this->userInterface->showConfirmAutoChangeUsernames ();
		}
	}

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////

	protected $cardManager;
	protected $userManager;
	protected $userInterface;
	protected $userProcessing;
	protected $messages;

}

?>