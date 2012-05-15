<?php

class AdminMealsInterface extends AdminInterface {
	public function __construct() {
		parent::__construct();
		$this->folderPath = PATH_SMARTY_ADMIN_MOD . '/mod_meals/';
		$this->parentPath = $this->folderPath . 'meals_header.tpl';
		$smarty->assign('mealParent', $this->parentPath);
	}
	
	private $folderPath;
}

?>