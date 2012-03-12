<?php

/**
 * Creates a new priceclassgroup
 * This function creates a new priceclass-group by showing the user an interface to add
 * priceclasses and evaluating it.
 */
function new_priceclass() {
	require_once 'priceclass_constants.php';
	require_once PATH_INCLUDE . '/group_access.php';
	require_once PATH_INCLUDE . '/price_class_access.php';
	global $smarty;
	$groupManager = new GroupManager();
	$pcManager = new PriceClassManager();
	$priceclasses = array();
	try {
		$priceclasses = $pcManager->getTableData();
	} catch (MySQLVoidDataException $e) {
	} catch (Exception $e) {
		die_error('Error while getting PriceclassData:' . $e->getMessage());
	}

	$highest_pc_ID = 0;
	foreach ($priceclasses as $priceclass) {
		if ($priceclass['pc_ID'] > $highest_pc_ID) {
			$highest_pc_ID = $priceclass['pc_ID'];
		}
	}

	if (isset($_POST['name'], $_POST['n_price'])) {
		$groups = $groupManager->getTableData();
		$pc_name = $_POST['name'];
		$normal_price = $_POST['n_price'];
		if (!preg_match('/\A^[0-9]{1,2}((,|\.)[0-9]{2})?\z/', $normal_price)) {
			die_error(ERR_INP_N_PRICE);
		}
		foreach ($groups as $group) {
			$price = $_POST['group_price' . $group['ID']];
			if (!$price || trim($price) = '') {
				$price = $normal_price;
			} else if (!preg_match('/\A^[0-9]{0,2}((,|\.)[0-9]{2})?\z/', $price)) {
				die_error(ERR_INP_PRICE);
			}
			$price = str_replace(',', '.', $price);//Comma bad for MySQL
			try {//add the group
				$pcManager->addPriceClass($pc_name, $group['ID'], $price, $highest_pc_ID + 1);
			} catch (Exception $e) {
				die_error(sprintf('%s %s %s', ERR_ADD_PRICECLASS_FOR_GROUP, $group['name'], $e->getMessage()));
			}
		}
		die_msg(PRICECLASS_ADDED);
	} else {
		$groups = $groupManager->getTableData();
		$smarty->assign('groups', $groups);
		$smarty->display(PATH_SMARTY_ADMIN_MOD . '/mod_priceclass/new_priceclass_all_groups.tpl');
	}
}

function delete_priceclass($priceclass_id) {
	include 'priceclass_constants.php';
	include PATH_INCLUDE . '/price_class_access.php';

	$priceclassManager = new PriceClassManager();
	try {
		$priceclassManager->delEntry($priceclass_id);
	} catch (Exception $e) {
		die_error(ERR_DEL_PRICECLASS);
	}
	die_msg(PRICECLASS_DELETED);
}

function change_priceclass($priceclass_id) {
	include 'priceclass_constants.php';

	if (isset($_GET['where'], $_POST['ID'], $_POST['name'], $_POST['price'], $_POST['group_id'])) {
		include PATH_INCLUDE . '/price_class_access.php';

		$priceclassManager = new PriceClassManager();

		$pc_old_ID = $_GET['where'];
		$pc_ID = $_POST['ID'];
		$pc_name = $_POST['name'];
		$pc_price = $_POST['price'];
		$pc_GID = $_POST['group_id'];

		if (!preg_match('/\A^[0-9]{0,2}((,|\.)[0-9]{2})?\z/', $pc_price))
			die_error(ERR_INP_PRICE);
		else if (!preg_match('/\A^[0-9]{1,5}\z/', $pc_ID))
			die_error(ERR_INP_ID);
		else if (!preg_match('/\A^[0-9]{1,5}\z/', $pc_old_ID))
			die_error(ERR_GET);

		try {
			$priceclassManager->changePriceClass($pc_old_ID, $pc_name, $pc_GID, $pc_price, $pc_ID);
		} catch (Exception $e) {
			die_error(ERR_DEL_PRICECLASS . $e->getMessage());
		}
		die_msg(PRICECLASS_CHANGED);
	} else {
		include PATH_INCLUDE . '/group_access.php';
		include PATH_INCLUDE . '/price_class_access.php';
		global $smarty;

		$priceclassManager = new PriceClassManager();
		$groupManager = new GroupManager('groups');

		$priceclass = $priceclassManager->getEntryData($priceclass_id, '*');
		$current_group_name = $groupManager->getEntryData($priceclass['GID'], 'name');
		$groups = $groupManager->getTableData();

		foreach ($groups as &$group) {
			if ($group['ID'] == $priceclass['GID']) {
				$group['default'] = 'selected';
			} else {
				$group['default'] = '';
			}
		}

		$smarty->assign('ID', $priceclass['ID']);
		$smarty->assign('name', $priceclass['name']);
		$smarty->assign('price', $priceclass['price']);
		$smarty->assign('groups', $groups);
		$smarty->assign('current_group_name', $current_group_name['name']);
		$smarty->display(PATH_SMARTY_ADMIN_MOD . '/mod_priceclass/change_priceclass.tpl');
	}
}

function show_priceclasses() {
	include 'priceclass_constants.php';
	include PATH_INCLUDE . '/group_access.php';
	include PATH_INCLUDE . '/price_class_access.php';

	global $smarty;
	$priceclassManager = new PriceClassManager();
	try {
		$groupManager = new GroupManager('groups');
		$priceclasses = $priceclassManager->getTableData();
	} catch (MySQLVoidDataException $e) {
		die_error(ERR_GET_PRICECLASS_GROUPS);
	} catch (Exception $e) {
		die_error(ERR_GET_PRICECLASS_GROUPS . $e->getMessage());
	}

	foreach ($priceclasses as &$priceclass) {
		try {
			$group = $groupManager->getEntryData($priceclass['GID'], 'name');
		} catch (MySQLVoidDataException $e) {
			$priceclass['group_name'] = ERR;
		} catch (Exception $e) {
			die_error(ERR . $e->getMessage());
		}
		if (!$group) {
			$priceclass['group_name'] = ERR;
		} else {
			$priceclass['group_name'] = $group['name'];
		}
	}

	$smarty->assign('priceclasses', $priceclasses);
	$smarty->display(PATH_SMARTY_ADMIN_MOD . '/mod_priceclass/show_priceclasses.tpl');
}

?>