<?php

class AdminLogInterface extends AdminInterface{
	
	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	
	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($mod_path) {
		
		parent::__construct($mod_path);
		$this->parentPath = $this->tplFilePath . 'header_logs.tpl';
		$this->smarty->assign('logsParent', $this->parentPath);
	}
	
	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function ShowLogs($logs) {
		
		$this->smarty->assign('logs',$logs);
		$this->smarty->display($this->tplFilePath . 'showLogs.tpl');
	}
	
	public function ChooseSeverity($severitys, $category) {
		
		$this->smarty->assign('severity_levels', $severitys);
		$this->smarty->assign('category', $category);
		$this->smarty->display($this->tplFilePath . 'chooseLogsSeverity.tpl');
	}
	
	public function ChooseCategory($categories) {
		$this->smarty->assign('categories', $categories);
		$this->smarty->display($this->tplFilePath . 'chooseLogs.tpl');
	}
}

?>