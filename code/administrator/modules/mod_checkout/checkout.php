<?php

    //no direct access
    defined('_AEXEC') or die("Access denied");
    global $smarty;

	require_once PATH_ACCESS . '/dbconnect.php';
	require_once PATH_ACCESS . '/CardManager.php';
	require_once PATH_ACCESS . '/UserManager.php';
	require_once PATH_ACCESS . '/OrderManager.php';
    require "checkout_constants.php"; 

    $cardManager = new CardManager();
    $userManager = new UserManager();
    $orderManager = new OrderManager();
    $smarty->assign('checkoutParent', PATH_SMARTY_ADMIN_MOD.'/mod_checkout/mod_checkout_header.tpl');
    
	if ('POST' == $_SERVER['REQUEST_METHOD']) {
	   if (!isset($_POST['card_ID'])) {
		  die(INVALID_FORM);
		}  //save values and check for empty fields
		if (($card_id = trim($_POST['card_ID'])) == '' OR !$cardManager->valid_card_ID($card_id)) {
	        die(EMPTY_FORM);
	   	}
	   	try {
	   		$uid = $cardManager->getUserID($card_id);
	   		if ($userManager->checkAccount($uid)) {
	   			$smarty->display(PATH_SMARTY_CHECKOUT.'/checkout_locked.tpl');
	   			exit();
	   		}
	   	} catch (Exception $e) {
	   		die_error(ERR_GET_USER_BY_CARD.' Error:'.$e->getMessage());die();
	   	}
	   
	   	
	   	$date = date("Y-m-d");
	   	$orders = array();
	   	try {
	   		$orders = $orderManager->getAllOrdersOfUserAtDate($uid, $date);
	   	} catch (MySQLVoidDataException $e) {
	   		$smarty->display(PATH_SMARTY_CHECKOUT.'/checkout_no_orders.tpl');
	   		exit();
	   	} catch(Exception $e) {
	   		die('An Error ocurred:'.$e->getMessage());
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
            else {
                $meal_names[] = $meal_name['name'].'; '.ORDER_ALREADY_FETCHED;
            }
		}
        $smarty->assign('meal_names', $meal_names);
        $smarty->display('administrator/modules/mod_checkout/checkout.tpl');	
    }
	else{
	   $smarty->display('administrator/modules/mod_checkout/form.tpl');
    }
      
?>