<?php

class AdminAdminInterface extends AdminInterface {
	function __construct($mod_path) {
		parent::__construct($mod_path);
		$this->parentPath = $this->tplFilePath . 'mod_admin_header.tpl';
		$this->smarty->assign('adminParent', $this->parentPath);
	}

	function CreateAdmin($admin_group_arr) {
		$this->smarty->assign('admin_groups', $admin_group_arr);
		$this->smarty->display($this->tplFilePath . 'addAdmin.tpl');
	}

	function CreateAdminGroup($mod_array) {
		$this->smarty->assign('modules', $mod_array);
		$this->smarty->display($this->tplFilePath . 'addAdminGroup.tpl');
	}

	function ShowAdmin($admins) {
		$this->smarty->assign('admins', $admins);
		$this->smarty->display($this->tplFilePath . 'show_admins.tpl');
	}

	function ShowAdminGroup($admingroups) {
		$this->smarty->assign('admingroups', $admingroups);
		$this->smarty->display($this->tplFilePath . 'show_admin_groups.tpl');
	}

	function CreateAdminFin($adminname, $str_admingroup) {
		$msg = sprintf('Der Administrator %s, der zu der Gruppe %s gehört, wurde erfolgreich hinzugefügt.', $adminname,
					   $str_admingroup);
		$this->dieMsg($msg);
	}

	function ChangeAdmin($ID, $name, $groups,$active_group) {
		$this->smarty->assign('ID', $ID);
		$this->smarty->assign('name', $name);
		$this->smarty->assign('groups', $groups);
		$this->smarty->assign('active_group', $active_group);
		$this->smarty->display($this->tplFilePath . 'change_admin.tpl');
	}

	function ChangeAdminFin($ID, $name, $GID) {
		$msg = sprintf('Der Administrator %s mit der ID %s und der GID %s wurde erfolgreich geändert.', $name, $ID,
					   $GID);
		$this->dieMsg($msg);
	}

	function ConfirmDeleteAdmin($ID, $adminname) {
		$this->smarty->assign('ID', $ID);
		$this->smarty->assign('name', $adminname);
		$this->smarty->display($this->tplFilePath . 'confirm_delete_admin.tpl');
	}

	function ConfirmDeleteAdminGroup($ID, $admingroup_name) {
		$this->smarty->assign('name', $admingroup_name);
		$this->smarty->assign('ID', $ID);
		$this->smarty->display($this->tplFilePath . 'confirm_delete_admingroup.tpl');
	}

	function ConfirmAddAdmingroup($admingroup_name) {

		$this->smarty->assign('name', $admingroup_name);
		$this->smarty->display($this->tplFilePath . 'confirm_add_admingroup.tpl');
	}

	function SelectionMenu($arr_action) {
		$this->smarty->assign('action', $arr_action);
		$this->smarty->display($this->tplFilePath . 'index.tpl');
	}
}

?>