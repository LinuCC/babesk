<?php

class AdminLogInterface extends AdminInterface{
	
	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	protected $folderPath;
	
	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct() {
		
		parent::__construct();
		$this->folderPath = PATH_SMARTY_ADMIN_MOD . '/mod_logs/';
		$this->parentPath = $this->folderPath . 'header_logs.tpl';
		$this->smarty->assign('logsParent', $this->parentPath);
	}
	
	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function ShowLogs($logs) {
		
		$this->smarty->assign('logs',$logs);
		$this->smarty->display($this->folderPath . 'showLogs.tpl');
	}
	
	public function ChooseSeverity($severitys, $category) {
		
		$this->smarty->assign('severity_levels', $severitys);
		$this->smarty->assign('category', $category);
		$this->smarty->display($this->folderPath . 'chooseLogsSeverity.tpl');
	}
	
	public function ChooseCategory($categories) {
		$this->smarty->assign('categories', $categories);
		$this->smarty->display($this->folderPath . 'chooseLogs.tpl');
	}
}

?>