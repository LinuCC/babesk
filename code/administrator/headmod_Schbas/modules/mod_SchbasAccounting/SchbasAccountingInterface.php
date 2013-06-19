<?php

class SchbasAccountingInterface extends AdminInterface {
	
	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	
	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($folder_path) {
		
		parent::__construct($folder_path);
		
		$this->parentPath = $this->tplFilePath  . 'mod_SchbasAccounting_header.tpl';
		$this->smarty->assign('checkoutParent', $this->parentPath);
	}
	
	////////////////////////////////////////////////////////////////////////////////
	//Methods
	
	public function MainMenu() {
		$this->smarty->display($this->tplFilePath . '/menu.tpl');
	}
	
	public function Scan() {
		$this->smarty->display($this->tplFilePath . '/scan.tpl');
	}
	
	function showAllUsers($grades, $gradeDesired, $users){
		$this->smarty->assign('gradeAll', $grades);
		$this->smarty->assign('gradeDesired', $gradeDesired);
		$this->smarty->assign('users', $users);
		$this->smarty->display($this->tplFilePath . 'showUsersGroupedByYearAndGrade.tpl');
	}
}

?>