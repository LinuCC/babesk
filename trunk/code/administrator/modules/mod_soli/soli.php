<?php
	/**
	 *@file meals.php handles all parts of the mealsmodule and combines them with the needed sourcefiles outside (like Database-functions)
	*/
    // No direct access
    defined('_AEXEC') or die("Access denied");
	
	require_once 'soli_functions.php';
	require_once 'soli_constants.php';
	require_once PATH_INCLUDE.'/meal_access.php';
	
	global $smarty;
	
	$smarty->display(MEAL_SMARTY_TEMPLATE_PATH.'/meals_header.tpl');
	
	if(isset($_GET["action"])) {
		if($_GET['action'] == 1)//show orders
			show_orders();
		if($_GET['action'] == 2)//show coupons
			show_coupons();
		if($_GET['action'] == 3)//show settings
			show_settings();
		}
	else {//User selects what he want to do
		$smarty->display(MEAL_SMARTY_TEMPLATE_PATH.'/soli_initial_menu.tpl');
	}
?>