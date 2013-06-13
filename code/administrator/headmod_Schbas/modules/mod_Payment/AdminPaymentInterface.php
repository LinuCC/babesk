<?php
require_once PATH_ADMIN.'/AdminInterface.php';
/**
 * @author Mirek Hancl
 *
 */
class AdminPaymentInterface extends AdminInterface{
	
	function __construct($mod_path) {
		
		parent::__construct($mod_path);
		
		$this->MOD_HEADING = $this->tplFilePath.'mod_booklist_header.tpl';
		$this->smarty->assign('booklistParent', $this->MOD_HEADING);
	}

	function ShowSelectionFunctionality() {
		$this->smarty->display($this->tplFilePath.'index.tpl');
	}
	
	function showAllUsers($grades, $gradeDesired, $users){
		$this->smarty->assign('gradeAll', $grades);
		$this->smarty->assign('gradeDesired', $gradeDesired);
		$this->smarty->assign('users', $users);
		$this->smarty->display($this->tplFilePath . 'showUsersGroupedByYearAndGrade.tpl');
	}
	
	
	
	/**
	 * The Path to the Smarty-Parent-Templatefile
	 */
	protected $MOD_HEADING;
}
?>