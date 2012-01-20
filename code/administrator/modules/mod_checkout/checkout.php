<?php

    //no direct access
    defined('_AEXEC') or die("Access denied");
    global $smarty;

	require PATH_INCLUDE.'/dbconnect.php';
	require PATH_INCLUDE.'/managers.php';
	require PATH_INCLUDE.'/card_access.php';
    require "checkout_constants.php"; 

    $cardManager = new CardManager();
    
	if ('POST' == $_SERVER['REQUEST_METHOD']) {
	   if (!isset($_POST['card_ID'])) {
		  die(INVALID_FORM);
		}  //save values and check for empty fields
		if (($card_id = trim($_POST['card_ID'])) == '' OR !$cardManager->valid_card_ID($card_id)) {
	        die(EMPTY_FORM);
	   	}
	   	try {
	   		$uid = $cardManager->getUserID($card_id);
	   	} catch (Exception $e) {
	   		die(ERR_GET_USER_BY_CARD.' Error:'.$e->getMessage());
	   	}
	   	
	   	$date = date("Y-m-d");
	   	$orders = array();
	   	try {
	   		$orders = $orderManager->getAllOrdersOfUser($uid, $date);
	   	} catch (MySQLVoidDataException $e) {
	   		$smarty->display(PATH_SMARTY_CHECKOUT.'/checkout_no_orders.tpl');
	   		exit();
	   	}
	   	$meal_names = array();
		for ($i = 0; $i < count($orders); $i++) {
            $row = $orders[$i];
            $meal_name = $mealManager->getEntryData($row['MID'], 'name');
            // Abfrage des feldes 'name' aus der Tabelle 'meals' mit der ID '$row['MID'], anschlie�end Ausgabe des Namens
            if(!$meal_name) {
                die(MEAL_NOT_FOUND);
            }
            if(!$orderManager->OrderFetched($row['ID'])) {
                $meal_names[] = $meal_name['name'];
                $orderManager->setOrderFetched($row['ID']);    
            } 
		}
        $smarty->assign('meal_names', $meal_names);
        $smarty->display('administrator/modules/mod_checkout/checkout.tpl');	
    }
	else{
	   $smarty->display('administrator/modules/mod_checkout/form.tpl');
    }
      
?>