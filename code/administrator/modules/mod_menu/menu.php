<?php
    /**
      *@file menu.php Module to display the meals for this week
      * SPECIAL: This Module can be used without registration via the direct link (administrator/modules/mod_menu/menu.php) 
      *@note the date has to be a date, NOT with hours, minutes or seconds, it will break some functions!
      */
	
	$from_modul = defined('PATH_INCLUDE') or require_once("../../../include/path.php");
    require_once PATH_INCLUDE."/meal_access.php";
	require_once "menu_functions.php";
	require_once PATH_SMARTY."/smarty_init.php";
	
	global $smarty;
	if(!$from_modul)$smarty->display(PATH_SMARTY.'/templates/administrator/modules/mod_menu/menu_header.tpl');
	
	$mealmanager = new MealManager;

	$meallist = array();
	$meallist = $mealmanager->get_meals_between_two_dates(get_weekday(1), get_weekday(5));
	$weekdate = array();
	for($i = 0; $i < 5; $i++)
		$weekdate[] = date_to_european_date(get_weekday($i));
	$meallist_veg = array();
	$meallist_notveg = array();
	if($meallist) {
		foreach($meallist as $meal) {
			if($meal['is_vegetarian'])
				$meallist_veg[] = $meal;
			else
				$meallist_notveg[] = $meal;
		}
		$meallistweeksorted = sort_meallist($meallist_notveg);
		$meallistweeksorted_veg = sort_meallist($meallist_veg);
	}
	else {
		$meallistweeksorted = NULL;
		$meallistweeksorted_veg = NULL;
	}
	$smarty->assign('meallistweeksorted',$meallistweeksorted);
	$smarty->assign('meallistweeksorted_veg',$meallistweeksorted_veg);
	$smarty->assign('weekdate',$weekdate);
	$smarty->display(PATH_SMARTY_ADMIN_MOD.'/mod_menu/menu_table.tpl');
?><a href="../mod_fill"></a>