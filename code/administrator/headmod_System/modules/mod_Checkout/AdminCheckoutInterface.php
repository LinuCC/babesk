<?php

class AdminCheckoutInterface extends AdminInterface {
	
	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	
	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($folder_path) {
		
		parent::__construct($folder_path);
		
		$this->parentPath = $this->tplFilePath  . 'mod_checkout_header.tpl';
		$this->smarty->assign('checkoutParent', $this->parentPath);
	}
	
	////////////////////////////////////////////////////////////////////////////////
	//Methods
	/**
	 * Shows a "card-is-locked"-Dialog
	 */
	public function CardLocked() {
		$this->smarty->display(PATH_SMARTY_CHECKOUT.'/checkout_locked.tpl');
	}
	
	public function Checkout($mealnames) {
		$this->smarty->assign('meal_names', $mealnames);
		$this->smarty->display($this->tplFilePath  . 'checkout.tpl');
	}
	
	public function CardId() {
		$this->smarty->display($this->tplFilePath . 'form.tpl');
	}
	
}

?>