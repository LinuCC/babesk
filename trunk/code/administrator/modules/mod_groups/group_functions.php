<?php

/**
 * new_group() adds a new group based on some POST-Variables
 * needs the POST-Variables groupname and max_credit. ID is done by MySQL's
 * auto-incrementing id.
 *
 * @see GroupManager
 */
function new_group() {

	require_once PATH_INCLUDE . '/group_access.php';
	require_once 'group_constants.php';

	global $smarty;
	$groupManager = new GroupManager('groups');

	if (isset($_POST['groupname'], $_POST['max_credit'])) {
		$groupname = $_POST['groupname'];
		$max_credit = $_POST['max_credit'];

		if (!preg_match('/\A^[0-9]{0,2}((,|\.)[0-9]{2})?\z/', $max_credit)) {
			die(ERR_INP_MAX_CREDIT . ' ' . $max_credit);
		}
		$max_credit = str_replace(',', '.', $max_credit);

		$groupManager->addEntry('name', $groupname, 'max_credit', $max_credit);
		die_msg(sprintf('Gruppe "%s", maximales Guthaben:"%s", wurde hinzugefügt', $groupname, $max_credit));
	} else {
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

	require_once PATH_INCLUDE . '/group_access.php';
	require_once 'group_constants.php';
	global $smarty;

	$groupManager = new GroupManager('groups');
	if (!is_numeric($ID))
		die_err(ERR_INP_ID);
	try {
		$groupManager->delEntry($ID);
	} catch (Exception $e) {
		die_err(ERR_DEL_GROUP . ' :' . $e->getMessage());
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

	require_once PATH_INCLUDE . '/group_access.php';
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

	require_once PATH_INCLUDE . '/group_access.php';

	$groupManager = new GroupManager('groups');
	global $smarty;

	$groups = array();
	$groups = $groupManager->getTableData();

	$smarty->assign('groups', $groups);
	$smarty->display(PATH_SMARTY . '/templates/administrator/modules/mod_groups/show_groups.tpl');
}

?>