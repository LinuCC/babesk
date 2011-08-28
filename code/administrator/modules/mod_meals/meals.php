<?php
	/**
	 *@file meals.php handles all parts of the mealsmodule and combines them with the needed sourcefiles outside (like Database-functions)
	*/
    // No direct access
    defined('_AEXEC') or die("Access denied");
	
	require_once 'meals_functions.php';
	require_once 'meals_constants.php';
	
	global $smarty;
	
	$smarty->display(MEAL_SMARTY_TEMPLATE_PATH.'/meals_header.tpl');
	
	if(isset($_GET["action"])) {
		if($_GET["action"] == 1)//show form for creating a meal
			create_meal();
		else if($_GET["action"] == 2)//show table of meals in Database
			show_meals();
		else if($_GET['action'] == 3)//show orders
			show_orders();
		else if($_GET['action'] == 4)//delete old orders
			delete_old_meals_and_orders();
	}
	else {//User selects what he want to do
		$smarty->display(MEAL_SMARTY_TEMPLATE_PATH.'/meals_initial_menu.tpl');
	}
?>