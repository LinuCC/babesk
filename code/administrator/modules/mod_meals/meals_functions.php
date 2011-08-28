<?php  
	/**
	 *@file meals_functions.php some outsourced functions of the meals-module
	 **/
	   
    /**
	 *Function to show the create-meal-form and, if it is filled out,
	 *it adds the meal in the MySQL-Server
	 */
    function create_meal(){
	//---INCLUDE---
		require_once "meals_constants.php";
		require_once PATH_INCLUDE."/logs.php";
		require_once PATH_INCLUDE.'/meal_access.php';
		
		global $smarty;
		define('PATH_TEMPLATE_MEAL',MEAL_SMARTY_TEMPLATE_PATH.'/add_meal.tpl');
	//---INIT---
		$severity = NOTICE;
		$categorie = ADMIN;
		$meal_db = new MealManager();
		
	//---METHODS---
		//safety-checks
		if('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['name'], $_POST['description'], $_POST['price_class'], $_POST['max_orders'])) {
			$name = $_POST['name'];
			$description = $_POST['description'];
			$price_class = $_POST['price_class'];
			$max_orders = $_POST['max_orders'];
			(isset($_POST['is_vegetarian'])) ? $is_vegetarian = 1 : $is_vegetarian = 0;
			$date_ar = array("day" => $_POST['day'],
						"month" => $_POST['month'],
	 					"year"  => $_POST['year']);
						
			if(strlen($name) > 255) {
				echo MEAL_ERROR_NAME."<br>";
				$logger->log($categorie, $severity, "MEAL_ERROR_NAME");
				return false;
			}
			if(strlen($description) > 1000) {
				echo MEAL_ERROR_DESCRIPTION."<br>";
				$logger->log($categorie, $severity, "MEAL_ERROR_DESCRIPTION");
				return false;
			}
			if(!preg_match('/\A^[0-9]{1,6}\z/',$price_class)) {
				echo MEAL_ERROR_PRICE_CLASS."<br>";
				$logger->log($categorie, $severity, "MEAL_ERROR_PRICE_CLASS");
				return false;
			}
			if(!preg_match('/\A^[0-9]{1,}\z/',$max_orders)) {
				echo MEAL_ERROR_MAX_ORDERS."<br>";
				$logger->log($categorie, $severity, "MEAL_ERROR_MAX_ORDERS");
				return false;
			}
			if($date_ar['day'] > 31 or $date_ar['month'] > 12 or $date_ar['year'] < 2000 or $date_ar['year'] > 3000) {
				echo MEAL_ERROR_DATE.'<br>';
				$logger->log($categorie, $severity, 'MEAL_ERROR_DATE');
			}
			//convert the date for MySQL-Server
			$date_conv = $date_ar["year"]."-".$date_ar["month"]."-".$date_ar["day"];
			//and add the meal
			if($meal_db->addMeal($name, $description, $date_conv, $price_class, $max_orders, $is_vegetarian))
				echo MEAL_ADDED;
		}
		else {//if Formular isnt filled yet or the link was wrong
			price_class_init_smarty_vars();
			$smarty->display(MEAL_SMARTY_TEMPLATE_PATH.'/add_meal.tpl');
		}
	}
	
	/**
	  *Prepare Smarty-variables with the price_class to use them in add_meal.tpl
	  */
	function price_class_init_smarty_vars() {
		require_once PATH_INCLUDE.'/price_class_access.php';
		global $smarty;
		$priceclassmanager = new PriceClassManager;
		$sql_price_classes = $priceclassmanager->getAllEntries();
		$price_class_id = array();
		$price_class_name = array();
		$old_ID = -1;
		for($i = 0; isset($sql_price_classes[$i]); $i++) {
			$priceclass = $sql_price_classes[$i];
			if($priceclass['ID'] == $old_ID){//priceclasses may have multiple entries cause of GID, we need to cut them to one entry
			}
			else {
				$price_class_id[$i] = $priceclass['ID'];
				$price_class_name[$i] = $priceclass['name'];
				
				$old_ID = $priceclass['ID'];
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
		require_once PATH_INCLUDE."/logs.php";
		require_once PATH_INCLUDE."/meal_access.php";
		
		global $logger;
		$mealmanager = new MealManager;
		$meals = $mealmanager->getMeals();

		if(preg_match('/\A[0-9]{2,4}-[0-9]{2}-[0-9]{2}\z/',$search_date)) {
			$search_array = explode('-', $search_date);
			$search_timestamp = mktime(0, 0, 1, $search_array[1], $search_array[2], $search_array[0]);
			}
		else if(preg_match('/\A[0-9]{1,}\z/',$search_date))
			$search_timestamp = $search_date;
		else {
			var_dump($search_date);
			$logger->log(ADMIN,MODERATE,'MEAL_F_ERROR_DATE_FORMAT');
			die(MEAL_F_ERROR_DATE_FORMAT);
		}
		
		foreach($meals as $meal) {
			$m_timearray = explode("-", $meal["date"]);
			$m_timestamp = mktime(0, 0, 1, $m_timearray[1], $m_timearray[2], $m_timearray[0]);
			if($m_timestamp < $search_timestamp) {
				if($mealmanager->delMeal($meal['ID']))
					$logger->log(ADMIN,NOTICE,MEAL_DELETED.', name:'.$meal['name']);
				else
					$logger->log(ADMIN,MODERATE,MEAL_ERROR_DELETE.' : ID='.$meal["ID"]);
			}
		}
	}
	
	/**
	  *shows the meals
	  */
	function show_meals(){
	
		require_once PATH_INCLUDE."/meal_access.php";
		require_once "meals_constants.php";
		require_once PATH_INCLUDE."/functions.php";
		
		global $smarty;
		$mealManager = new MealManager();
		$meals = $mealManager->getMeals();
		foreach($meals as &$meal) {
			$meal['date'] = formatDate($meal['date']);
		}
		
		
		$smarty->assign('meals',$meals);
		$smarty->display(MEAL_SMARTY_TEMPLATE_PATH.'/show_meals.tpl');
	}
	
	/**
	  *sorts the orders for show_orders()
	  *first it sorts by meal, then by username / Real Name (its the same anyway)
	  */
	function sort_orders($orders) {
		//sorting by meals
		foreach($orders as $order) {
			if(empty($order)){
				echo MEAL_DATABASE_PROB_ENTRY; var_dump($order); echo MEAL_DATABASE_PROB_ENTRY_END;
				continue;}
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
	
	/**
	  *shows the orders in a table
	  */
	function show_orders() {
		require_once PATH_INCLUDE.'/order_access.php';
		require_once PATH_INCLUDE.'/meal_access.php';
		require_once PATH_INCLUDE.'/user_access.php';
		require_once PATH_INCLUDE.'/functions.php';
		require_once "meals_constants.php";
	
		global $smarty;
		
		if(!isset($_POST['ordering_day']) or !isset($_POST['ordering_month']) or !isset($_POST['ordering_year']) ) {
			$today = array('day' => date('d'),'month' => date('m'),'year' => date('Y'));
			$smarty->assign('today', $today);
			$smarty->display(MEAL_SMARTY_TEMPLATE_PATH.'/show_orders_select_date.tpl');
		}
		else {
			$order_manager = new OrderManager;
			$meal_manager = new MealManager;
			$user_manager = new UserManager;
			if($_POST['ordering_day'] > 31 or $_POST['ordering_month'] > 12 or $_POST['ordering_year'] < 2000 or $_POST['ordering_year'] > 3000) {
				die(MEAL_ERROR_DATE);
			}
			$date = $_POST['ordering_year'].'-'.$_POST['ordering_month'].'-'.$_POST['ordering_day'];
			$orders_object = $order_manager->getAllOrdersAt($date);
			$orders = array();
			$mysql_orders = array();
			$order = array();
			///@todo temporary solution, there are better ways than multiple loops
			foreach($orders_object as $order_object) {
				if (!count($meal_ID = $meal_manager->getMealData($order_object['MID'],'name')) or
					!count($user_forename = $user_manager->getUserData($order_object['UID'],'forename')) or
					!count($user_name = $user_manager->getUserData($order_object['UID'],'name'))) {
					echo MEAL_DATABASE_PROB_ENTRY;
					var_dump($order_object);
					echo MEAL_DATABASE_PROB_ENTRY_END;
				}
				else {
					$order['meal_name'] = implode($meal_ID);
					$order['user_name'] = implode($user_forename).' '.
											implode($user_name);
					$orders[] = $order;
				}
			}
			//for showing the number of orders for one meal
			$num_orders = array(array());
			$already_there = 0;
			$counter = 0;
			foreach($orders as $order) {
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
			
			if(isset($num_orders[0]) && $counter) {
				$smarty->assign('num_orders',$num_orders);
				$smarty->assign('orders',$orders);
				$smarty->assign('ordering_date',formatDate($date));
				$smarty->display(MEAL_SMARTY_TEMPLATE_PATH.'/show_orders.tpl');
			}
			else {
				die('für den '.formatDate($date).' sind keine Bestellungen vorhanden');
			}
		}
	}
	
	/**
	  *deletes the old meals and old orders, At the moment all entries which are older than yesterday
	  *will be deleted
	  */
	function delete_old_meals_and_orders() {
		require_once PATH_INCLUDE.'/meal_access.php';
		require_once PATH_INCLUDE.'/order_access.php';
		require_once 'meals_constants.php';
		
		global $smarty;
		
		if(!isset($_POST['day']) or !isset($_POST['month']) or !isset($_POST['year'])) {
		
			if($timestamp = strtotime($_POST['year'].'-'.$_POST['month'].'-'.$_POST['day']) == -1)
				die(MEAL_ERROR_DATE);
			$order_access = new OrderManager;
			remove_old_meals($timestamp);
			$order_access->RemoveOldOrders($timestamp);
		}
		else {
			$today = array('day'=>date('d'),'month'=>date('m'),'year'=>date('Y'));
			$smarty->assign('today',$today);
			$smarty->display(MEAL_SMARTY_TEMPLATE_PATH.'/delete_old_select_date.tpl');
		}
	}
?>