<?php
class AdminUserProcessing {
	function __construct($userInterface) {

		$this->userInterface = $userInterface;
		global $logger;
		$this->logs = $logger;
		$this->messages = array(
				'error' => array('max_credits' => 'Maximales Guthaben der Gruppe überschritten.',
						'mysql_register' => 'Problem bei dem Versuch, den neuen Benutzer in MySQL einzutragen.',
						'input1' => 'Ein Feld wurde falsch mit ', 'input2' => ' ausgefüllt',
						'uid_get_param' => 'Die Benutzer-ID (UID) vom GET-Parameter ist falsch: Der Benutzer ist nicht vorhanden!',
						'groups_get_param' => 'Ein Fehler ist beim holen der Gruppen aufgetreten.',
						'delete' => 'Ein Fehler ist beim löschen des Benutzers aufgetreten:',
						'add_cardid' => 'Konnte die Karten-ID nicht hinzufügen. Vorgang abgebrochen.',
						'register' => 'Konnte den Benutzer nicht hinzufügen!',
						'change' => 'Konnte den Benutzer nicht ändern!',
						'passwd_repeat' => 'das Passwort und das wiederholte Passwort stimmen nicht überein',
						'card_id_change' => 'Warnung: Konnte den Zähler der Karten-ID nicht erhöhen.',
						'no_groups' => 'Es sind keine Gruppen vorhanden!',
						'user_existing' => ' der Benutzer ist schon vorhanden oder die Kartennummer wird schon benutzt.',
						'booklisterror' => 'Fehler beim &Uuml;berpr&uuml;fen der Schulbuchausleihe.',
				'booklist' => 'Benutzer kann nicht gel&ouml;scht werden. Es sind noch B&uuml;cher ausgeliehen!'),
				'get_data_failed' => 'Ein Fehler ist beim fetchen der Daten aufgetreten',
				'notice' => array('please_repeat' => 'Bitte wiederholen sie den Vorgang.'));
	}

	function getGroups() {

		require_once PATH_ACCESS . '/GroupManager.php';

		$group_manager = new GroupManager('groups');

		$arr_group_id = array();
		$arr_group_name = array();

		try {
			$sql_groups = $group_manager->getTableData();
		} catch (MySQLVoidDataException $e) {
			$this->userInterface->dieError($this->messages['error']['no_groups']);
		}
		if (!empty($sql_groups)) {
			foreach ($sql_groups as $group) {
				$arr_group_id[] = $group["ID"];
				$arr_group_name[] = $group["name"];
			}
		}
		return array('arr_gid' => $arr_group_id, 'arr_group_name' => $arr_group_name);
		// 		$smarty->assign('gid', $arr_group_id);
		// 		$smarty->assign('g_names', $arr_group_name);
	}
	//////////////////////////////////////////////////
	//--------------------Show Users--------------------
	//////////////////////////////////////////////////
	function ShowUsers($filter) {
		require_once PATH_ACCESS . '/UserManager.php';
		require_once PATH_ACCESS . '/GroupManager.php';

		$userManager = new UserManager();
		$groupManager = new GroupManager();

		try {
			$groups = $groupManager->getTableData();
			isset($_GET['sitePointer'])?$showPage = $_GET['sitePointer'] + 0:$showPage = 1;
			$nextPointer = $showPage*10-10;
			$users = $userManager->getUsersSorted($nextPointer,$filter);
		} catch (Exception $e) {
			$this->logs
					->log('ADMIN', 'MODERATE',
							sprintf('Error while getting Data from MySQL:%s in %s', $e->getMessage(), __METHOD__));
			$this->userInterface->dieError($this->messages['error']['get_data_failed']);
		}

		foreach ($users as &$user) {
			$is_named = false;
			foreach ($groups as $gn) {
				if ($gn['ID'] == $user['GID']) {
					$user['groupname'] = $gn['name'];
					$is_named = true;
					break;
				}
			}
			$is_named or $user['groupname'] = 'Error: This group is non-existent!';
		}
		$navbar = navBar($showPage, 'users', 'System', 'User', '2',$filter);
		$this->userInterface->ShowUsers($users,$navbar);
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

		require_once PATH_ACCESS . '/UserManager.php';

		$userManager = new UserManager();


		try {
			$user = $userManager->getEntryData($uid, 'forename', 'name');
		} catch (Exception $e) {
			$this->userInterface
					->dieError($this->messages['error']['uid_get_param'] . ';<br>ExceptionMessage:' . $e->getMessage());
		}

		$this->userInterface->ShowDeleteConfirmation($uid, $user['forename'], $user['name']);
	}


	var $messages = array();
	private $userInterface;

	/**
	 *@var Logger
	 */
	protected $logs;
}

?>