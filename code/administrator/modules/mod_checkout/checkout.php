<?php

    //no direct access
    defined('_AEXEC') or die("Access denied");
    global $smarty;

	require PATH_INCLUDE.'/dbconnect.php';
	require PATH_INCLUDE.'/managers.php';
	require PATH_INCLUDE.'/card_access.php';
    require "checkout_constants.php"; 

    
    
	if ('POST' == $_SERVER['REQUEST_METHOD']) {
	   if (!isset($_POST['card_ID'])) {
		  die(INVALID_FORM);
		}  //save values and check for empty fields
		if (($card_id = trim($_POST['card_ID'])) == '' OR !valid_card_ID($card_id)) {
	        die(EMPTY_FORM);
	   	}
	   	
	   	$uid = $card_id;//the userID is the ID of the card
	   	
	   	$date = date("Y-m-d");
	   	
	   	$orders = $orderManager->getAllOrdersOfUser($uid, $date);
	   	$meal_names = array();
		for ($i = 0; $i < $orders->num_rows; $i++) {
            $row = $orders->fetch_assoc();
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