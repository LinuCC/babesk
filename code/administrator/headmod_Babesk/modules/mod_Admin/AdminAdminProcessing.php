<?php
/**
 * This class processes the Administrator-module
 * AdminAdminProcessing chooses which formular to show and connects to the access-files
 * to change data in the MySQL-Server.
 * @author voelkerball
 *
 */
class AdminAdminProcessing {

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	function __construct ($adminInterface) {
		require_once PATH_ACCESS . '/AdminManager.php';
		require_once PATH_ACCESS . '/AdminGroupManager.php';
		require_once 'AdminAdminInterface.php';
		$this->adminInterface = $adminInterface;
		$this->adminManager = new AdminManager();
		$this->admingroupManager = new AdminGroupManager();
		$this->messages = array('err_inp_gname'			 => 'Der Gruppenname wurde falsch eingegeben.',
			'err_inp'				 => 'Ein Wert wurde falsch eingegeben',
			'err_add_admin'			 => 'Der Administrator konnte nicht hinzugefügt werden!',
			'err_get_groupname'		 => 'Der ID konnte kein Gruppenname zugeordnet werden.',
			'err_get_admingroups'	 =>
				'Die Administratorgruppe konnte nicht abgerufen werden, der Administrator wurde aber hinzugefügt.',
			'err_mysql'				 => 'Konnte die Daten nicht vom MySQL-Server holen',
			'err_add_admingroup'	 => 'Konnte die Administratorgruppe nicht hinzufügen',
			'err_del_admingroup'	 => 'Konnte die Admingruppe nicht löschen',
			'err_init_admingroup'	 => 'Ein Fehler ist beim initialisieren der AdminGruppen aufgetreten',
			'err_no_modules'		 => 'Es sind keine Module vorhanden!',
			'err_del_admin'			 => 'Konnte den Administrator nicht löschen',
			'err_change_admin'		 => 'Ein Fehler ist beim ändern des Administrators aufgetreten',
			'del_admingroup_fin'	 => 'Die Administratorgruppe wurde erfolgreich gelöscht.',
			'err_get_modules'		 => 'Ein Fehler ist beim Abrufen der Module aufgetreten.',
			'field_password'		 => 'Passwort',
			'field_groupid'			 => 'Gruppen-ID',
			'field_name'			 => 'Name',
			'field_id'				 => 'ID',
			'del_admin_fin'			 => 'Der Administrator wurde erfolgreich gelöscht.');
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	/**
	 * The function adds an entry to the MySQL-AdminTable
	 *
	 * @param string $name The name of the admin
	 * @param string $password The admin-Password
	 * @param numeric_string $agid The adminGroup-ID, to which the admin belongs to
	 */
	public function addAdmin ($name, $password, $agid) {

		if ($name && $password && $agid) {

			$this->CheckInputAddUser($name, $password, $agid);
			$this->AddAdminToServer($name, $password, $agid);
			$admingroupname = $this->GetAdminGroupName($agid);
			$this->adminInterface->CreateAdminFin($name, $admingroupname);
		}
		else {

			$admin_groups = $this->GetAdminGroups();
			$this->adminInterface->CreateAdmin($this->ProcessAdminGroupsForSmarty($admin_groups));
		}
	}

	/**
	 * adds an AdministratorGroup to the MySQL-table
	 * Enter description here ...
	 * @param string $name The name of the Group
	 * @param array[numeric_string] $module_ids
	 */
	public function addAdminGroup ($name, $module_ids) {

		if (!$name || !$module_ids) {

			$modules = $this->GetModules();
			if (count($modules) < 1)
				$this->adminInterface->dieError($this->messages['err_no_modules']);
				$this->adminInterface->CreateAdminGroup($modules);
		}
		else {

			$allowed_modules = $this->GetAllowedModules($module_ids);
			$module_str = implode(', ', $allowed_modules);
			$this->CheckInputGroupname($name);

			$this->AddAdminGroupToServer($name, $module_str);
			$this->adminInterface->ConfirmAddAdminGroup($name);
		}
	}

	/**
	 * deletes an Administrator-entry from the MySQL-table
	 * Enter description here ...
	 * @param numeric_string $ID The ID of the Administrator to delete
	 * @param boolean $confirm If false, it will show an deletion-confirmation first
	 */
	public function deleteAdmin ($ID, $confirm) {
		if ($confirm) {
			try {
				$this->adminManager->delEntry($ID);
			} catch (Exception $e) {
				$this->adminInterface->dieError($this->messages['err_del_admin'] . ':' . $e->getMessage());
				die();
			}
			$this->adminInterface->dieMsg($this->messages['del_admin_fin']);
		}
		else {
			try {
				$name = $this->adminManager->getAdminName($ID);
			} catch (Exception $e) {
				$name = $this->messages['err_mysql'];
			}
			$this->adminInterface->ConfirmDeleteAdmin($ID, $name);
		}
	}

	/**
	 * Handels the deletion of AdminGroups
	 * If $confirm is zero, it will show a confirmation-dialog. Else it will delete the AdminGroup
	 * by the given ID.
	 * @param numeric $ID The ID of the AdminGroup to delete (or zero)
	 */
	public function deleteAdminGroup ($ID, $confirm) {
		if ($confirm) {
			try {
				$this->admingroupManager->delEntry($ID);
			} catch (Exception $e) {
				$this->adminInterface->dieError($this->messages['err_del_admingroup'] . ':' . $e->getMessage());
				die();
			}
			echo $this->messages['del_admingroup_fin'];
		}
		else {
			try {
				$name = $this->admingroupManager->getAdminGroupName($ID);
			} catch (Exception $e) {
				$name = $this->messages['err_mysql'];
			}
			$this->adminInterface->ConfirmDeleteAdminGroup($ID, $name);
		}
	}

	/**
	 * Handels the needed information to let the adminInterface show the admins
	 * Enter description here ...
	 */
	public function ShowAdmins () {
		try {
			$admins = $this->adminManager->getTableData();
		} catch (Exception $e) {
			$this->adminInterface->dieError($this->messages['err_mysql']);
			die();
		}
		foreach ($admins as & $admin) {
			try {
				$admin['groupname'] = $this->admingroupManager->getAdminGroupName($admin['GID']);
			} catch (Exception $e) {
				$admin['groupname'] = $this->messages['err_get_groupname'];
			}
		}
		$this->adminInterface->ShowAdmin($admins);
	}

	/**
	 * Handles the needed information to let the adminInterface Show the admingroups
	 * Enter description here ...
	 */
	public function ShowAdminGroups () {

		try {
			$admingroups = $this->admingroupManager->getTableData();
		} catch (Exception $e) {
			$this->adminInterface->dieError($this->messages['err_mysql']);
			die();
		}
		$this->adminInterface->ShowAdminGroup($admingroups);
	}

	/**
	 * Changes Data of an Admin
	 * Enter description here ...
	 */
	public function ChangeAdmin ($old_ID, $ID, $name, $password, $gid) {

		if (isset($old_ID, $ID, $name, $gid)) {

			$this->CheckInputChangeAdmin($old_ID, $ID, $name, $gid);

			try {
				if (isset($password) && $password != '') {

					$this->CheckInputPasswordChangeAmin($password);
					$this->adminManager->alterEntry($old_ID, 'ID', $ID, 'name', $name, 'password', hash_password(
						$password), 'GID', $gid);
				}
				else {

					$this->adminManager->alterEntry($old_ID, 'ID', $ID, 'name', $name, 'GID', $gid);
				}
				$this->adminInterface->ChangeAdminFin($ID, $name, $gid);
			} catch (Exception $e) {

				$this->adminInterface->dieError($this->messages['err_change_admin'] . ':' . $e->getMessage());
				///@todo log the error
				}
		}
		else if (isset($old_ID)) {
			$admingroups = $this->admingroupManager->getTableData();
			$admin = $this->adminManager->getEntryData($old_ID);
			$this->adminInterface->ChangeAdmin($old_ID, $admin['name'], $admingroups);
		}
	}

	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	private function CheckInputAddUser ($name, $password, $agid) {
		try {
			inputcheck($name, 'name');
			inputcheck($password, 'password');
			inputcheck($agid, 'id');

		} catch (Exception $e) {
			$this->adminInterface->dieError($this->messages['err_inp'] . ':' . $e->getMessage());
		}
	}

	private function ProcessAdminGroupsForSmarty ($admin_groups) {

		$smarty_admin_groups = array();
		foreach ($admin_groups as $a_group) {
			if (!isset($a_group['name'])) {
				$this->adminInterface->dieError($this->messages['err_init_admingroup']);
				continue;
			}
			$smarty_admin_groups[$a_group['ID']] = $a_group['name'];
		}
		return $smarty_admin_groups;
	}

	private function AddAdminToServer ($name, $password, $agid) {

		try {
			$this->adminManager->addAdmin($name, hash_password($password), $agid);
		} catch (Exception $e) {
			$this->adminInterface->dieError($this->messages['err_add_admin'] . ':' . $e->getMessage());
		}
	}

	private function AddAdminGroupToServer ($name, $module_str) {
		try {
			$this->admingroupManager->addAdminGroup($name, $module_str);
		} catch (Exception $e) {
			$this->adminInterface->dieError($this->messages['err_add_admingroup'] . $e->getMessage());
		}
	}

	private function GetAdminGroupName ($agid) {

		try {
			$admingroupname = $this->admingroupManager->getAdminGroupName($agid);
		} catch (Exception $e) {
			$this->adminInterface->dieError($this->msg['err_get_admingroups'] . ':' . $e->getMessage());
		}
		return $admingroupname;
	}

	private function GetAdminGroups () {

		try {
			$admin_groups = $this->admingroupManager->getTableData();
		} catch (Exception $e) {
			$this->adminInterface->dieError($this->messages['err_mysql'] . ':' . $e->getMessage());
		}
		return $admin_groups;
	}

	private function GetModules () {

		global $modManager;
		$modules = array();
		try {
			$module_arr = $modManager->getAllModules();
		} catch (Exception $e) {
			$this->adminInterface->dieError($this->msg['err_get_modules']);
		}
		
		foreach($module_arr as $module) {
			$modules [$module->getName()] = $module->getDisplayName();
		}
		
		return $modules;
	}

	private function GetAllowedModules ($module_ids) {

		global $modManager;
		$allowed_modules = array();

		foreach ($module_ids as $m_id) {
			$allowed_modules[] = $modManager->getModuleDisplayName($m_id);
		}
		return $allowed_modules;
	}

	private function CheckInputGroupname ($name) {
		try {
			inputcheck($name, 'name');
		} catch (Exception $e) {
			$this->adminInterface->dieError($message['err_inp_gname']);
		}
	}

	private function CheckInputChangeAdmin ($old_ID, $ID, $name, $gid) {

		try {
			inputcheck($old_ID, 'id');
			inputcheck($ID, 'id', $this->messages['field_id']);
			inputcheck($name, 'name', $this->messages['field_name']);
			inputcheck($gid, 'id', $this->messages['field_groupid']);
		} catch (WrongInputException $e) {
			$this->adminInterface->dieError($this->messages['err_inp'] . ' - ' . $e->getFieldName());
		}
	}

	private function CheckInputPasswordChangeAmin ($password) {

		try {
			inputcheck($password, 'password', $this->messages['field_password']);
		} catch (Exception $e) {
			$this->adminInterface->dieError($this->messages['err_inp'] . ' - ' . $e->getFieldName());
		}
	}

	////////////////////////////////////////////////////////////////////////////////
	//Attributes

	/**
	 * @var AdminAdminInterface
	 */
	private $adminInterface;

	/**
	 * An object of the AdminManager-class, to handle MySQL
	 */
	private $adminManager;

	/**
	 * An object of the AdminGroupManager-class, to handle MySQL
	 */
	private $admingroupManager;

	/**
	 * Some constant strings showed to the user
	 * @var array[string]
	 */
	private $messages;

}

?>