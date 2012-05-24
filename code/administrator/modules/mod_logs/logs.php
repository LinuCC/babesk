<?php

/**
 *  @file logs.php
 *  handles the modul logs, which is an interface for the admin to get easy access to
 *  the MySQL-table
 */

//no direct access
defined('_AEXEC') or die("Access denied");

require_once 'AdminLogProcessing.php';
require_once 'AdminLogInterface.php';

$logProcessing = new AdminLogProcessing();
$logInterface = new AdminLogInterface();

//the different actions the module can do
$_chooseSev = 'choose_sev';
$_showLogs = 'show';
$_delLogs  = 'delete';


if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_GET['action'])) {
	switch($_GET['action']) {
		case $_showLogs:
			$logProcessing->ShowLogs((string) trim($_GET['Category']), (string) trim($_POST['Severity']));
			break;
		case $_chooseSev:
			$logProcessing->ChooseSeverity($_POST['Category']);
			break;
		case $_delLogs:
			$logProcessing->DeleteLogs();
			break;
	}
	
	
}
else {
	$logProcessing->ChooseCategory();
}
// 	// if(isset($_GET['action'])) {
// 	if($_GET['action'] == $_showLogs) {
// 		//show Logs
// 		if (!isset($_GET['Category'], $_POST['Severity'])) {
// 			die_error(EMPTY_FORM);
// 		}
// 		$category = (string) trim($_GET['Category']);
// 		$severity = (string) trim($_POST['Severity']);
// 		try {
// 			$logs = $logger->getTableData('category = "'.$category.'" AND severity = "'.$severity.'"');
// 		} catch (Exception $e) {
// 			die_error(ERROR_LOGS.'; Fehler:'.$e->getMessage());
// 		}
// 		$smarty->assign('logs',$logs);
// 		$smarty->display(PATH_SMARTY_ADMIN_MOD.'/mod_logs/showLogs.tpl');
// 	}
// 	else if ($_GET['action'] == $_chooseSev) {
// 		if(!isset($_POST['Category'])) {
// 			die_error(ERROR_CAT);
// 		}
// 		$severitys = array();
// 		$category = $_POST['Category'];
// 		try {
// 			$logs = $logger->getLogDataByCategory($category);
// 		} catch (Exception $e) {
// 			die_error(ERROR_LOGS.':'.$e->getMessage());
// 		}
// 		$is_existing = false;
// 		foreach($logs as $log) {
// 			foreach($severitys as $severity) {
// 				if($severity == $log['severity']) {
// 					$is_existing = true;
// 					break;
// 				}
// 			}
// 			if(!$is_existing) {
// 				$severitys[] = $log['severity'];
// 			}
// 			$is_existing = false;
// 		}

// 		$smarty->assign('severity_levels', $severitys);
// 		$smarty->assign('category', $category);
// 		$smarty->display('administrator/modules/mod_logs/chooseLogsSeverity.tpl');
// 	}
// 	else if($_GET['action'] == $_delLogs) {
// 		$logger->clearLogs();
// 	}
// 	else {
// 		$smarty->display('administrator/modules/mod_logs/logs.tpl');
// 	}
// }
// else { //show form for log-selection
// 	$logs = $logger->getLogData();
// 	$categories = array();
// 	$is_existing = false;
// 	/* add severitys and categories to display them with smarty
// 	 * This method has the advantage that unused severities or categories
// 	* cannot be choosen by the user
// 	*/
// 	foreach ($logs as $log) {
// 		foreach($categories as $category) {
// 			if($category == $log['category'])
// 			$is_existing = true;
// 		}
// 		if(!$is_existing) {
// 			$categories [] = $log['category'];
// 		}
// 		$is_existing = false;
// 	}
// 	$smarty->assign('categories', $categories);
// 	$smarty->display('administrator/modules/mod_logs/chooseLogs.tpl');
// }

?>