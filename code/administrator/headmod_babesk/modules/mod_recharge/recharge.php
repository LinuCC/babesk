<?php

//no direct access
defined('_AEXEC') or die("Access denied");

require_once 'AdminRechargeProcessing.php';
require_once 'AdminRechargeInterface.php';

$rechargeInterface = new AdminRechargeInterface();
$rechargeProcessing = new AdminRechargeProcessing();

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

?>