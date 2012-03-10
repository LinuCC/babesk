<?php
	/**
	 *@file meals.php handles all parts of the mealsmodule and combines them with the needed sourcefiles outside (like Database-functions)
	*/
    // No direct access
    defined('_AEXEC') or die("Access denied");
	
	require_once 'meals_functions.php';
	require_once 'meals_constants.php';
	require_once PATH_INCLUDE.'/meal_access.php';
	
	global $smarty;
	
	$smarty->assign('mealParent', MEAL_SMARTY_TEMPLATE_PATH.'/meals_header.tpl');
	
	//$smarty->display(MEAL_SMARTY_TEMPLATE_PATH.'/meals_header.tpl');
	
	if(isset($_GET["action"])) {
		if($_GET["action"] == 1)//show form for creating a meal
			create_meal();
		else if($_GET["action"] == 2)//show table of meals in Database
			show_meals();
		else if($_GET['action'] == 3)//show orders
			show_orders();
		else if($_GET['action'] == 4)//delete old orders
			delete_old_meals_and_orders();
		else if($_GET['action'] == 5) {//delete specific meal
// 			require_once PATH_INCLUDE.'/meal_access.php';
// 			$mealManager = new MealManager();
// 			try {
// 				$mealManager->delEntry($_GET['id']);
// 			} catch (Exception $e) {
// 				die('Could not delete meal: '.$e->getMessage());
// 			}
			try {
				delete_meal($_GET['id'], TRUE);
			} catch (Exception $e) {
				die_error($e->getMessage());
			}
			$smarty->display(MEAL_SMARTY_TEMPLATE_PATH.'/meal_deleted.tpl');
		}
		else if ($_GET['action'] == 6) {
			edit_infotext();
		}
		else if ($_GET['action'] == 7) {
			editLastOrderTime();
		}
	}
	else {//User selects what he want to do
		$smarty->display(MEAL_SMARTY_TEMPLATE_PATH.'/meals_initial_menu.tpl');
	}
?>