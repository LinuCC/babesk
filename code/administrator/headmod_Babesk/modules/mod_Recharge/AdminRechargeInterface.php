<?php

class AdminRechargeInterface extends AdminInterface {
	public function __construct($mod_path) {
		
		parent::__construct($mod_path);
		
		$this->parentPath = $this->tplFilePath . 'header_recharge.tpl';
		
		$this->smarty->assign('rechargeParent', $this->parentPath);
	}
	
	function CardIdInput() {
		$this->smarty->display($this->tplFilePath . 'form1.tpl');
	}
	
	public function ChangeAmount($max_amount, $uid) {
		
		$this->smarty->assign('max_amount', $max_amount);
		$this->smarty->assign('uid', $uid);
		$this->smarty->display($this->tplFilePath . 'form2.tpl');
	}
	
	public function RechargeCard($username, $amount) {
		
		$this->smarty->assign('username', $username);
		$this->smarty->assign('amount', $amount);
			
		$this->smarty->display($this->tplFilePath . 'recharge_success.tpl');
	}
}

?>