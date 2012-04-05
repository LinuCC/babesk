<?php

/**
 * new_group() adds a new group based on some POST-Variables
 * needs the POST-Variables groupname and max_credit. ID is done by MySQL's
 * auto-incrementing id.
 *
 * @see GroupManager
 */
function new_group() {

	require_once PATH_ACCESS . '/GroupManager.php';
	require_once PATH_ACCESS . '/PriceClassManager.php';
	require_once 'group_constants.php';

	global $smarty;
	$pcManager = new PriceClassManager();
	$groupManager = new GroupManager('groups');

	/**
	 * Add a new group to the MySQL-table
	 */
	if (isset($_POST['groupname'], $_POST['max_credit'])) {
		/**
		 * add Group
		 */
		$groupname = $_POST['groupname'];
		$max_credit = $_POST['max_credit'];

		//error-checking
		if (!isset($groupname) || $groupname == '')
			die_error(ERR_INP_GROUP_NAME, GROUP_SMARTY_PARENT);
		$groupname_is_already_existing = true;
		try {
			$groupManager->getGroupIDByName($groupname);
		} catch (MySQLVoidDataException $e) {
			$groupname_is_already_existing = false;
		}
		if ($groupname_is_already_existing)
			die_error(ERR_GROUP_EXISTING);
		if (!isset($max_credit) || $max_credit == '' || !preg_match('/\A^[0-9]{0,2}((,|\.)[0-9]{2})?\z/', $max_credit)) {
			die_error(ERR_INP_MAX_CREDIT . ' ' . $max_credit);
		}
		$max_credit = str_replace(',', '.', $max_credit);

		/**
		 * add Priceclasses belonging to the group
		 * Fetches the Priceclasses from the Server. For every Priceclass-ID there is a POST-Variable from the 
		 * create_group-form with name and ID. 
		 */
		try {
			$priceclasses = $pcManager->getTableData();
		} catch (MySQLVoidDataException $e) {
			die_msg(
					sprintf('Gruppe "%s", maximales Guthaben:"%s", wurde ohne Preisklassen hinzugefügt', $groupname,
							$max_credit));
		} catch (Exception $e) {
			die_error(ERR_FETCH_PC);
		}

		$pc_is_added = array();
		//pc_to_add_arr: If a problem happens during the adding-loop, we need to be safe that no entries are added yet
		$pc_to_add_arr = array();
		$standard_price = $_POST['n_price'];

		//check standardprice-input
		try {
			inputcheck($standard_price, 'credits', 'StandardPreis');
		} catch (Exception $e) {
			die_error(ERR_INP_PRICE . ' in:' . $e->getFieldName());
		}

		//Priceclass-routine
		foreach ($priceclasses as $priceclass) {
			foreach ($pc_is_added as $pc) {
				//is priceclass with this pc_ID already added?
				if ($priceclass['pc_ID'] == $pc)
					continue 2;
			}
			//priceclass is not added yet, add it
			try {
				$pc_id = $priceclass['pc_ID'];
				$pc_name = $_POST['pc_name' . $pc_id];
				$pc_price = $_POST['pc_price' . $pc_id];
				if (!isset($pc_price) || !$pc_price || $pc_price == '')
					$pc_price = $standard_price;
				try {
					//check for correct input of price
					inputcheck($pc_price, 'credits', $pc_name);
				} catch (WrongInputException $e) {
					die_error(ERR_INP_PRICE . ' in:' . $e->getFieldName());
				}
				try {
					//groupID will be added after the data-checking, so the next ID of MySQL's Autoincrement is the groupID
					$group_id = $groupManager->getNextAutoIncrementID();
				} catch (Exception $e) {
					throw new MySQLVoidDataException($e->getMessage() . ' in getNextAutoIncrementID');
				}
				$pc_to_add_arr[] = array('name' => $pc_name, 'gid' => $group_id, 'pc_price' => $pc_price,
						'pid' => $pc_id);
				$pc_is_added[] = $priceclass['pc_ID'];
			} catch (Exception $e) {
				///@todo replace echo with show_msg
				die_error('A Priceclass with the ID ' . $pc_id . ' could not be added: ' . $e->getMessage());
			}
		}

		/**
		 * finish adding Group and Priceclass
		 */
		try {
			$groupManager->addGroup($groupname, $max_credit);
		} catch (Exception $e) {
			die_error(ERR_ADD_GROUP);
		}
		foreach ($pc_to_add_arr as $pc_to_add) {
			try {
				$pcManager
						->addPriceClass($pc_to_add['name'], $pc_to_add['gid'], $pc_to_add['pc_price'],
										$pc_to_add['pid']);
			} catch (Exception $e) {
				die_error(ERR_ADD_PC);
			}
		}

		die_msg(sprintf('Gruppe "%s", maximales Guthaben:"%s", wurde hinzugefügt', $groupname, $max_credit),
				GROUP_SMARTY_PARENT);
	} /**
	   * Show a form to create a new group
	   */
 else {
		/**
		 * get Priceclass-information
		 */
		$pc_arr = array();

		try {
			$priceclasses = $pcManager->getTableData();
		} catch (MySQLVoidDataException $e) {
			$smarty->display(PATH_SMARTY . '/templates/administrator/modules/mod_groups/form_new_group.tpl');
			die();
		} catch (Exception $e) {
			die_error(ERR_FETCH_PC);
		}

		//there are multiple Priceclass-entries with the same pc_ID, prevent multiple entries with same name and ID
		foreach ($priceclasses as $priceclass) {
			foreach ($pc_arr as $pc) {
				if ($priceclass['pc_ID'] == $pc['ID'])
					continue 2;
			}
			$pc_arr[] = array('ID' => $priceclass['pc_ID'], 'name' => $priceclass['name']);
		}

		/**
		 * show new-group-form
		 */
		$smarty->assign('priceclasses', $pc_arr);
		$smarty->display(PATH_SMARTY . '/templates/administrator/modules/mod_groups/form_new_group.tpl');
	}
}

/**
 * delete_group deletes a group
 * The function makes use of group_access.php and deletes a group
 *
 * @see GroupManager
 *
 * @param integer/long $ID the ID of the Group to delete
 */
function delete_group($ID) {

	require_once PATH_ACCESS . '/GroupManager.php';
	require_once PATH_ACCESS . '/PriceClassManager.php';
	require_once 'group_constants.php';
	global $smarty;

	$groupManager = new GroupManager();
	$pcManager = new PriceClassManager();
	
	if (!is_numeric($ID))
		die_err(ERR_INP_ID);
	
	try {
		$groupManager->delEntry($ID);
	} catch (Exception $e) {
		die_err(ERR_DEL_GROUP . ' :' . $e->getMessage());
	}
	try {//delete priceclasses which are connected to the groups
		$priceclasses = $pcManager->getTableData(sprintf('GID = %s', $ID));
	} catch (Exception $e) {
		die_err(ERR_DEL_PC . ' :' . $e->getMessage());
	}
	foreach($priceclasses as $priceclass) {
		$pcManager->delEntry($priceclass['ID']);
	}
	
	die_msg('Die Gruppe wurde erfolgreich gelöscht');
}

/**
 * change_group lets the user change group-parameters in the MySQL
 * It shows a form, which let the user decide what to change. It then deletes the old
 * group and adds the changed new group into the table.
 * @param integer/long $ID
 *
 * @see GroupManager
 */
function change_group($ID) {

	require_once PATH_ACCESS . '/GroupManager.php';
	require_once 'group_constants.php';
	global $smarty;

	//form is filled out
	if (isset($_GET['where'], $_POST['ID'], $_POST['name'], $_POST['max_credit'])) {

		$groupManager = new GroupManager();
		$old_ID = $_GET['where'];//if group moved to new ID, delete the old one
		$ID = $_POST['ID'];
		$name = $_POST['name'];
		$max_credit = $_POST['max_credit'];

		if (!preg_match('/\A^[0-9]{0,2}((,|\.)[0-9]{2})?\z/', $max_credit))
			die_error(ERR_INP_MAX_CREDIT . ' ' . $max_credit);
		if (!is_numeric($ID))
			die_error(ERR_INP_ID);
		try {
			$groupManager->alterEntry($old_ID, 'name', $name, 'max_credit', $max_credit, 'ID', $ID);
		} catch (Exception $e) {
			die_error(ERR_CHANGE_GROUP);
		}
		die_msg('Die Gruppe wurde erfolgreich verändert.');
	} else { //show form

		$groupManager = new GroupManager('groups');
		global $smarty;
		if (!is_numeric($ID))
			die_error(ERR_INP_ID);

		try {
			$group_data = $groupManager->getEntryData($ID, 'ID', 'name', 'max_credit');
		} catch (MySQLVoidDataException $e) {
			die_error(ERR_GET_DATA_GROUP);
		}

		$smarty->assign('ID', $group_data['ID']);
		$smarty->assign('name', $group_data['name']);
		$smarty->assign('max_credit', $group_data['max_credit']);
		$smarty->display(PATH_SMARTY_ADMIN_MOD . '/mod_groups/change_group.tpl');
	}
}

/**
 * shows form with all groups listed in MySQL-table
 * makes use of group_access.php
 *
 * @see GroupManager
 */
function show_groups() {

	require_once PATH_ACCESS . '/GroupManager.php';

	$groupManager = new GroupManager('groups');
	global $smarty;

	$groups = array();
	$groups = $groupManager->getTableData();

	$smarty->assign('groups', $groups);
	$smarty->display(PATH_SMARTY . '/templates/administrator/modules/mod_groups/show_groups.tpl');
}

?>