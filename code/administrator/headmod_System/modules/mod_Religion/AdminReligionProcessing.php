<?php
class AdminReligionProcessing {
	function __construct($ReligionInterface) {

		$this->ReligionInterface = $ReligionInterface;
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
						'user_existing' => ' der Benutzer ist schon vorhanden oder die Kartennummer wird schon benutzt.'),
				'get_data_failed' => 'Ein Fehler ist beim fetchen der Daten aufgetreten',
				'notice' => array('please_repeat' => 'Bitte wiederholen sie den Vorgang.'));
	}
	
	function EditReligions($editOrShow) {
		
		require_once PATH_ACCESS . '/GlobalSettingsManager.php';
		
		$globalSettingsManager = new globalSettingsManager();
		
		
		if(!$editOrShow) {
			$religions = $globalSettingsManager->getReligion();
			$religions_exploded = explode("|", $religions);
			$this->ReligionInterface->ShowReligions($religions_exploded);
		}
		else {
			$religions="";
			for ($i = 1; $i <= $editOrShow['relcounter']; $i++) {
				if (!$editOrShow['rel'.$i]=="") {
					$religions.=$editOrShow['rel'.$i]."|";
				}	
			}
			if(sizeof($religions)>0) $religions = substr($religions, 0,strlen($religions)-1); 
			$globalSettingsManager->setReligion($religions);
			$this->ReligionInterface->ShowReligionsSet($religions);
		}
		
	}
	
	

	//////////////////////////////////////////////////
	//--------------------Show Users--------------------
	//////////////////////////////////////////////////
	function ShowUsers($filter) {
		require_once PATH_ACCESS . '/UserManager.php';
		require_once PATH_ACCESS . '/GroupManager.php';
		require_once PATH_ACCESS . '/GlobalSettingsManager.php';
		
		$globalSettingsManager = new globalSettingsManager();
		$userManager = new UserManager();
		$groupManager = new GroupManager();

		try {
			$groups = $groupManager->getTableData();
			//$users = $userManager->getTableData();
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
		$religions = $globalSettingsManager->getReligion();
		$religions_exploded = explode("|", $religions);
		$navbar = navBar($showPage, 'users', 'Religion', '3',$filter);
		$this->ReligionInterface->ShowUsers($users,$religions_exploded,$navbar);
	}

	
	function SaveUsers($post_vars) {
		require_once PATH_ACCESS . '/UserManager.php';
		$userManager = new UserManager();
		foreach($post_vars as $key => $value) {
			try {
				$userManager->SetReligion($key, $value);		
			} catch (Exception $e) {
				$this->userInterface->dieError($this->messages['error']['change'] . $e->getMessage());
			}
		}
		$this->ReligionInterface->ShowUsersSuccess();
	}
	

	

	var $messages = array();
	private $userInterface;

	/**
	 *@var Logger
	 */
	protected $logs;
}

?>