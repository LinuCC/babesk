<?php

require_once PATH_INCLUDE . '/Module.php';

class Recharge extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	
	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
	}
	
	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute() {
		//no direct access
		defined('_AEXEC') or die("Access denied");
		
		require_once 'AdminRechargeProcessing.php';
		require_once 'AdminRechargeInterface.php';
		
		$rechargeInterface = new AdminRechargeInterface($this->relPath);
		$rechargeProcessing = new AdminRechargeProcessing($rechargeInterface);
		
		if ('POST' == $_SERVER['REQUEST_METHOD']) {
		
			if (isset($_POST['card_ID'])) {
				$rechargeProcessing->ChangeAmount($_POST['card_ID']);
			}
			else if(isset($_POST['amount'], $_POST['uid'])) {
				$rechargeProcessing->RechargeCard($_POST['uid'], $_POST['amount']);
			}
		}
		else {
		
			$rechargeInterface->CardIdInput();
		}
	}
}

?>