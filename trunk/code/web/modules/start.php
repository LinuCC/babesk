<?php
    //No direct access
    defined('_WEXEC') or die("Access denied");
	
	$meal = array();
	$result = $orderManager->getAllOrdersOfUser($_SESSION['uid'], strtotime(date('Y-m-d')));
	if(!$result) {
		
	}
	else {
		foreach($result as $order) {
			$mealname = $mealManager->getEntryData($order['MID'], 'name');
			$meal[] = array('date' => $order["date"], 'name' => $mealname["name"]);
		}
	}
	$smarty->assign('meal', $meal);
	$smarty->display('web/menu.tpl');
?>