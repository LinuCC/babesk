<?php

function new_priceclass() {
	include 'priceclass_constants.php';

	global $smarty;

	if(isset($_POST['name'], $_POST['group_id'], $_POST['price'])) {
		//form was filled
		include PATH_INCLUDE.'/group_access.php';
		include PATH_INCLUDE.'/price_class_access.php';

		$pc_name = $_POST['name'];
		$pc_group_id = $_POST['group_id'];
		$pc_price =  $_POST['price'];
		$groupManager = new GroupManager('groups');
		$groups = $groupManager->getTableData();
		$priceclassManager = new PriceClassManager();

		if(!preg_match('/\A^[0-9]{0,2}((,|\.)[0-9]{2})?\z/', $pc_price)) {
			die(ERR_INP_PRICE);
		}
		$pc_price = str_replace(',', '.', $pc_price);//Kommata bad for MySQL

		foreach($groups as $temp_group) {
			if($temp_group['ID'] == $pc_group_id) {
				$group = $temp_group;
			}
		}

		try {
			$priceclassManager->addPriceClass($pc_name, $pc_group_id, $pc_price);
		} catch (Exception $e) {
			die(ERR_ADD_PRICECLASS);
		}
		echo PRICECLASS_ADDED;
	}
	else {	//form was not filled, show it
		include PATH_INCLUDE.'/group_access.php';

		$groupManager = new GroupManager('groups');
		$groups = $groupManager->getTableData();

		if(!isset($groups)) {
			die(ERR_NO_GROUPS);
		}

		$smarty->assign('groups', $groups);
		$smarty->display(PATH_SMARTY_ADMIN_MOD.'/mod_priceclass/new_priceclass.tpl');
	}
}

function new_priceclass_all_groups() {
	require_once 'priceclass_constants.php';
	require_once PATH_INCLUDE.'/group_access.php';
	require_once PATH_INCLUDE.'/price_class_access.php';
	global $smarty;
	$groupManager = new GroupManager();
	$pcManager = new PriceClassManager();
	var_dump($_POST);
	if(isset($_POST['name'])) {
		$groups = $groupManager->getTableData();
		$pc_name = $_POST['name'];
		foreach($groups as $group) {
			$price = $_POST['group_price'.$group['ID']];
			if(!$price) {
				die('Some squirky errormessage');
			}
			if(!preg_match('/\A^[0-9]{0,2}((,|\.)[0-9]{2})?\z/', $price)) {
				die(ERR_INP_PRICE);
			}
			try {
				$pcManager->addPriceClass($pc_name, $group['ID'], $price);
			} catch (Exception $e) {
				echo ERR_ADD_PRICECLASS_FOR_GROUP.$group['name'];
			}
		}
	} else {
		$groups = $groupManager->getTableData();
		$smarty->assign('groups', $groups);
		$smarty->display(PATH_SMARTY_ADMIN_MOD.'/mod_priceclass/new_priceclass_all_groups.tpl');
	}
}

function delete_priceclass($priceclass_id) {
	include 'priceclass_constants.php';
	include PATH_INCLUDE.'/price_class_access.php';

	$priceclassManager = new PriceClassManager();
	$is_deleted = $priceclassManager->delEntry($priceclass_id);

	if(!$is_deleted) {
		die(ERR_DEL_PRICECLASS);
	}
	else {
		echo PRICECLASS_DELETED;
	}
}

function change_priceclass($priceclass_id) {
	include 'priceclass_constants.php';

	if(isset($_GET['where'],$_POST['ID'],$_POST['name'],$_POST['price'], $_POST['group_id'])) {
		include PATH_INCLUDE.'/price_class_access.php';

		$priceclassManager = new PriceClassManager();

		$pc_old_ID = $_GET['where'];
		$pc_ID = $_POST['ID'];
		$pc_name= $_POST['name'];
		$pc_price = $_POST['price'];
		$pc_GID = $_POST['group_id'];

		if(!preg_match('/\A^[0-9]{0,2}((,|\.)[0-9]{2})?\z/', $pc_price))
		die(ERR_INP_PRICE);
		else if(!preg_match('/\A^[0-9]{1,5}\z/', $pc_ID))
		die(ERR_INP_ID);
		else if(!preg_match('/\A^[0-9]{1,5}\z/', $pc_old_ID))
		die(ERR_GET);

		if($pc_old_ID == $pc_ID) {
			//only delete priceclass first if entry is already in DB
			if(!$priceclassManager->delEntry($pc_ID))
			die(ERR_DEL_PRICECLASS);
			else {
				$priceclassManager->addPriceClass($pc_name, $pc_GID, $pc_price, $pc_ID);
			}
		}
		else {//otherwise it could be a duplicated ID in MySQL, be save and DONT delete entry first
			try {
				$priceclassManager->addPriceClass($pc_name, $pc_GID, $pc_price, $pc_ID);
			} catch (Exception $e) {
				die(ERR_CHANGE_PRICECLASS.$e->getMessage());
			}
			if(!$priceclassManager->delEntry($pc_old_ID)) {
				die(ERR_DEL_PRICECLASS);
			}
		}
		echo PRICECLASS_CHANGED;
	}
	else {
		include PATH_INCLUDE.'/group_access.php';
		include PATH_INCLUDE.'/price_class_access.php';
		global $smarty;

		$priceclassManager = new PriceClassManager();
		$groupManager = new GroupManager('groups');

		$priceclass = $priceclassManager->getEntryData($priceclass_id, '*');
		$current_group_name = $groupManager->getEntryData($priceclass['GID'], 'name');
		$groups = $groupManager->getTableData();

		foreach($groups as &$group) {
			if($group['ID'] == $priceclass['GID']) {
				$group['default'] = 'selected';
			}
			else {
				$group['default'] = '';
			}
		}

		$smarty->assign('ID', $priceclass['ID']);
		$smarty->assign('name', $priceclass['name']);
		$smarty->assign('price', $priceclass['price']);
		$smarty->assign('groups', $groups);
		$smarty->assign('current_group_name', $current_group_name ['name']);
		$smarty->display(PATH_SMARTY_ADMIN_MOD.'/mod_priceclass/change_priceclass.tpl');
	}
}

function show_priceclasses() {
	include 'priceclass_constants.php';
	include PATH_INCLUDE.'/group_access.php';
	include PATH_INCLUDE.'/price_class_access.php';

	global $smarty;
	$priceclassManager = new PriceClassManager();
	$groupManager = new GroupManager('groups');

	$priceclasses = $priceclassManager->getTableData();
	foreach($priceclasses as &$priceclass) {
		$group = $groupManager->getEntryData($priceclass['GID'], 'name');
		if(!$group) {
			$priceclass['group_name'] = ERR;
		}
		else {
			$priceclass['group_name'] = $group['name'];
		}
	}

	$smarty->assign('priceclasses', $priceclasses);
	$smarty->display(PATH_SMARTY_ADMIN_MOD.'/mod_priceclass/show_priceclasses.tpl');
}

?>