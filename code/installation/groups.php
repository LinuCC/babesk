<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set("default_charset", "utf-8");

require_once "../include/group_access.php";

if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['go_on'])) { //go on to next step of installation
	if (!isset($_POST['Name'], $_POST['Max_Credit'])) {
		die(INVALID_FORM);
	}
	///@todo use regex to test user-given arguments
	else if($_POST['Name'] != '' && $_POST['Max_Credit'] != '') {//save values and check for empty fields
		if (($groupname = trim($_POST['Name'])) == '' OR
		($max_credit = trim($_POST['Max_Credit'])) == '') {
			die(EMPTY_FORM);
		}
		if(!preg_match('/\A\d{1,5}(,.\d{2})?\z/', $max_credit)) {
			die(INVALID_FORM);
		}
		$max_credit = str_replace(',', '.', $max_credit);//Kommata bad for MySQL
		$groupManager = new GroupManager('groups');
		$groupManager->addEntry('name', $groupname, 'max_credit', $max_credit);
	}
	require "price_classes.php";
}
else if('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['add_another'])) {
	//add another group
	if (!isset($_POST['Name'], $_POST['Max_Credit'])) {
		die(INVALID_FORM);
	}  //save values and check for empty fields
	if (($groupname = trim($_POST['Name'])) == '' OR
	($max_credit = trim($_POST['Max_Credit'])) == '') {
		die(EMPTY_FORM);
	}
	$groupManager = new GroupManager('groups');
	$groupManager->addEntry('name', $groupname, 'max_credit', $max_credit);

	require "groups.tpl";
}
else {//entry-point
	require "groups.tpl";
}
?>