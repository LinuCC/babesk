<?php
error_reporting(E_ALL);

require_once '../include/price_class_access.php';
require_once '../include/group_access.php';
require_once 'installation_constants.php';

if(!_AEXEC){
	die(ERR_INP_EXEC);
}
die('This has yet to be changed');

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['group_id'],$_POST['price'],$_POST['name'])) {
	$group_id = trim($_POST['group_id']);
	$price = trim($_POST['price']);
	$name = trim($_POST['name']);
	if(!preg_match('/\A^[0-9]{0,2}((,|\.)[0-9]{2})?\z/', $price)){
		die(ERR_INP_PRICE);
	} 
	else {
		$price = str_replace(',', '.', $price);//Kommata bad for MySQL
	}
	if($group_id != '' && $price != '' && $name != '') {
		$pcManager = new PriceClassManager();
		try {
			$pcManager->addPriceClass($name, $group_id, $price);
		} catch (Exception $e) {
			die('Exception: '.$e);
		}
	}
	if(isset($_POST['add_another'])){
		global $smarty;
		$groupManager = new GroupManager('groups');
		if(!($groups = $groupManager->getTableData())) {
			die(ERR_GET_DATA_GROUP);
		}
		$smarty->assign('groups', $groups);
		$smarty->display('price_classes.tpl');
	}
	else if(isset($_POST['go_on'])) {
		require 'step5.tpl';
	}
}
else {
// 	global $smarty;
	$groupManager = new GroupManager('groups');
	if(!($groups = $groupManager->getTableData())) {
		die(ERR_GET_DATA_GROUP);
	}
	$smarty->assign('groups', $groups);
	$smarty->display('price_classes.tpl');
}
?>