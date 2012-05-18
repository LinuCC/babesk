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
	
	
// 	$cardManager = new CardManager();
// 	$userManager = new UserManager();
// 	if (isset($_POST['card_ID'])) {
// 		//save values and check for empty fields

// 		///WTF is wrong here???
// 		/*if (($card_id = trim($_POST['card_ID']) == '')) {
// 		die(EMPTY_FORM);
// 		}*/
// 		$card_id = $_POST['card_ID'];
// 		try {
// 			$uid = $cardManager->getUserID($card_id);
// 			if ($userManager->checkAccount($uid)) {
// 				$smarty->display('administrator/modules/mod_recharge/error_locked.tpl');
// 				exit();
// 			}
			
// 		} catch (Exception $e) {
// 			die_error(ERR_GET_UID.$e->getMessage());
// 		}
// 		$_SESSION['module_data']['recharge_user'] = $uid;
		
// 		try {
// 			$smarty->assign('max_amount', $userManager->getMaxRechargeAmount($uid));
// 		} catch (Exception $e) {
// 			die_error(ERR_MAX_RECHARGE.$e->getMessage());
// 		}
// 		$smarty->display('administrator/modules/mod_recharge/form2.tpl');
// 	}

// 	if(isset($_POST['amount'])) {
// 		$amount = str_replace(',', '.', $_POST['amount']);
// 		$amount = floatval($amount);

// 		if($userManager->changeBalance($_SESSION['module_data']['recharge_user'], $amount)) {
// 			$userdata = $userManager->getEntryData($_SESSION['module_data']['recharge_user'], 'username');
// 			$smarty->assign('username', $userdata['username']);
// 			$logger->log(USERS,NOTICE,"USERNAME:".$userdata['username']."-AMOUNT:".$amount."-");
// 			$smarty->assign('amount', $amount);
			
// 			$smarty->display('administrator/modules/mod_recharge/recharge_success.tpl');
// 		}
// 		else {
// 			//$smarty->assign('reason', $reason);
// 			$smarty->display('administrator/modules/mod_recharge/recharge_failed.tpl');
// 		}
// 	}
}
else {
	$rechargeInterface->CardIdInput();
}

?>