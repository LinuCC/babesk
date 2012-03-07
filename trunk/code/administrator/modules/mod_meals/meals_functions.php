<?php
/**
 *@file meals_functions.php some outsourced functions of the meals-module
 **/

/**
 *Function to show the create-meal-form and, if it is filled out,
 *it adds the meal in the MySQL-Server
 */
function create_meal() {
	//---INCLUDE---
	require_once "meals_constants.php";
	require_once PATH_INCLUDE . '/logs.php';
	require_once PATH_INCLUDE . '/meal_access.php';
	
	require_once PATH_INCLUDE . '/access.php'; //for ordercount workaround
	
	global $smarty;
	global $logger;
	
	define('PATH_TEMPLATE_MEAL', MEAL_SMARTY_TEMPLATE_PATH . '/add_meal.tpl');
	//---INIT---
	$severity = NOTICE;
	$categorie = ADMIN;
	$meal_db = new MealManager();
	
	//---METHODS---
	//safety-checks
	if ('POST' == $_SERVER['REQUEST_METHOD']
			&& isset($_POST['name'], $_POST['description'], $_POST['price_class'], $_POST['max_orders'])) {
		$name = $_POST['name'];
		$description = $_POST['description'];
		$price_class = $_POST['price_class'];
		$max_orders = $_POST['max_orders'];
		$date_ar = array("day" => $_POST['Date_Day'], "month" => $_POST['Date_Month'], "year" => $_POST['Date_Year']);
		
		if (strlen($name) > 255) {
			echo MEAL_ERROR_NAME . "<br>";
			$logger->log($categorie, $severity, "MEAL_ERROR_NAME");
			return false;
		}
		if (strlen($description) > 1000) {
			echo MEAL_ERROR_DESCRIPTION . "<br>";
			$logger->log($categorie, $severity, "MEAL_ERROR_DESCRIPTION");
			return false;
		}
		if (!preg_match('/\A^[0-9]{1,6}\z/', $price_class)) {
			echo MEAL_ERROR_PRICE_CLASS . "<br>";
			$logger->log($categorie, $severity, "MEAL_ERROR_PRICE_CLASS");
			return false;
		}
		if (!preg_match('/\A^[0-9]{1,}\z/', $max_orders)) {
			echo MEAL_ERROR_MAX_ORDERS . "<br>";
			$logger->log($categorie, $severity, "MEAL_ERROR_MAX_ORDERS");
			return false;
		}
		if ($date_ar['day'] > 31 or $date_ar['month'] > 12 or $date_ar['year'] < 2000 or $date_ar['year'] > 3000) {
			echo MEAL_ERROR_DATE . '<br>';
			$logger->log($categorie, $severity, 'MEAL_ERROR_DATE');
			return false;
		}
		//convert the date for MySQL-Server
		$date_conv = $date_ar["year"] . "-" . $date_ar["month"] . "-" . $date_ar["day"];
		//and add the meal
		try {
			$meal_db->addMeal($name, $description, $date_conv, $price_class, $max_orders);
		} catch (Exception $e) {
			die(MEAL_ERROR_ADD . $e->getMessage());
		}
		echo MEAL_ADDED;
	} else {//if Formular isnt filled yet or the link was wrong
		try {
			price_class_init_smarty_vars();
		} catch (Exception $e) {
			die(MEAL_ERROR_PC . $e->getMessage());
		}
		$smarty->display(MEAL_SMARTY_TEMPLATE_PATH . '/add_meal.tpl');
	}
}

/**
 *Prepare Smarty-variables with the price_class to use them in add_meal.tpl
 */
function price_class_init_smarty_vars() {
	require_once PATH_INCLUDE . '/price_class_access.php';
	global $smarty;
	$priceclassmanager = new Priceclassmanager('price_classes');
	try {
		$sql_price_classes = $priceclassmanager->getTableData();
	} catch (MySQLVoidDataException $e) {
		die(MEAL_ERROR_PC);
	} catch (Exception $e) {
		die("Error:" . $e->getMessage());
	}
	$price_class_id = array();
	$price_class_name = array();
	
	$used_pc_IDs = array();
	foreach ($sql_price_classes as $pc) {
		$is_id_existing = false;
		foreach ($used_pc_IDs as $id) {
			if ($pc['pc_ID'] == $id) {
				$is_id_existing = true;
				break;
			}
		}
		if (!$is_id_existing) {
			$used_pc_IDs[] = $pc['pc_ID'];
			$price_class_id[] = $pc['pc_ID'];
			$price_class_name[] = $pc['name'];
		}
	}
	
	$smarty->assign('price_class_id', $price_class_id);
	$smarty->assign('price_class_name', $price_class_name);
}

/**
 *this function looks old meals up and delete them.
 *@param date All meals before this date will be deleted, allowed formats: yyyy-mm-dd and timestamp
 */
function remove_old_meals($search_date) {
	require_once "meals_constants.php";
	require_once PATH_INCLUDE . "/logs.php";
	require_once PATH_INCLUDE . "/meal_access.php";
	
	global $logger;
	$mealmanager = new MealManager('meals');
	$meals = $mealmanager->getTableData();
	
	if (preg_match('/\A[0-9]{2,4}-[0-9]{2}-[0-9]{2}\z/', $search_date)) {
		$search_array = explode('-', $search_date);
		$search_timestamp = mktime(0, 0, 1, $search_array[1], $search_array[2], $search_array[0]);
	} else if (preg_match('/\A[0-9]{1,}\z/', $search_date))
		$search_timestamp = $search_date;
	else if (empty($search_date)) {
		echo MEAL_ERROR_PARAM . ' Funktion: ' . __FUNCTION__ . '<br>';
		return;
	} else {
		var_dump($search_date);
		$logger->log(ADMIN, MODERATE, 'MEAL_F_ERROR_DATE_FORMAT');
		die(MEAL_F_ERROR_DATE_FORMAT);
	}
	
	foreach ($meals as $meal) {
		$m_timearray = explode("-", $meal["date"]);
		$m_timestamp = mktime(0, 0, 1, $m_timearray[1], $m_timearray[2], $m_timearray[0]);
		if ($m_timestamp < $search_timestamp) {
			try {
				$mealmanager->delEntry($meal['ID']);
			} catch (Exception $e) {
				die(MEAL_ERROR_DELETE . $e->getMessage() . ' ' . __FUNCTION__);
			}
			$logger->log(ADMIN, NOTICE, MEAL_DELETED_LOG . ', name:' . $meal['name']);
			echo MEAL_DELETED . ', name:' . $meal['name'] . '<br>';
		}
	}
}

/**
 *shows the meals
 */
function show_meals() {
	
	require_once PATH_INCLUDE . "/meal_access.php";
	require_once "meals_constants.php";
	require_once PATH_INCLUDE . "/functions.php";
	
	global $smarty;
	$mealManager = new MealManager('meals');
	$meals = $mealManager->getTableData();
	if (!$meals)
		die(MEAL_NO_MEALS_FOUND);
	foreach ($meals as &$meal) {
		$meal['date'] = formatDate($meal['date']);
	}
	
	$smarty->assign('meals', $meals);
	$smarty->display(MEAL_SMARTY_TEMPLATE_PATH . '/show_meals.tpl');
}

/**
 *sorts the orders for show_orders()
 *first it sorts by meal, then by username / Real Name
 */
function sort_orders($orders) {
	//sorting by meals
	if (!count($orders))
		echo MEAL_ERROR_PARAM . ' Funktion: ' . __FUNCTION__;
	foreach ($orders as $order) {
		if (empty($order)) {
			echo MEAL_DATABASE_PROB_ENTRY;
			echo MEAL_DATABASE_PROB_ENTRY_END;
			continue;
		}
		//$meals[$order['meal_name']] [] = $order;
		$meals[$order['meal_name']][] = $order;
	}
	
	//sorting by usernames
	foreach ($meals as $meal) {
		foreach ($meal as $order) {
			$temp[] = $order['user_name'];
		}
		sort($temp);
		foreach ($temp as $temp_name) {
			foreach ($meal as &$order) {
				if ($order['user_name'] == $temp_name) {
					$sorted_orders[] = $order;
					$order = NULL;//to avoid bugs with multiple orders from one user
					break;
				}
			}
		}
	}
	return $sorted_orders;
}

function translate_fetched($is_fetched) {
	switch ($is_fetched) {
		case 0:
			return ORDER_NOT_FETCHED;
		case 1:
			return ORDER_FETCHED;
	}
	throw Exception('Wrong argument');
}

function edit_infotext() {
	require_once PATH_INCLUDE . '/access.php';
	
	global $smarty;
	
	$temp = new TableManager('global_settings');
	
	$infotext1 = $temp->getTableData('name="menu_text1"');
	$infotext2 = $temp->getTableData('name="menu_text2"');
	
	if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['infotext1'], $_POST['infotext2'])) {
		
		$infotext1neu = $_POST['infotext1'];
		$infotext2neu = $_POST['infotext2'];
		if($infotext1neu == '') 
			$infotext1neu = '&nbsp;';
		if($infotext2neu == '') 
			$infotext2neu = '&nbsp;';
		try {
			$temp->alterEntry($infotext1[0]["id"], 'value', $infotext1neu);
			$temp->alterEntry($infotext2[0]["id"], 'value', $infotext2neu);
		} catch (Exception $e) {
			die_error(MEAL_ERROR_EDIT_INFOTEXT . $e->getMessage());
		}
		$smarty->assign('infotext1', $infotext1neu);
		$smarty->assign('infotext2', $infotext2neu);
		$smarty->display(MEAL_SMARTY_TEMPLATE_PATH . '/edit_infotext_fin.tpl');
		
	} else {
		
		$smarty->assign('infotext1', $infotext1[0]["value"]);
		$smarty->assign('infotext2', $infotext2[0]["value"]);
		$smarty->display(MEAL_SMARTY_TEMPLATE_PATH . '/edit_infotext.tpl');
	}
}


function editLastOrderTime() {
	require_once PATH_INCLUDE . '/access.php';

	global $smarty;

	$temp = new TableManager('global_settings');

	$lastOrderTime = $temp->getTableData('name="last_order_time"');
	

	if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['Time_Hour'],$_POST['Time_Minute'])) {

		try {
			$temp->alterEntry($lastOrderTime[0]["id"], 'value', $_POST['Time_Hour'].':'.$_POST['Time_Minute']);
		} catch (Exception $e) {
			die_error(MEAL_ERROR_EDIT_DEADLINE . $e->getMessage());
		}
		$smarty->assign('lastOrderTime', $_POST['Time_Hour'].':'.$_POST['Time_Minute']);
		$smarty->display(MEAL_SMARTY_TEMPLATE_PATH . '/edit_deadline_fin.tpl');

	} else {

		$smarty->assign('lastOrderTime', $lastOrderTime[0]["value"]);;
		$smarty->display(MEAL_SMARTY_TEMPLATE_PATH . '/edit_deadline.tpl');
	}
}


/**
 *shows the orders in a table
 */
function show_orders() {
	require_once PATH_INCLUDE . '/order_access.php';
	require_once PATH_INCLUDE . '/meal_access.php';
	require_once PATH_INCLUDE . '/user_access.php';
	require_once PATH_INCLUDE . '/group_access.php';
	require_once PATH_INCLUDE . '/global_settings_access.php';
	require_once PATH_INCLUDE . '/functions.php';
	require_once "meals_constants.php";
	
	global $smarty;
	
	if (!isset($_POST['ordering_day']) or !isset($_POST['ordering_month']) or !isset($_POST['ordering_year'])) {
		$today = array('day' => date('d'), 'month' => date('m'), 'year' => date('Y'));
		$smarty->assign('today', $today);
		$smarty->display(MEAL_SMARTY_TEMPLATE_PATH . '/show_orders_select_date.tpl');
	} else {
		$order_manager = new OrderManager();
		$meal_manager = new MealManager();
		$user_manager = new UserManager();
		$groupManager = new GroupManager();
		
		$mysql_orders = array();
		$order = array();
		
		if ($_POST['ordering_day'] > 31 or $_POST['ordering_month'] > 12 or $_POST['ordering_year'] < 2000
				or $_POST['ordering_year'] > 3000) {
			die(MEAL_ERROR_DATE);
		}
		$date = $_POST['ordering_year'] . '-' . $_POST['ordering_month'] . '-' . $_POST['ordering_day'];
		try {
			$orders = $order_manager->getAllOrdersAt($date);
			
		} catch (MySQLVoidDataException $e) {
			die(MEAL_NO_ORDERS_FOUND);
		} catch (MySQLConnectionException $e) {
			die($e);
		}
		
		if (!count($orders)) {
			die_error(MEAL_NO_ORDERS_FOUND);
			die();
		}
		
		foreach ($orders as &$order) {
			if (!count($meal_data = $meal_manager->getEntryData($order['MID'], 'name'))
					or !count($user_data = $user_manager->getEntryData($order['UID'], 'name', 'forename', 'GID'))) {
				echo MEAL_DATABASE_PROB_ENTRY;
				echo MEAL_DATABASE_PROB_ENTRY_END;
			} else {
				
				$order['meal_name'] = $meal_data['name'];
				$order['user_name'] = $user_data['forename'] . ' ' . $user_data['name'];
				$order['is_fetched'] = translate_fetched($order['fetched']);
			}
		}
		
		//--------------------
		//Count all Orders
		$num_orders = array();
		$mealIdArray = $meal_manager->GetMealIdsAtDate($date);
		$counter = 0;
		foreach ($mealIdArray as $mealIdEntry) {
			
			$groups = array();//to show how many from different groups ordered something
			$sp_orders = $order_manager->getAllOrdersOfMealAtDate($mealIdEntry['MID'], $date);
			$num_orders[$counter]['MID'] = $mealIdEntry['MID'];
			$num_orders[$counter]['name'] = $meal_manager->GetMealName(($mealIdEntry['MID']));
			$num_orders[$counter]['number'] = count($sp_orders);
			//--------------------
			//Get specific Usergroups for Interface
			foreach ($sp_orders as $sp_order) {
				
				$user = $user_manager->getEntryData($sp_order['UID'], 'GID');
				$sql_group = $groupManager->getEntryData($user['GID'], 'name');
				$group_name = $sql_group['name'];
				if (count($groups)) {
					$is_new_group = true;
					foreach ($groups as &$group) {
						if (isset($group['name']) && $group['name'] == $group_name) {
							$group['counter'] += 1;
							$is_new_group = false;
							continue;
						}
					}
					if ($is_new_group) {
						$group_arr = array('name' => $group_name, 'counter' => 1);
						$groups[] = $group_arr;
					}
				} else {
					//no group defined yet
					$group_arr = array('name' => $group_name, 'counter' => 1);
					$groups[] = $group_arr;
				}
			}
			//--------------------
			
			$num_orders[$counter]['user_groups'] = $groups;
			$counter++;
		}
		
		$orders = sort_orders($orders);
		
		if (isset($num_orders[0]) && $counter) {
			$smarty->assign('num_orders', $num_orders);
			$smarty->assign('orders', $orders);
			$smarty->assign('ordering_date', formatDate($date));
			$smarty->display(MEAL_SMARTY_TEMPLATE_PATH . '/show_orders.tpl');
		} else {
			die('für den ' . formatDate($date) . ' sind keine Bestellungen vorhanden');
		}
	}
}

/**
 *deletes the old meals and old orders.
 */
function delete_old_meals_and_orders() {
	require_once PATH_INCLUDE . '/meal_access.php';
	require_once PATH_INCLUDE . '/order_access.php';
	require_once 'meals_constants.php';
	
	global $smarty;
	global $logger;
	
	$orderManager = new OrderManager('orders');
	
	//show the User some Messages after finishing the job
	$error_str = '';
	$msg_str = '';
	
	if (!isset($_POST['day']) or !isset($_POST['month']) or !isset($_POST['year'])) {
		$today = array('day' => date('d'), 'month' => date('m'), 'year' => date('Y'));
		$smarty->assign('today', $today);
		$smarty->display(MEAL_SMARTY_TEMPLATE_PATH . '/delete_old_select_date.tpl');
	} else {
		$timestamp = strtotime($_POST['year'] . '-' . $_POST['month'] . '-' . $_POST['day']);
		if ($timestamp == -1) {
			die_error(MEAL_ERROR_DATE);die();
		}
		remove_old_meals($timestamp);
		
		//////////////////////////////////////////////////
		//Remove old Orders
		//////////////////////////////////////////////////
		try { //if-else-handling with two try-catches
			try {
				$orders = $orderManager->getTableData();
			} catch (Exception $e) {
				die_error(MEAL_NO_ORDERS_FOUND);
				throw $e;
				
				foreach ($orders as $order) {
					$o_timearray = explode("-", $order["date"]);
					$o_timestamp = mktime(0, 0, 1, $o_timearray[1], $o_timearray[2], $o_timearray[0]);
					if ($o_timestamp < $timestamp) {
						try {
							$orderManager->delEntry($order['ID']);
						} catch (Exception $e) {
							$logger->log(ADMIN, MODERATE, ORDER_ERROR_DELETE . 'dump:' . var_dump($order));
							die_error(ORDER_ERROR_DELETE);die();
						}
						$msg_str .= ORDER_DELETED . ' ID:' . $order['ID'] . '<br>';
					}
				}
			}
		} catch (Exception $e) {
			die();
		}
		if (!preg_match('/\A[0-9]{1,}\z/', $timestamp)) {
			die(MEAL_ERROR_DATE);
		}
		
		//show finished
		$smarty->assign('messageStr', $msg_str);
		$smarty->display(MEAL_SMARTY_TEMPLATE_PATH.'/delete_old_fin.tpl');
	}
}

/**
 * deletes a meal
 * This function deletes a meal. If linked_orders is set to true, it will also delete the 
 * orders linked to it.
 * @param numeric_string $ID the ID of the meal to delete
 * @param boolean $linked_orders If set to true, all orders linked to the meal to delete will also be deleted
 */
function delete_meal($ID, $linked_orders) {
	require_once PATH_INCLUDE . '/meal_access.php';
	require_once PATH_INCLUDE . '/order_access.php';
	require_once 'meals_constants.php';
	
	$mealManager = new MealManager();
	$orderManager = new OrderManager();
	
	try {
		$mealManager->delEntry($ID);
	} catch (Exception $e) {
		throw MEAL_ERROR_DELETE . ':' . $e->getMessage();
	}
	if ($linked_orders) {
		$orders = $orderManager->getAllOrdersOfMeal($ID);
		foreach ($orders as $order) {
			try {
				$orderManager->delEntry($order['ID']);
			} catch (Exception $e) {
				echo MEAL_ERROR_DEL_ORDER . ': BestellungsID:' . $order['ID'] . 'Gemeldetes Problem:'
						. $e->getMessage() . '<br>';
			}
		}
	}
}

?>