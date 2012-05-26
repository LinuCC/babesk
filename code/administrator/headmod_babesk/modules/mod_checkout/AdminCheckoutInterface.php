<?php

class AdminCheckoutInterface extends AdminInterface {
	
	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	protected $folderPath;
	
	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($folder_path) {
		
		parent::__construct();
		
		$this->folderPath = $folder_path;
		$this->parentPath = $this->folderPath . 'mod_checkout_header.tpl';
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
		$this->smarty->display($this->folderPath . 'checkout.tpl');
	}
	
	public function CardId() {
		$this->smarty->display('administrator/modules/mod_checkout/form.tpl');
	}
	
}

?>