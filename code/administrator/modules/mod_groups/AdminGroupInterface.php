<?php

class AdminGroupInterface extends AdminInterface {
	public function __construct () {
		parent::__construct();
		$this->folderPath = PATH_SMARTY_ADMIN_MOD . '/mod_groups/';
		$this->parentPath = $this->folderPath . 'groups_header.tpl';
		$this->smarty->assign('groupsParent', $this->parentPath);
	}

	public function ChangeGroup ($id, $name, $max_credit) {

		$this->smarty->assign('ID', $id);
		$this->smarty->assign('name', $name);
		$this->smarty->assign('max_credit', $max_credit);
		$this->smarty->display($this->folderPath . 'change_group.tpl');
	}

	public function ShowGroups ($groups) {

		$this->smarty->assign('groups', $groups);
		$this->smarty->display($this->folderPath . 'show_groups.tpl');
	}

	public function NewGroup ($pc_arr) {

		$this->smarty->assign('priceclasses', $pc_arr);
		$this->smarty->display($this->folderPath . 'form_new_group.tpl');
	}

	public function Menu () {

		$this->smarty->display($this->folderPath . 'group_menu.tpl');
	}

	private $folderPath;
}

?>