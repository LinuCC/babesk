<?php
    error_reporting(E_ALL);
    global $smarty;
    
    $orderManager = new OrderManager('orders');
    $priceClassManager = new PriceClassManager();
    $userManager = new UserManager();
    
		
	$orderData = $orderManager->getEntryData($_GET['id'], 'MID');
	$mid = $orderData['MID'];
	$price = $priceClassManager->getPrice($_SESSION['uid'], $mid);
	
	//"repay", add the price for the menu to the users account
    if(!$userManager->changeBalance($_SESSION['uid'], $priceClassManager->getPrice($_SESSION['uid'], $mid))) {
        $smarty->display("web/modules/mod_cancel/failed.tpl");
        die();    
    }
    
    $orderManager->delEntry($_GET['id']);
	
	$smarty->display("web/modules/mod_cancel/cancel.tpl");
?>