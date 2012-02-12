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
	
	$mealmanager = new MealManager('meals');

	$meallist = array();
	$meallist = $mealmanager->get_meals_between_two_dates(get_weekday(0), get_weekday(14));
	$weekdate = array();
	for($i = 0; $i < 12; $i++) {
	if ($i <> 5 && $i <> 6)
		$weekdate[] = date_to_european_date(get_weekday($i));
	}

	if($meallist) {
		
		$meallistweeksorted = sort_meallist($meallist);
	}
	else {
		$meallistweeksorted = NULL;
	}
	
	require_once PATH_INCLUDE.'/access.php';
	$temp = new TableManager('global_settings');	
	$infotext1 = $temp->getTableData('name="menu_text1"');
	$infotext2 = $temp->getTableData('name="menu_text2"');
	$smarty->assign('menu_text1',$infotext1[0]["value"]);
	$smarty->assign('menu_text2',$infotext2[0]["value"]);
	
	$smarty->assign('meallistweeksorted',$meallistweeksorted);
	$smarty->assign('weekdate',$weekdate);
	$smarty->display(PATH_SMARTY_ADMIN_MOD.'/mod_menu/menu_table.tpl');
?><a href="../mod_fill"></a>