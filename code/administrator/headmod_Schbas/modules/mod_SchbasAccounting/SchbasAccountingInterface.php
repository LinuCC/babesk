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
		$this->smarty->assign('adress', ($_SERVER['HTTP_HOST']).$_SERVER['REQUEST_URI']);
		$this->smarty->display($this->tplFilePath . 'showUsersGroupedByYearAndGrade.tpl');
	}
	
	public function showRememberList($schueler1, $class, $title, $date, $schuelerTotalNr){
		$this->smarty->assign('schuelerTotalNr', $schuelerTotalNr);
		$this->smarty->assign('schueler1', $schueler1);
		$this->smarty->assign('class', $class);
		$this->smarty->assign('title', $title);
		$this->smarty->assign('date', $date);
		$this->smarty->display($this->tplFilePath . 'showRememberList.tpl');
	}
	
	function test(){
		$this->smarty->display($this->tplFilePath . 'test.tpl');
	}
}

?>