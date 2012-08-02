<?php
require_once PATH_ADMIN.'/AdminInterface.php';
/**
 * AdminUserInterface is to output the Interface
 * Enter description here ...
 * @author infchem
 *
 */
class AdminReligionInterface extends AdminInterface{
	
	function __construct($mod_path) {
		
		parent::__construct($mod_path);
		
		$this->MOD_HEADING = $this->tplFilePath.'mod_religion_header.tpl';
		$this->smarty->assign('ReligionParent', $this->MOD_HEADING);
	}

	function ShowSelectionFunctionality() {
		$this->smarty->display($this->tplFilePath.'religions_select.tpl');
	}
	
	function ShowReligions($religions) {
		$this->smarty->assign('religions', $religions);
		$this->smarty->display($this->tplFilePath.'show_religions.tpl');
	}
	
	function ShowReligionsSet($religions) {
		$this->smarty->assign('religions', $religions);
		$this->smarty->display($this->tplFilePath.'show_religions_set.tpl');
	}
	
	function ShowUsers($users,$religions) {
		$this->smarty->assign('users', $users);
		$this->smarty->assign('religions', $religions);
		$this->smarty->display($this->tplFilePath.'show_users.tpl');
	
	}
	
	function ShowUsersSuccess() {
		$this->smarty->display($this->tplFilePath.'show_religions_set.tpl');
	
	}
	
	/**
	 * The Path to the Smarty-Parent-Templatefile
	 */
	protected $MOD_HEADING;
}
?>