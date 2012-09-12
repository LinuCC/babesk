<?php

class AdminFitsCheckInterface extends AdminInterface {
	
	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	
	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($folder_path) {
		
		parent::__construct($folder_path);
		
		$this->parentPath = $this->tplFilePath  . 'mod_fitscheck_header.tpl';
		$this->smarty->assign('checkoutParent', $this->parentPath);
	}
	
	////////////////////////////////////////////////////////////////////////////////
	//Methods
	/**
	 * Shows a "card-is-locked"-Dialog
	 */
	public function CardLocked() {
		$this->smarty->display($this->tplFilePath.'/card_locked.tpl');
	}
	
	public function HasFits($has_Fits) {
		$this->smarty->assign('has_Fits', $has_Fits);
		$this->smarty->display($this->tplFilePath  . '/result.tpl');
	}
	
	public function CardId() {
		$this->smarty->display($this->tplFilePath . '/form.tpl');
	}
	
}

?>