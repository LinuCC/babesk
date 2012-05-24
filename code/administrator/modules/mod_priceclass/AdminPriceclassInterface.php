<?php

class AdminPriceclassInterface extends AdminInterface {
	public function __construct() {
		parent::__construct();
		$this->folderPath = PATH_SMARTY_ADMIN_MOD . '/mod_priceclass/';
		$this->parentPath = $this->folderPath . 'header_priceclass.tpl';
		$this->smarty->assign('pcParent', $this->parentPath);
	}
	
	public function NewPriceclass($groups) {
		$this->smarty->assign('groups', $groups);
		$this->smarty->display($this->folderPath . 'new_priceclass_all_groups.tpl');
	}
	
	public function ChangePriceclass($priceclass, $groups, $current_group_name) {
		$this->smarty->assign('ID', $priceclass['ID']);
		$this->smarty->assign('name', $priceclass['name']);
		$this->smarty->assign('price', $priceclass['price']);
		$this->smarty->assign('groups', $groups);
		$this->smarty->assign('current_group_name', $current_group_name);
		$this->smarty->display($this->folderPath . 'change_priceclass.tpl');
	}
	
	public function ShowPriceclasses($priceclasses) {
		$this->smarty->assign('priceclasses', $priceclasses);
		$this->smarty->display($this->folderPath . 'show_priceclasses.tpl');
	}
	
	public function Menu() {
		$this->smarty->display($this->folderPath . 'priceclass_menu.tpl');
	}
	
	protected $folderPath;
}

?>