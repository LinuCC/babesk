<?php

    //no direct access
    defined('_AEXEC') or die("Access denied");

    require_once 'AdminCheckoutProcessing.php';
    require_once 'AdminCheckoutInterface.php';
    
    $checkoutProcessing = new AdminCheckoutProcessing();
    $checkoutInterface = new AdminCheckoutInterface();
    
	if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['card_ID'])) {
		$checkoutProcessing->Checkout($_POST['card_ID']);	
    }
	else{
		$checkoutInterface->CardId();
    }
      
?>