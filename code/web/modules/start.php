<?php
    //No direct access
    defined('_WEXEC') or die("Access denied");
	
	$meal = array();
	$result = $orderManager->getAllOrdersOfUser($_SESSION['uid'], strtotime(date('Y-m-d')));
	while ($row = $result->fetch_assoc()) {
		$mealname = $mealManager->getTableData($row['MID'], 'name');
		$meal[] = array('date' => $row["date"], 'name' => $mealname["name"]);
	}
	$smarty->assign('meal', $meal);
	$smarty->display('web/menu.tpl');
?>