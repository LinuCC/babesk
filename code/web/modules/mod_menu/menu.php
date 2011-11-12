<?php
//No direct access
defined('_WEXEC') or die("Access denied");
global $smarty;
//global OrderManager; //etc. geht auch (glaub ich)

$orderManager = new OrderManager('orders');
$mealManager = new MealManager('meals');

$meal = array();
$orders_existing = true;

try {
	$orders = $orderManager->getAllOrdersOfUser($_SESSION['uid'], strtotime(date('Y-m-d')));
} catch (MySQLVoidDataException $e) {
	$smarty->assign('error', 'Keine Bestellungen vorhanden.');
	$orders_existing = false;
} catch (Exception $e) {
	die('Error: '.$e);
}
if($orders_existing) {
	$today = date('Y-m-d');
	$hour = date('H', time());
	foreach($orders as $order) {
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
}
if(!count($meal)) {
	//no new meals there
	$smarty->assign('error', 'Keine Bestellungen vorhanden.');
}
$smarty->assign('meal', $meal);
$smarty->display('web/modules/mod_menu/menu.tpl');
?>