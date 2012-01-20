<?php

require_once 'AdminUserInterface.php';

class AdminUserProcessing {
	function __construct() {
		$this->userInterface = new AdminUserInterface();
		$this->messages = array('error' => array(
									'max_credits' => 'Maximales Guthaben der Gruppe überschritten.',
									'mysql_register' => 'Problem bei dem Versuch, den neuen Benutzer in MySQL einzutragen.',
									'input1' => 'Ein Feld wurde falsch mit ',
									'input2' => ' ausgefüllt',
									'uid_get_param' => 'Die Benutzer-ID (UID) vom GET-Parameter ist falsch: Der Benutzer ist nicht vorhanden!',
									'groups_get_param' => 'Ein Fehler ist beim holen der Gruppen aufgetreten.',
									'delete' => 'Ein Fehler ist beim löschen des Benutzers aufgetreten:',
									'add_cardid' => 'Konnte die Karten-ID nicht hinzufügen. Vorgang abgebrochen.',
									'register' => 'Konnte den Benutzer nicht hinzufügen!',
									'change' => 'Konnte den Benutzer nicht ändern!',
									'passwd_repeat' => 'das Passwort und das wiederholte Passwort stimmen nicht überein',
									'user_existing' => ' der Benutzer ist schon vorhanden.'),
								'notice' => array(
									'please_repeat' => 'Bitte wiederholen sie den Vorgang.'));
	}
	//////////////////////////////////////////////////
	//--------------------Register--------------------
	//////////////////////////////////////////////////
	/**
	* Registers an User
	* RegisterUser Registers an user by adding a MySQL-usertable-entry
	* @param string $forename The forename of the user
	* @param string $name The name of the user
	* @param string $username The username (to login) of the user
	* @param string $passwd The password of the user
	* @param string $passwd_repeat The reapeated password, to make sure its spelled right
	* @param number $cardID The Card-id the user has
	* @param string $birthday The Birthday of the user. Format: YYYY-MM-DD
	* @param number $GID The Id of the Group the user is in
	* @param string $credits How much credits the user has
	* @throws Exception if something gone wrong
	*/
	function RegisterUser($forename,$name,$username,$passwd,$passwd_repeat,$cardID,$birthday,$GID,$credits) {
		require_once PATH_INCLUDE."/user_access.php";
		require_once PATH_INCLUDE.'/card_access.php';
		require_once PATH_INCLUDE.'/group_access.php';
		require_once PATH_INCLUDE."/logs.php";

		$userManager = new UserManager();
		$cardManager = new CardManager();
		$groupManager = new GroupManager();
		$logger= new Logger;

		//checks the input for wrong Characters etc
		try {
			inputcheck($forename, 'name');
			inputcheck($name, 'name');
			inputcheck($username, 'name');
			inputcheck($passwd, 'password');
			inputcheck($passwd_repeat, 'password');
			inputcheck($cardID, 'card_id');
			inputcheck($birthday, 'birthday');
			inputcheck($GID, 'id');
			inputcheck($credits, 'credits');
			
		} catch (Exception $e) {
			$this->userInterface->ShowError($this->messages['error']['input1'].'"'.$e->getMessage().'"'.
			$this->messages['error']['input2']);
			$this->userInterface->ShowRepeatRegister();
			throw new Exception($this->messages['error']['register']);
		}
		if($cardManager->is_card_existing($cardID) || $userManager->isUserExisting($forename, $name, $username)) {
			throw new Exception($this->messages['error']['register'].$this->messages['error']['user_existing']);
		}
		//check max amount of credits of the group
		if($credits > $groupManager->getMaxCredit($GID)) {
			$this->userInterface->ShowError($this->messages['error']['max_credits'].'maximales Guthaben der Gruppe:'.$groupManager->getMaxCredit($GID).'€');
			throw new Exception($this->messages['error']['register']);
		}
		try {
			$userManager->addUser($name, $forename, $username, $passwd, $birthday, $credits, $GID);
		} catch (Exception $e) {
			$this->userInterface->ShowError("<br>".$this->messages['error']['mysql_register'] .$e->getMessage()."<br>");
			$cardManager->delEntry($cardID);
			throw new Exception($this->messages['error']['register'].$e->getMessage());
		}
		try {
			$cardManager->addCard($cardID, $userManager->getUserID($username));
		} catch (Exception $e) {
			$userManager->delEntry($userManager->getUserID($username));//user has no cardID, delete him
			throw new Exception($this->messages['error']['add_cardid'].$e->getMessage());
		}
		$this->userInterface->ShowRegisterFin($name, $forename);
			
		$_SESSION['CARD_ID'] = NULL;
		// 		echo "<br><b>Hallo ".$name."!</b><br>";
		// 		echo '<a href="index.php?'.htmlspecialchars(SID).'">Zur&uuml;ck zum Admin Bereich</a>';
		$logger->log(USERS,NOTICE,"REG_ADDED_USER-ID:".$cardID."-NAME:".$name."-FORENAME:".$forename."-BIRTHDAY:".
		$birthday."-CREDITS:".$credits."-GID:".$GID."-");
	}

	function getGroups() {
		require_once PATH_INCLUDE."/group_access.php";

		$group_manager = new GroupManager('groups');

		$arr_group_id = array();
		$arr_group_name = array();

		$sql_groups = $group_manager->getTableData();
		if(!empty($sql_groups)){
			foreach($sql_groups as $group) {
				$arr_group_id[] = $group["ID"];
				$arr_group_name[] = $group["name"];
			}
		}
		return array('arr_gid' => $arr_group_id,
						'arr_group_name' => $arr_group_name);
		// 		$smarty->assign('gid', $arr_group_id);
		// 		$smarty->assign('g_names', $arr_group_name);
	}
	//////////////////////////////////////////////////
	//--------------------Show Users--------------------
	//////////////////////////////////////////////////
	function ShowUsers($filter) {
		require_once PATH_INCLUDE.'/user_access.php';
		require_once PATH_INCLUDE.'/group_access.php';

		$userManager = new UserManager();
		$groupManager = new GroupManager();

		$groups = $groupManager->getTableData();
		$users = $userManager->getTableData();

		foreach($users as &$user) {
			$is_named = false;
			foreach ($groups as $gn) {
				if($gn['ID'] == $user['GID']) {
					$user['groupname'] = $gn['name'];
					$is_named = true;
					break;
				}
			}
			$is_named or $user['groupname'] = 'Error: This group is non-existent!';
		}

		$this->userInterface->ShowUsers($users);
	}
	//////////////////////////////////////////////////
	//--------------------Delete User--------------------
	//////////////////////////////////////////////////
	/**
	* Shows the confirm-deletion-dialog
	* Enter description here ...
	* @param number $uid the UserID
	*/
	function DeleteConfirmation($uid) {
		require_once PATH_INCLUDE.'/user_access.php';

		$userManager = new UserManager();

		try {
			$user = $userManager->getEntryData($uid, 'forename', 'name');
		} catch (Exception $e) {
			var_dump($uid);
			$this->userInterface->ShowError($this->messages['error']['uid_get_param'].';<br>ExceptionMessage:'.$e->getMessage());
			die();
		}

		$this->userInterface->ShowDeleteConfirmation($uid, $user['forename'] , $user['name']);
	}

	function DeleteUser($uid) {
		require_once PATH_INCLUDE.'/user_access.php';
		$userManager = new UserManager();
		try {
			$userManager->delEntry($uid);
		} catch (Exception $e) {
			$this->userInterface->ShowError($this->messages['error']['delete'].$e->getMessage());
		}
		$this->userInterface->ShowDeleteFin();
	}

	/**
	 * This function prepares and shows the ChangeUser-Form
	 * Enter description here ...
	 * @param string (numeric) $uid The ID of the User
	 */
	function ChangeUserForm($uid) {
		require_once PATH_INCLUDE.'/user_access.php';
		require_once PATH_INCLUDE.'/group_access.php';
		require_once PATH_INCLUDE.'/card_access.php';
		$cardManager = new CardManager();
		$userManager = new UserManager();
		$groupManager = new GroupManager();
		$cardnumber = $cardManager->getCardnumberByUserID($_GET['ID']);
		try {
			$user = $userManager->getEntryData($uid);
		} catch (Exception $e) {
			$this->userInterface->ShowError($this->messages['error']['uid_get_param'].$e->getMessage());
		}
		try {
			$groups = $this->getGroups();
		} catch (Exception $e) {
			$this->userInterface->ShowError($this->messages['error']['groups_get_param'].$e->getMessage());
		}

		$this->userInterface->ShowChangeUser($user, $groups['arr_gid'], $groups['arr_group_name'], $cardnumber);
	}

	/**
	 * This function changes the user by the given parameters.
	 * This function just alters the entries of the table, so if
	 * unchanged arguments are given these will be not changed.
	 * @param numeric string $old_id The ID of the object to change
	 * @param numeric string $id The new ID of the object
	 * @param string $forename The new forename of the object
	 * @param string $name The new name of the object
	 * @param string $username The new username of the object
	 * @param string $passwd The new password of the object
	 * @param string $passwd_repeat The repition of the password
	 * @param string $birthday The new birthday of the object. Format YYYY-MM-DD
	 * @param numeric string $GID The new Group-ID for the object
	 * @param unknown_type $credits
	 * @param boolean $locked is 1 if account is locked
	 * @throws Exception
	 */
	function ChangeUser($old_id, $id, $forename,$name,$username,$passwd,$passwd_repeat,$birthday,$GID,$credits,$locked, $cardnumber) {
		require_once PATH_INCLUDE.'/user_access.php';
		require_once PATH_INCLUDE.'/card_access.php';
		$userManager = new UserManager();
		$cardManager = new CardManager();
		try {
			inputcheck($forename, 'name');
			inputcheck($name, 'name');
			inputcheck($username, 'name');
			inputcheck($birthday, 'birthday');
			inputcheck($GID, 'id');
			inputcheck($credits, 'credits');
			if($cardnumber)
				inputcheck($cardnumber, 'card_id');
		} catch (Exception $e) {
			$this->userInterface->ShowError($this->messages['error']['input1'].'"'.$e->getMessage().'"'.
			$this->messages['error']['input2']);
			die();
			//throw new Exception($this->messages['error']['change']);
		}
		if(isset($passwd, $passwd_repeat)) {
			if($passwd != $passwd_repeat) {
				try {
					inputcheck($passwd, 'password');
					inputcheck($passwd_repeat, 'password');
				} catch (Exception $e) {
					$this->userInterface->ShowError($this->messages['error']['passwd_repeat']);
					die();
				}
			}
		}
		try {
			$userManager->alterUser($old_id, $id, $name, $forename, $username, hash_password($passwd), $birthday, $credits, $GID, $locked);
			if($cardnumber)$cardManager->changeCardnumber($cardManager->getIDByUserID($id), $cardnumber);
		} catch (Exception $e) {
			$this->userInterface->ShowError($this->messages['error']['change'].$e->getMessage());
			die();
		}
		$this->userInterface->ShowChangeUserFin($id, $name, $forename, $username, $birthday, $credits, $GID, $locked);
	}

	var $messages = array();
	private $userInterface;
}


?>