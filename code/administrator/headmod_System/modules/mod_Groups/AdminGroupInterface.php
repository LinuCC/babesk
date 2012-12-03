<?php

class AdminGroupInterface extends AdminInterface {
	public function __construct ($mod_path) {
		
		parent::__construct($mod_path);
		$this->parentPath = $this->tplFilePath . 'groups_header.tpl';
		$this->smarty->assign('groupsParent', $this->parentPath);
	}

	public function ChangeGroup ($id, $name, $max_credit) {

		$this->smarty->assign('ID', $id);
		$this->smarty->assign('name', $name);
		$this->smarty->assign('max_credit', $max_credit);
		$this->smarty->display($this->tplFilePath . 'change_group.tpl');
	}

	public function ShowGroups ($groups) {

		$this->smarty->assign('groups', $groups);
		$this->smarty->display($this->tplFilePath . 'show_groups.tpl');
	}

	public function NewGroup ($pc_arr) {

		$this->smarty->assign('priceclasses', $pc_arr);
		$this->smarty->display($this->tplFilePath . 'form_new_group.tpl');
	}

	public function Menu () {

		$this->smarty->display($this->tplFilePath . 'group_menu.tpl');
	}
}

?>