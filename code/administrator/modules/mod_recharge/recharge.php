<?php

    //no direct access
    defined('_AEXEC') or die("Access denied");
    global $smarty;

	require PATH_INCLUDE.'/managers.php'; 

	if ('POST' == $_SERVER['REQUEST_METHOD']) {
	   if (isset($_POST['card_ID'])) {
	       //save values and check for empty fields

		   ///WTF is wrong here???
		   /*if (($card_id = trim($_POST['card_ID']) == '')) {
	           die(EMPTY_FORM);
	   	   }*/
	       $card_id = $_POST['card_ID'];
	   	   
	       $uid = $userManager->getCardOwner($card_id);
	       $_SESSION['module_data']['recharge_user'] = $uid;
	       $smarty->assign('max_amount', $userManager->getMaxRechargeAmount($uid));
	       $smarty->display('administrator/modules/mod_recharge/form2.tpl');
	    }
	    
        if(isset($_POST['amount'])) {
            $amount = str_replace(',', '.', $_POST['amount']);
            $amount = floatval($amount);
            
            if($userManager->changeBalance($_SESSION['module_data']['recharge_user'], $amount)) {
                $smarty->assign('amount', $amount);
                $userdata = $userManager->getUserData($_SESSION['module_data']['recharge_user'], 'username');
                $smarty->assign('username', $userdata['username']);
                $smarty->display('administrator/modules/mod_recharge/recharge_success.tpl');
            }
            else {
                //$smarty->assign('reason', $reason);
                $smarty->display('administrator/modules/mod_recharge/recharge_failed.tpl');
            }
        }    
    }
	else {
	   $smarty->display('administrator/modules/mod_recharge/form1.tpl');
    }
      
?>