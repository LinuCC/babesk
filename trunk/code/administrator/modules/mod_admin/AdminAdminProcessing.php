<?php
/**
 * This class processes the Administrator-module
 * AdminAdminProcessing chooses which formular to show and connects to the access-files
 * to change data in the MySQL-Server.
 * @author voelkerball
 *
 */
class AdminAdminProcessing {
	function __construct() {
		require_once PATH_ACCESS . '/admin_access.php';
		require_once PATH_ACCESS . '/admin_group_access.php';
		require_once 'AdminAdminInterface.php';
		$this->adminInterface = new AdminAdminInterface();
		$this->adminManager = new AdminManager();
		$this->admingroupManager = new AdminGroupManager();
		$this->messages = array('err_inp_gname' => 'Der Gruppenname wurde falsch eingegeben.',
								'err_inp' => 'Ein Wert wurde falsch eingegeben',
								'err_add_admin' => 'Der Administrator konnte nicht hinzugefügt werden!',
								'err_get_groupname' => 'Der ID konnte kein Gruppenname zugeordnet werden.',
								'err_mysql' => 'Konnte die Daten nicht vom MySQL-Server holen',
								'err_del_admingroup' => 'Konnte die Admingruppe nicht löschen',
								'err_init_admingroup' => 'Ein Fehler ist beim initialisieren der AdminGruppen aufgetreten',
								'err_del_admin' => 'Konnte den Administrator nicht löschen',
								'err_change_admin' => 'Ein Fehler ist beim ändern des Administrators aufgetreten',
								'del_admingroup_fin' => 'Die Administratorgruppe wurde erfolgreich gelöscht.',
								'del_admin_fin' => 'Der Administrator wurde erfolgreich gelöscht.');
	}
	
	/**
	 * The function adds an entry to the MySQL-AdminTable
	 * 
	 * @param string $name The name of the admin 
	 * @param string $password The admin-Password
	 * @param numeric_string $agid The adminGroup-ID, to which the admin belongs to
	 */
	function addAdmin($name, $password, $agid) {
		if($name && $password && $agid) {
			try {
				inputcheck($name, 'name');
				inputcheck($password, 'password');
				inputcheck($agid, 'id');

			} catch (Exception $e) {
				//wrong input!!
				die_error($this->messages['err_inp'].':'.$e->getMessage());
			}
			try {
				$this->adminManager->addAdmin($name, hash_password($password), $agid);
			} catch (Exception $e) {
				$this->adminInterface->ShowError($this->messages['err_add_admin'].':'.$e->getMessage());
			}
			$admingroupname = $this->admingroupManager->getEntryData($agid);
			$this->adminInterface->CreateAdminFin($name, $admingroupname['name']);
		}
		else {
			try {
				$admin_groups = $this->admingroupManager->getTableData();
			} catch (Exception $e) {
				$this->adminInterface->ShowError($this->messages['err_mysql'].':'.$e->getMessage());
			}
			$smarty_admin_groups = array();
			foreach($admin_groups as $a_group) {
				if(!isset($a_group['name'])) {
					$this->adminInterface->ShowError($this->messages['err_init_admingroup']);
					continue;
				}
				$smarty_admin_groups[$a_group['ID']] = $a_group['name'];
			}
			$this->adminInterface->CreateAdmin($smarty_admin_groups);
		}
	}

	/**
	 * adds an AdministratorGroup to the MySQL-table
	 * Enter description here ...
	 * @param string $name The name of the Group
	 * @param array[numeric_string] $module_ids 
	 */
	function addAdminGroup($name, $module_ids) {
		if(!$name || !$module_ids) {
			global $modManager;
			$modules = $modManager->getModules();
			$this->adminInterface->CreateAdminGroup($modules);
		}
		else {
			global $modManager;
			$allowed_modules = array();
			foreach($module_ids as $m_id) {
				$allowed_modules[] = $modManager->getModuleName($m_id);
			}
			try {
				inputcheck($name, 'name');
			} catch (Exception $e) {
				$this->adminInterface->ShowError($message['err_inp_gname']);
				die();
			}
			$module_str = implode(', ', $allowed_modules);
			if($this->admingroupManager->addAdminGroup($name, $module_str)) {
				$this->adminInterface->ConfirmAddAdminGroup($name);
			
			}
		}
	}
	
	/**
	 * deletes an Administrator-entry from the MySQL-table
	 * Enter description here ...
	 * @param numeric_string $ID The ID of the Administrator to delete
	 * @param boolean $confirm If false, it will show an deletion-confirmation first
	 */
	function deleteAdmin($ID, $confirm) {
		if($confirm) {
			try {
				$this->adminManager->delEntry($ID);
			} catch (Exception $e) {
				$this->adminInterface->ShowError($this->messages['err_del_admin'] . ':' . $e->getMessage());
				die();
			}
			echo $this->messages['del_admin_fin'];
		} else {
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
	function deleteAdminGroup($ID, $confirm) {
		if($confirm) {
			try {
				$this->admingroupManager->delEntry($ID);
			} catch (Exception $e) {
				$this->adminInterface->ShowError($this->messages['err_del_admingroup'] . ':' . $e->getMessage());
				die();
			}
			echo $this->messages['del_admingroup_fin'];
		} else {
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
	function ShowAdmins() {
		try {
			$admins = $this->adminManager->getTableData();
		} catch (Exception $e) {
			$this->adminInterface->ShowError($this->messages['err_mysql']);
			die();
		}
		foreach($admins as &$admin) {
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
	function ShowAdminGroups() {
		try {
			$admingroups = $this->admingroupManager->getTableData();
		} catch (Exception $e) {
			$this->adminInterface->ShowError($this->messages['err_mysql']);
			die();
		}
		$this->adminInterface->ShowAdminGroup($admingroups);
	}
	
	/**
	 * Changes Data of an Admin
	 * Enter description here ...
	 */
	function ChangeAdmin($old_ID, $ID, $name, $password, $gid) {
		if(isset($old_ID, $ID, $name, $gid)) {
			try {
				inputcheck($old_ID, 'id');
				inputcheck($ID, 'id');
				inputcheck($name, 'name');
				inputcheck($gid, 'id');
			} catch (Exception $e) {
				$this->adminInterface->ShowError($this->messages['err_inp'].' - Eingabe:'.$e->getMessage());
				die();
			}
			try {
				if(isset($password) && $password != '') {
					try {
						inputcheck($password, 'password');
					} catch (Exception $e) {
						$this->adminInterface->ShowError($this->messages['err_inp'].' - Eingabe:'.$e->getMessage());
						die();
					}
					$this->adminManager->alterEntry($old_ID, 'ID', $ID, 'name', $name, 
													'password', hash_password($password), 'GID', $gid);			
				} else {
					$this->adminManager->alterEntry($old_ID, 'ID', $ID, 'name', $name, 
													'GID', $gid);			
				}
				$this->adminInterface->ChangeAdminFin($ID, $name, $gid);
			} catch (Exception $e) {
				$this->adminInterface->ShowError($this->messages['err_change_admin'].':'.$e->getMessage());
				///@todo log the error
			}
		} 
		else if(isset($old_ID)){
			$admingroups = $this->admingroupManager->getTableData();
			$admin = $this->adminManager->getEntryData($old_ID);
			$this->adminInterface->ChangeAdmin($old_ID, $admin['name'], $admingroups);
		}
	}
	
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