<?php
    //No direct access
    defined('_WEXEC') or die("Access denied");
    global $smarty;
    //global OrderManager; //etc. geht auch (glaub ich)
    
    $orderManager = new OrderManager();
    $mealManager = new MealManager('meals');
	
	$meal = array();
	$result = $orderManager->getAllOrdersOfUser($_SESSION['uid'], strtotime(date('Y-m-d')));
	$today = date('Y-m-d');
	$hour = date('H', time());
	while ($row = $result->fetch_assoc()) {
		$mealname = $mealManager->getTableData($row['MID'], 'name');
		if(!$row['fetched'] AND $row['date'] >= $today) {
		    if ($row['date'] == $today AND $hour > 8) {
                $meal[] = array('date' => formatDate($row["date"]), 'name' => $mealname["name"], 'orderID' => $row['ID'], 'cancel' => false);
            }
            else {
                $meal[] = array('date' => formatDate($row["date"]), 'name' => $mealname["name"], 'orderID' => $row['ID'], 'cancel' => true);    
            }  
        }	
	}
	$smarty->assign('meal', $meal);
	$smarty->display('web/modules/mod_menu/menu.tpl');
?>