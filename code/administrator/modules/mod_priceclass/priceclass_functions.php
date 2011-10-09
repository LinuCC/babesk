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
		$groupManager = new GroupManager();
		$groups = $groupManager->getGroupData();
		$priceclassManager = new PriceClassManager();
		
		if(!preg_match('/\A^[0-9]{0,2}((,|\.)[0-9]{2})?\z/', $pc_price)) {
			die(ERR_INP_PRICE);	}
		$pc_price = str_replace(',', '.', $pc_price);//Kommata bad for MySQL
		
		foreach($groups as $temp_group) {
			if($temp_group['ID'] == $pc_group_id) {
				$group = $temp_group;
			}
		}
		
		if(!$priceclassManager->addPriceClass($pc_name, $pc_group_id, $pc_price)) {
			die(ERR_ADD_PRICECLASS);
		}
		else {
			echo PRICECLASS_ADDED;
		}
		
	}
	else {	//form was not filled, show it
		include PATH_INCLUDE.'/group_access.php';
		
		$groupManager = new GroupManager();
		$groups = $groupManager->getGroupData();
		
		if(!isset($groups)) {
			die(ERR_NO_GROUPS);
		}
		
		$smarty->assign('groups', $groups);
		$smarty->display(PATH_SMARTY_ADMIN_MOD.'/mod_priceclass/new_priceclass.tpl');
	}
}

function delete_priceclass($priceclass_id) {
	include 'priceclass_constants.php';
	include PATH_INCLUDE.'/price_class_access.php';
	
	$priceclassManager = new PriceClassManager();
	$is_deleted = $priceclassManager->delPriceClass($priceclass_id);
	
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
		
		if($pc_old_ID == $pc_ID) { //only delete priceclass first if entry is already in DB
			if(!$priceclassManager->delPriceClass($pc_ID))
				die(ERR_DEL_PRICECLASS);
			else {
				$priceclassManager->addPriceClass($pc_name, $pc_GID, $pc_price, $pc_ID);
			}
		}
		else {//otherwise it could be a duplicated ID in MySQL, be save and DONT delete entry first
			if(!$priceclassManager->addPriceClass($pc_name, $pc_GID, $pc_price, $pc_ID))
				die(ERR_ADD_PRICECLASS);
			else {
				if(!$priceclassManager->delPriceClass($pc_old_ID))
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
		$groupManager = new GroupManager();
		
		$ar_priceclass = $priceclassManager->getPriceClassData($priceclass_id, '*');
		if(!count($ar_priceclass))
			die(ERR_GET_MYSQL_PRICECLASS);
		$priceclass = $ar_priceclass[0];//Theres just one priceclass
		$ar_current_group_name = $groupManager->getGroupData($priceclass['GID'], 'name');
		$current_group_name = $ar_current_group_name[0]['name'];
		$groups = $groupManager->getGroupData();
		
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
		$smarty->assign('current_group_name', $current_group_name);
		$smarty->display(PATH_SMARTY_ADMIN_MOD.'/mod_priceclass/change_priceclass.tpl');
	}
}

function show_priceclasses() {
	include 'priceclass_constants.php';
	include PATH_INCLUDE.'/group_access.php';
	include PATH_INCLUDE.'/price_class_access.php';
	
	global $smarty;
	$priceclassManager = new PriceClassManager();
	$groupManager = new GroupManager();
	
	$priceclasses = $priceclassManager->getPriceClassData();
	foreach($priceclasses as &$priceclass) {
		$group = $groupManager->getGroupData($priceclass['GID'], 'name');
		foreach($group as $group_name) {//$group is an array(array()), first array has only one entry
			$priceclass['group_name'] = $group_name['name'];
		}
	}
	
	$smarty->assign('priceclasses', $priceclasses);
	$smarty->display(PATH_SMARTY_ADMIN_MOD.'/mod_priceclass/show_priceclasses.tpl');
}

?>