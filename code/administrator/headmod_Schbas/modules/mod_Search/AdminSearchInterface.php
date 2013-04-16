<?php
require_once PATH_ADMIN.'/AdminInterface.php';
/**
 *
 */
class AdminSearchInterface extends AdminInterface{
	
	function __construct($mod_path) {
		
		parent::__construct($mod_path);
		
		$this->MOD_HEADING = $this->tplFilePath.'mod_search_header.tpl';
		$this->smarty->assign('SearchParent', $this->MOD_HEADING);
	}
	
	public function showSearchForm(){
		$this->smarty->display($this->tplFilePath . 'form.tpl');
	}
	
	
	/**
	 * The Path to the Smarty-Parent-Templatefile
	 */
	protected $MOD_HEADING;
}
?>