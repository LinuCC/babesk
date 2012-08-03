<?php
require_once PATH_ADMIN.'/AdminInterface.php';
/**
 * AdminUserInterface is to output the Interface
 * Enter description here ...
 * @author infchem
 *
 */
class AdminForeignLanguageInterface extends AdminInterface{
	
	function __construct($mod_path) {
		
		parent::__construct($mod_path);
		
		$this->MOD_HEADING = $this->tplFilePath.'mod_foreignLanguage_header.tpl';
		$this->smarty->assign('ForeignLanguageParent', $this->MOD_HEADING);
	}

	function ShowSelectionFunctionality() {
		$this->smarty->display($this->tplFilePath.'foreignlanguage_select.tpl');
	}
	
	function ShowForeignLanguages($foreignLanguages) {
		$this->smarty->assign('foreignLanguages', $foreignLanguages);
		$this->smarty->display($this->tplFilePath.'show_foreignLanguages.tpl');
	}
	
	function ShowForeignLanguagesSet($foreignLanguages) {
		$this->smarty->assign('foreignLanguage', $foreignLanguages);
		$this->smarty->display($this->tplFilePath.'show_foreignLanguages_set.tpl');
	}
	
	function ShowUsers($users,$foreignLanguages) {
		$this->smarty->assign('users', $users);
		$this->smarty->assign('foreignLanguages', $foreignLanguages);
		$this->smarty->display($this->tplFilePath.'show_users.tpl');
	
	}
	
	function ShowUsersSuccess() {
		$this->smarty->display($this->tplFilePath.'show_foreignLanguages_set.tpl');
	
	}
	
	/**
	 * The Path to the Smarty-Parent-Templatefile
	 */
	protected $MOD_HEADING;
}
?>