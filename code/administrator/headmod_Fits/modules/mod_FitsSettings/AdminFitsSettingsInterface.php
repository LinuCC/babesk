<?php

class AdminFitsSettingsInterface extends AdminInterface {
	public function __construct($mod_path) {
		parent::__construct($mod_path);
		$this->parentPath = $this->tplFilePath . 'fits_header.tpl';
		$this->smarty->assign('fitsParent', $this->parentPath);
		$this->smarty->assign('inh_path', $this->parentPath);
	}
	

	
	public function ShowEditForm($key, $year,$class,$allClasses) {
		$this->smarty->assign('key',$key);
		$this->smarty->assign('year',$year);
		$this->smarty->assign('class',$class);
		$this->smarty->assign('allClasses',$allClasses);		
		$this->smarty->display($this->tplFilePath. 'edit_settings.tpl');
	}
	
	public function FinEditSettings () {
		$this->smarty->display($this->tplFilePath . 'edit_settings_fin.tpl');
	}

}
?>