<?php

class AdminCardChangeInterface extends AdminInterface {
	
	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	
	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($folder_path) {
		
		parent::__construct($folder_path);
		
		$this->parentPath = $this->tplFilePath  . 'mod_cardchange_header.tpl';
		$this->smarty->assign('cardChange', $this->parentPath);
	}
	
	////////////////////////////////////////////////////////////////////////////////
	//Methods
	/**
	 * Shows some generic user infos
	 */	
	public function ShowCardChangeStats($sum) {
		$this->smarty->assign('cardChanges', $sum);

		$this->smarty->display($this->tplFilePath  . '/sum.tpl');
	}
	

	
}

?>