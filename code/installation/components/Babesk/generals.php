<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require "../include/sql_access/dbconnect.php";
require_once '../include/functions.php';

if ('POST' == $_SERVER['REQUEST_METHOD']) {
	if (!isset($_POST['Schoolname'], $_POST['Password'])) {
		die(INVALID_FORM);
	} //save values and check for empty fields
	if (!is_array($_POST['Password']) OR count($_POST['Password']) != 2) {
		die(INVALID_FORM);
	}
	if ($_POST['Password'][0] != $_POST['Password'][1]) {
		die("Bitte selbes Passwort eingeben");
	}
	if (($schoolname = trim($_POST['Schoolname'])) == '' OR ($password = trim($_POST['Password'][0])) == '') {
		die(EMPTY_FORM);
	}

	// Create global administrator group
	$sql[0] = 'INSERT INTO
		admin_groups(name, modules)
		VALUES
		("global",
		"_ALL");';

	// Create global administrator
	$sql[1] = sql_prev_inj('INSERT INTO
			administrators(name, password, GID)
			VALUES
			("admin",
			"' . hash_password($password) . '",
			1);');

	$sql[2] = 'INSERT INTO
		global_settings(name, value)
		VALUES
		("menu_text1",
		"infotext 1");';

	$sql[3] = 'INSERT INTO
		global_settings(name, value)
		VALUES
		("menu_text2",
		"infotext 2");';

	$sql[4] = 'INSERT INTO
		global_settings(name, value)
		VALUES
		("soli_price",
		"1");';

	$sql[5] = 'INSERT INTO
		global_settings(name, value)
		VALUES
		("last_order_time",
		"09:30");';
	$sql[6] = 'INSERT INTO
		global_settings(name, value)
		VALUES
		("helptext",
		"&nbsp;");';

	foreach ($sql as $query) {
		$result = $db->query($query);
		if (!$result) {
			die(DB_QUERY_ERROR . $db->error);
		}
	}

	//next step
	require "groups.tpl";
} else {
	require "generals.tpl";
}

?>