<?php
    //No direct access
    defined('_WEXEC') or die("Access denied");
    global $smarty;
    //global OrderManager; //etc. geht auch (glaub ich)
    
    $orderManager = new OrderManager('orders');
    $mealManager = new MealManager('meals');
	
	$meal = array();
	$result = $orderManager->getAllOrdersOfUser($_SESSION['uid'], strtotime(date('Y-m-d')));
	$today = date('Y-m-d');
	$hour = date('H', time());
	foreach($result as $order) {
		$mealname = $mealManager->getEntryData($order['MID'], 'name');
		if(!$order['fetched'] AND $order['date'] >= $today) {
		    if ($order['date'] == $today AND $hour > 8) {
                $meal[] = array('date' => formatDate($order["date"]), 'name' => $mealname["name"], 'orderID' => $order['ID'], 'cancel' => false);
            }
            else {
                $meal[] = array('date' => formatDate($order["date"]), 'name' => $mealname["name"], 'orderID' => $order['ID'], 'cancel' => true);    
            }  
        }	
	}
	$smarty->assign('meal', $meal);
	$smarty->display('web/modules/mod_menu/menu.tpl');
?>