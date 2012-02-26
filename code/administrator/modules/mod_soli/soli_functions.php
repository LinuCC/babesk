<?php
/**
 *@file meals_functions.php some outsourced functions of the meals-module
 **/



/**
 *Prepare Smarty-variables with the price_class to use them in add_meal.tpl
 */
function price_class_init_smarty_vars() {
	require_once PATH_INCLUDE.'/price_class_access.php';
	global $smarty;
	$priceclassmanager = new Priceclassmanager('price_classes');
	try {
		$sql_price_classes = $priceclassmanager->getTableData();
	} catch (MySQLVoidDataException $e) {
		die(MEAL_ERROR_PC);
	} catch (Exception $e) {
		die("Error:".$e->getMessage());
	}
	$price_class_id = array();
	$price_class_name = array();

	$used_pc_IDs = array();
	foreach($sql_price_classes as $pc) {
		$is_id_existing = false;
		foreach($used_pc_IDs as $id) {
			if($pc['pc_ID'] == $id) {
				$is_id_existing = true;
				break;
			}
		}
		if(!$is_id_existing) {
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


/**
 *shows the meals
 */
function show_meals(){

	require_once PATH_INCLUDE."/meal_access.php";
	require_once "meals_constants.php";
	require_once PATH_INCLUDE."/functions.php";

	global $smarty;
	$mealManager = new MealManager('meals');
	$meals = $mealManager->getTableData();
	if(!$meals) die(MEAL_NO_MEALS_FOUND);
	foreach($meals as &$meal) {
		$meal['date'] = formatDate($meal['date']);
	}


	$smarty->assign('meals',$meals);
	$smarty->display(MEAL_SMARTY_TEMPLATE_PATH.'/show_meals.tpl');
}

/**
 *sorts the orders for show_orders()
 *first it sorts by meal, then by username / Real Name
 */
function sort_orders($orders) {
	//sorting by meals
	if(!count($orders))
	echo MEAL_ERROR_PARAM.' Funktion: '.__FUNCTION__;
	foreach($orders as $order) {
		if(empty($order)){
			echo MEAL_DATABASE_PROB_ENTRY; echo MEAL_DATABASE_PROB_ENTRY_END;
			continue;
		}
		$meals[$order['meal_name']] [] = $order;
	}

	//sorting by usernames
	foreach($meals as $meal){
		foreach($meal as $order){
			$temp[] = $order ['user_name'];
		}
		sort($temp);
		foreach($temp as $temp_name){
			foreach($meal as &$order){
				if($order['user_name'] == $temp_name){
					$sorted_orders [] = $order;
					$order = NULL;//to avoid bugs with multiple orders from one user
					break;
				}
			}
		}
	}
	return $sorted_orders;
}

function translate_fetched($is_fetched) {
	switch ($is_fetched){
		case 0:
			return ORDER_NOT_FETCHED;
		case 1:
			return ORDER_FETCHED;
	}
	throw Exception('Wrong argument');
}



/**
 *shows the orders in a table
 */
function show_orders() {
	require_once PATH_INCLUDE.'/access.php';
	require_once PATH_INCLUDE.'/order_access.php';
	require_once PATH_INCLUDE.'/meal_access.php';
	require_once PATH_INCLUDE.'/user_access.php';
	require_once PATH_INCLUDE.'/functions.php';
	require_once PATH_INCLUDE.'/price_class_access.php';
	require_once "soli_constants.php";

	global $smarty;
	
	$today = array('day' => date('d'),'month' => date('m'),'year' => date('Y'),'week' => date('W'));
	$tableaccess = new TableManager('price_classes');
	$table_settings = new TableManager('global_settings');
	$table_user = new TableManager('soli_coupons');
	$order_manager = new OrderManager('orders');
	$meal_manager = new MealManager('meals');
	$user_manager = new UserManager;
		

	if(!isset($_POST['ordering_kw']) ) {
		$solis = array();
		$accounts = $user_manager->checkSoliAccounts();
		foreach ($accounts as $account) {
			array_push($solis, $account['username']);
		}
		$smarty->assign('solis', $solis);
		$smarty->assign('today', $today);
		$user_manager = new UserManager;
		 
		$soliUsers = array();
		$usersWithSoli = $user_manager->checkSoliAccounts();
		$counter = 0;
		foreach($usersWithSoli as &$userWithSoli) {
			$soliUsers[$counter]['forename'] = $userWithSoli['forename'];
			$soliUsers[$counter]['name'] = $userWithSoli["name"];
			$counter++;
		}
		$smarty->assign('$soli_users',$soliUsers);
		
		$smarty->display(MEAL_SMARTY_TEMPLATE_PATH.'/show_orders_select_date.tpl');
	}
	else {
		 $dates = translateWeekIntoDates($_POST['ordering_kw'], $today);
		 $username = $_POST['name'];
		 try {
		 	$uid = $user_manager->getUserID($username);
		 } catch (MySQLException $e) {
		 }

		try {
			$orders = $order_manager->getAllOrdersOfUserBetween($uid, $dates[0], $dates[1]);
		
		} catch (MySQLVoidDataException $e) {
			die(MEAL_NO_ORDERS_FOUND);
		} catch (MySQLConnectionException $e) {
			die($e);
		}
		$mysql_orders = array();
		$order = array();
		if(!count($orders)) {
			die(MEAL_NO_ORDERS_FOUND);
		}
		foreach($orders as &$order) {
			if (!count($meal_data = $meal_manager->getEntryData($order['MID'],'name','price_class')) or
			!count($user_data = $user_manager->getEntryData($order['UID'],'name', 'forename', 'soli','GID'))) {
				echo MEAL_DATABASE_PROB_ENTRY;
				echo MEAL_DATABASE_PROB_ENTRY_END;
			}
			else {
				if ($user_data['soli'] = 1) {
					$query = sql_prev_inj(sprintf(' pc_ID=%s and GID=%s;',
					$meal_data['price_class'],$user_data['GID']));
					$price = $tableaccess->getTableData($query);
					

					$order['meal_name'] = $meal_data['name'];
					$order['user_name'] = $user_data['forename'].' '.$user_data['name'];
					$order['is_fetched'] = translate_fetched($order['fetched']);
					$order['price'] = $price[0]["price"];
				}
			}
		}
		$soli_price_array = $table_settings->getTableData("name='soli_price'");
		foreach ($soli_price_array as $soli_price_element) {
			$soli_price = $soli_price_element['value'];
		}
		$coupons = $user_manager->checkCouponAccounts();
		$sum = 0;
		foreach ($orders as &$order) {
			$order['date'] = change_date_format($order['date']);
			if (in_array($order['UID'], $coupons)) {
				$order['from_bank'] = $order['price'] - $soli_price;
				$order['soli_price'] = $soli_price;
			} else {
				$order['from_bank'] = 0;
				$order['soli_price'] = $order['price'];
			}	
			$sum = $sum + $order['from_bank'];
		}

		//////////////////////////////////////////////////
		/**
		* @todo refactor this part, some things are deprecated
		*/
		//for showing the number of orders for one meal
		$num_orders = array(array());
		$already_there = 0;
		$counter = 0;
		foreach($orders as &$order) {
			foreach($num_orders as &$num_order) {
				if(count($num_order) and $order['meal_name'] == $num_order['name']){
					$num_order['number'] += 1;
					$already_there = true;
				}
			}
			if(!$already_there) {
				$num_orders[$counter]['name'] = $order['meal_name'];
				$num_orders[$counter]['number'] = 1;
				$counter ++;
			}
			$already_there = false;
		}
		$orders = sort_orders($orders);
		//////////////////////////////////////////////////
			
		//if(isset($num_orders[0]) && $counter) {
			$smarty->assign('name',$order['user_name']);
			$smarty->assign('num_orders',$num_orders);
			$smarty->assign('orders',$orders);
			$smarty->assign('ordering_date',$_POST['ordering_kw']);
			$smarty->assign('sum',$sum);
			$smarty->assign('soli_price',$order['soli_price']);
			$smarty->assign('from_bank',$order['from_bank']);
			$smarty->display(MEAL_SMARTY_TEMPLATE_PATH.'/show_orders.tpl');
		//}
		//else {
		//	die('für die Kalenderwoche '.$_POST['ordering_kw'].' sind keine Bestellungen vorhanden');
		//}
	}
}

/**
 * shows the coupons
 */ 
function show_coupons() {
	require_once PATH_INCLUDE.'/access.php';
	require_once PATH_INCLUDE.'/order_access.php';
	require_once PATH_INCLUDE.'/meal_access.php';
	require_once PATH_INCLUDE.'/user_access.php';
	require_once PATH_INCLUDE.'/functions.php';
	require_once PATH_INCLUDE.'/price_class_access.php';
	require_once "soli_constants.php";
	
	global $smarty;
	$user_manager = new UserManager;
	$table_manager = new TableManager('soli_coupons');
	
	if(!isset($_POST['name'])) {
		$solis = array();
		$accounts = $user_manager->checkSoliAccounts();
		foreach ($accounts as $account) {
			array_push($solis, $account['username']);
		}
		$smarty->assign('solis', $solis);
		$smarty->display(MEAL_SMARTY_TEMPLATE_PATH.'/show_coupons.tpl');	
	}
	else {
		$uid = $user_manager->getUserID($_POST['name']);
		$date_arrray = array("day" => $_POST['Date_Day'],
						"month" => $_POST['Date_Month'],
	 					"year"  => $_POST['Date_Year']);
		$date = $date_arrray["year"]."-".$date_arrray["month"]."-".$date_arrray["day"];
		try {
			$table_manager->addEntry('enddate',$date,'UID',$uid);
		} catch (MEAL_DATABASE_PROB_ENTRY $e) {
		}
		echo COUPON_ADDED;
	}
}

/**
 * shows a menu in which you can set the settings
 */
function show_settings() {
	require_once PATH_INCLUDE.'/access.php';
	require_once PATH_INCLUDE.'/order_access.php';
	require_once PATH_INCLUDE.'/meal_access.php';
	require_once PATH_INCLUDE.'/user_access.php';
	require_once PATH_INCLUDE.'/functions.php';
	require_once PATH_INCLUDE.'/price_class_access.php';
	require_once "soli_constants.php";
	
	global $smarty;
	$table_access = new TableManager('global_settings');
	$price_id = $table_access->searchEntry("name = 'soli_price'");
	
	if(!isset($_POST['soli_price']) ) {
		$query = 'id ='.$price_id['id'];
		$old_soli_price_arr = $table_access->getTableData($query);
		$old_soli_price = $old_soli_price_arr[0]['value'];
		$smarty->assign('old_price',$old_soli_price);
		$smarty->display(MEAL_SMARTY_TEMPLATE_PATH.'/show_settings.tpl');	
	}
	else {
		try {
			$table_access->alterEntry($price_id['id'],'value',$_POST['soli_price']);
		} catch (MEAL_DATABASE_PROB_ENTRY $e) {
		}echo SETTINGS_CHANGED;
	}
	
}


/**
 * changes the week into the date of the monday and sunday
 */
function translateWeekIntoDates($week, $today) {
	$dt = new DateTime;
	$dt->setISODate($today['year'],$week, 1);
	$date_start = $dt->format('Y-m-d');
	$dt->setISODate($today['year'],$week, 7);
	$date_end = $dt->format('Y-m-d');
	$dates = array($date_start, $date_end);
	
	return $dates;
}

/**
 * changes the date format from MySQL in a readable date format 
 */
function change_date_format($date) {
	$day = substr($date, 8, 2); 
	$month = substr($date, 5, 2); 
	$year = substr($date, 0, 4); 
	$datum = $day.".".$month.".".$year;
	
	return $datum; 
}
