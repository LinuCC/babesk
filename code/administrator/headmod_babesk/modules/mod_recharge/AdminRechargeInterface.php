<?php

class AdminRechargeInterface extends AdminInterface {
	public function __construct() {
		
		parent::__construct();
		
		$this->folderPath = PATH_SMARTY_ADMIN_MOD . '/mod_recharge/';
		$this->parentPath = $this->folderPath . 'header_recharge.tpl';
		
		$this->smarty->assign('rechargeParent', $this->parentPath);
	}
	
	function CardIdInput() {
		$this->smarty->display('administrator/modules/mod_recharge/form1.tpl');
	}
	
	public function ChangeAmount($max_amount, $uid) {
		
		$this->smarty->assign('max_amount', $max_amount);
		$this->smarty->assign('uid', $uid);
		$this->smarty->display($this->folderPath . 'form2.tpl');
	}
	
	public function RechargeCard($username, $amount) {
		
		$this->smarty->assign('username', $username);
		$this->smarty->assign('amount', $amount);
			
		$this->smarty->display('administrator/modules/mod_recharge/recharge_success.tpl');
	}
	
 	private $folderPath;
}

?>