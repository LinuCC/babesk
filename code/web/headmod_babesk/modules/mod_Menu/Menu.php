<?php

require_once PATH_INCLUDE . '/Module.php';

class Menu extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	
	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
	}
	
	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute() {
		//No direct access
		defined('_WEXEC') or die("Access denied");
		
		require_once PATH_ACCESS . '/GlobalSettingsManager.php';
		require_once PATH_ACCESS . '/OrderManager.php';
		require_once PATH_ACCESS . '/MealManager.php';
		
		global $smarty;
		global $logger;
		
		$orderManager = new OrderManager('orders');
		$mealManager = new MealManager('meals');
		
		$meal = array();
		$orders_existing = true;
		$meal_problems = false;
		
		try {
			$orders = $orderManager->getAllOrdersOfUser($_SESSION['uid'], strtotime(date('Y-m-d')));
		} catch (MySQLVoidDataException $e) {
			$smarty->assign('error', 'Keine Bestellungen vorhanden.');
			$orders_existing = false;
		} catch (Exception $e) {
			die('Error: ' . $e);
		}
		if ($orders_existing) {
			$today = date('Y-m-d');
			$hour = date('H', time());
			foreach ($orders as $order) {
				try {
					$mealname = $mealManager->getEntryData($order['MID'], 'name');
				} catch (MySQLVoidDataException $e) {
					$meal_problems = true;
					$smarty->assign('error', '<p class="error">Zu bestimmten Bestellung(-en) fehlen Daten einer Mahlzeit! Bitte benachrichtigen sie den Administrator!</p>');
					$logger->log(WEB, CRITICAL,
							sprintf('Order does not point on correct Meal! Exception: %s', $e->getMessage()));
					continue;
				}
				if (!$order['fetched'] AND $order['date'] >= $today) {
		
					//fetch last_order_time from database and compare with actual time
					$gsManager = new GlobalSettingsManager();
					$hour = date('H:i', time());
					$last_order_time = $gsManager->getLastOrderTime();
					if ((str_replace(":", "", $hour) > str_replace(":", "", $last_order_time)) AND ($order['date'] == $today)) {
		
						$meal[] = array('date' => formatDate($order["date"]), 'name' => $mealname["name"],
								'orderID' => $order['ID'], 'cancel' => false);
					} else {
						$meal[] = array('date' => formatDate($order["date"]), 'name' => $mealname["name"],
								'orderID' => $order['ID'], 'cancel' => true);
					}
				}
			}
		}
		if (!count($meal) && !$meal_problems) {
			//no new meals there
			$smarty->assign('error', 'Keine Bestellungen vorhanden.');
		}
		$smarty->assign('meal', $meal);
		$smarty->display('web/modules/mod_menu/menu.tpl');
	}
}
?>